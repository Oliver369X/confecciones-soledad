# Documentación de API - Endpoints

Esta documentación describe todos los endpoints REST disponibles en el sistema.

## Autenticación

Todos los endpoints (excepto `/login` y `/register`) requieren autenticación mediante sesiones de Laravel (cookies HTTP-only).

**Middleware aplicado**: `auth`

---

## 📁 Casos de Uso

## CU1: Gestión de Usuarios

### Listar Usuarios
```http
GET /users
```

**Respuesta exitosa (200)**:
```json
{
  "users": [
    {
      "usuario_id": 1,
      "nombre_completo": "María González",
      "email": "maria@example.com",
      "rol": "PROPIETARIO",
      "telefono": "75123456",
      "created_at": "2024-01-15T10:30:00.000000Z"
    }
  ]
}
```

### Crear Usuario
```http
POST /users
Content-Type: application/json
```

**Body**:
```json
{
  "nombre_completo": "Juan Pérez",
  "email": "juan@example.com",
  "password": "password123",
  "rol": "CLIENTE",
  "telefono": "75987654"
}
```

**Validaciones**:
- `nombre_completo`: required, string, max:255
- `email`: required, email, unique:users
- `password`: required, min:8
- `rol`: required, in:PROPIETARIO,AYUDANTE,CLIENTE
- `telefono`: nullable, string

**Respuesta exitosa (302)**: Redirect a `/users`

### Actualizar Usuario
```http
PUT /users/{id}
Content-Type: application/json
```

**Body** (similar a crear, password opcional)

### Eliminar Usuario
```http
DELETE /users/{id}
```

---

## CU2: Gestión de Portafolio

### Listar Items del Portafolio
```http
GET /portfolio
```

**Respuesta exitosa (200)**:
```json
{
  "items": [
    {
      "portafolio_id": 1,
      "titulo": "Arreglo de Vestido de Novia",
      "descripcion": "Ajuste y modificaciones",
      "imagen_url_principal": "/storage/portfolio/img1.jpg",
      "imagen_url_antes": "/storage/portfolio/antes1.jpg",
      "imagen_url_despues": "/storage/portfolio/despues1.jpg",
      "publicado": true
    }
  ]
}
```

### Crear Item
```http
POST /portfolio
Content-Type: application/json
```

**Body**:
```json
{
  "titulo": "Confección de Traje",
  "descripcion": "Traje a medida",
  "imagen_url_principal": "https://example.com/img.jpg",
  "imagen_url_antes": null,
  "imagen_url_despues": null,
  "publicado": true
}
```

---

## CU3: Gestión de Pedidos

### Listar Pedidos
```http
GET /orders
```

**Filtrado automático por rol**:
- PROPIETARIO/AYUDANTE: Ve todos los pedidos
- CLIENTE: Solo ve sus propios pedidos

**Respuesta exitosa (200)**:
```json
{
  "orders": [
    {
      "pedido_id": 1,
      "cliente_id": 2,
      "tipo_servicio": "ARREGLO",
      "descripcion_prenda": "Ajuste de pantalón",
      "estado": "EN_PROCESO",
      "presupuesto_total": 50.00,
      "monto_descuento": 5.00,
      "cliente": {
        "nombre_completo": "Juan Pérez"
      }
    }
  ]
}
```

### Crear Pedido
```http
POST /orders
Content-Type: application/json
```

**Body**:
```json
{
  "cliente_id": 2,
  "tipo_servicio": "CONFECCION",
  "descripcion_prenda": "Vestido de fiesta",
  "presupuesto_total": 200.00,
  "promocion_id": null
}
```

**Validaciones**:
- `cliente_id`: required, exists:users,usuario_id
- `tipo_servicio`: required, in:ARREGLO,CONFECCION
- `descripcion_prenda`: required, string
- `presupuesto_total`: required, numeric, min:0

### Ver Detalle de Pedido
```http
GET /orders/{id}
```

### Actualizar Estado de Pedido
```http
PUT /orders/{id}
Content-Type: application/json
```

**Body**:
```json
{
  "estado": "ENTREGADO",
  "fecha_entrega_estimada": "2024-02-15"
}
```

---

## CU4: Gestión de Inventario

### Listar Items de Inventario
```http
GET /inventory
```

**Respuesta exitosa (200)**:
```json
{
  "items": [
    {
      "item_id": 1,
      "nombre_material": "Tela Algodón",
      "descripcion": "Tela blanca",
      "cantidad_stock": 100.50,
      "unidad_medida": "Metros",
      "costo_unitario": 15.00
    }
  ]
}
```

### Registrar Nuevo Material
```http
POST /inventory
Content-Type: application/json
```

**Body**:
```json
{
  "nombre_material": "Hilo Negro",
  "descripcion": "Carrete de 100m",
  "cantidad_stock": 50,
  "unidad_medida": "Unidades",
  "costo_unitario": 5.50
}
```

### Registrar Movimiento de Inventario
```http
POST /inventory/{id}/movement
Content-Type: application/json
```

**Body**:
```json
{
  "tipo_movimiento": "SALIDA",
  "cantidad": 10,
  "motivo": "Uso en pedido #123",
  "pedido_id": 123
}
```

**Validaciones**:
- `tipo_movimiento`: required, in:ENTRADA,SALIDA
- `cantidad`: required, numeric, min:0.01
- `motivo`: nullable, string
- `pedido_id`: nullable, exists:pedidos,pedido_id

**Lógica**:
- ENTRADA: Incrementa stock
- SALIDA: Decrementa stock (valida stock suficiente)

---

## CU5: Gestión de Promociones

### Listar Promociones
```http
GET /promotions
```

### Crear Promoción
```http
POST /promotions
Content-Type: application/json
```

**Body**:
```json
{
  "codigo": "VERANO2024",
  "descripcion": "Descuento de verano",
  "tipo_descuento": "PORCENTAJE",
  "valor_descuento": 15,
  "fecha_inicio": "2024-01-01",
  "fecha_fin": "2024-03-31",
  "activa": true
}
```

**Validaciones**:
- `codigo`: required, unique, max:50
- `tipo_descuento`: required, in:PORCENTAJE,MONTO_FIJO
- `valor_descuento`: required, numeric, min:0
- `fecha_inicio`: required, date
- `fecha_fin`: required, date, after_or_equal:fecha_inicio

---

## CU6: Gestión de Reseñas

### Listar Reseñas
```http
GET /reviews
```

**Acceso**: Público (sin autenticación) o autenticado

**Respuesta exitosa (200)**:
```json
{
  "reviews": [
    {
      "resena_id": 1,
      "pedido_id": 5,
      "calificacion": 5,
      "comentario": "Excelente trabajo!",
      "fecha_resena": "2024-01-20T14:30:00.000000Z",
      "cliente": {
        "nombre_completo": "Juan Pérez"
      }
    }
  ]
}
```

### Crear Reseña
```http
POST /reviews
Content-Type: application/json
```

**Body**:
```json
{
  "pedido_id": 5,
  "calificacion": 5,
  "comentario": "Muy buen servicio, recomendado"
}
```

**Validaciones**:
- `pedido_id`: required, exists:pedidos, unique (una reseña por pedido)
- `calificacion`: required, integer, min:1, max:5
- `comentario`: nullable, string

**Restricciones**:
- El pedido debe pertenecer al cliente autenticado
- El pedido debe estar en estado `ENTREGADO`
- Solo puede haber una reseña por pedido

---

## CU7: Gestión de Pagos

### Listar Pagos
```http
GET /payments
```

**Respuesta exitosa (200)**:
```json
{
  "payments": [
    {
      "pago_id": 1,
      "pedido_id": 3,
      "monto": 50.00,
      "metodo_pago": "EFECTIVO",
      "qr_status": null,
      "fecha_pago": "2024-01-18T10:00:00.000000Z",
      "pedido": {
        "cliente": {
          "nombre_completo": "María López"
        }
      }
    }
  ]
}
```

### Registrar Pago Manual
```http
POST /payments
Content-Type: application/json
```

**Body**:
```json
{
  "pedido_id": 3,
  "monto": 100.00,
  "metodo_pago": "EFECTIVO",
  "comprobante_url": null
}
```

**Validaciones**:
- `pedido_id`: required, exists:pedidos
- `monto`: required, numeric, min:0.01
- `metodo_pago`: required, in:EFECTIVO,TRANSFERENCIA,QR
- `comprobante_url`: nullable, string

**Lógica**:
- Valida que el monto no exceda el saldo pendiente del pedido

### Generar QR para Pago (PagoFácil)
```http
POST /payments/generate-qr
Content-Type: application/json
```

**Body**:
```json
{
  "pedido_id": 3
}
```

**Respuesta exitosa (302)**: Redirect con mensaje de éxito y `qr_data` en sesión

**Estructura de `qr_data`**:
```json
{
  "transactionId": "12345",
  "qrBase64": "iVBORw0KGgoAAAANS...",
  "expirationDate": "2024-01-18T12:00:00",
  "qrContentUrl": "https://..."
}
```

**Lógica**:
1. Calcula el saldo pendiente del pedido
2. Llama a `PagoFacilService::generateQr()`
3. Crea un registro de pago con `qr_status = 'PENDING'`
4. Retorna el QR en base64

### Webhook PagoFácil (Callback)
```http
POST /payments/callback
Content-Type: application/json
```

**Body** (enviado por PagoFácil):
```json
{
  "PedidoID": "CONF-3-1234567890",
  "Fecha": "2024-01-18",
  "Hora": "11:30:00",
  "MetodoPago": "QR Simple",
  "Estado": "Completado"
}
```

**Respuesta esperada (200)**:
```json
{
  "error": 0,
  "status": 1,
  "message": "Notificación recibida correctamente",
  "values": true
}
```

**Lógica**:
- Busca el pago por `company_transaction_id`
- Si el estado es "Completado" o "PAID", actualiza `qr_status = 'PAID'`

---

## CU8: Reportes y Estadísticas

### Ver Reporte de Rentabilidad
```http
GET /reports?start_date=2024-01-01&end_date=2024-01-31
```

**Parámetros de Query**:
- `start_date`: Fecha de inicio (formato: Y-m-d)
- `end_date`: Fecha de fin (formato: Y-m-d)

**Respuesta exitosa (200)**:
```json
{
  "filters": {
    "start_date": "2024-01-01",
    "end_date": "2024-01-31"
  },
  "stats": {
    "ingresos": 5000.00,
    "costos": 2000.00,
    "rentabilidad": 3000.00
  }
}
```

**Cálculos**:
- **Ingresos**: Suma de `pagos.monto` donde `fecha_pago` está en el rango
- **Costos**: Suma de `(movimientos_inventario.cantidad * inventario_items.costo_unitario)` donde `tipo_movimiento = 'SALIDA'` y `fecha_movimiento` está en el rango
- **Rentabilidad**: `ingresos - costos`

---

## Códigos de Respuesta HTTP

- **200 OK**: Solicitud exitosa (GET)
- **302 Found**: Redirect después de operación exitosa (POST/PUT/DELETE)
- **401 Unauthorized**: No autenticado
- **403 Forbidden**: Sin permisos
- **404 Not Found**: Recurso no encontrado
- **422 Unprocessable Entity**: Errores de validación
- **500 Internal Server Error**: Error del servidor

## Manejo de Errores

**Formato de error de validación (422)**:
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": [
      "The email has already been taken."
    ],
    "password": [
      "The password must be at least 8 characters."
    ]
  }
}
```

---

**Nota**: Esta API utiliza Inertia.js, por lo que las respuestas pueden ser HTML (para navegadores) o JSON (para solicitudes XHR). Los ejemplos anteriores muestran la estructura de datos que se pasa a las vistas Vue.
