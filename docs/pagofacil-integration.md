# Integración con PagoFácil - Documentación

Esta documentación describe cómo el sistema se integra con la API de PagoFácil para generar códigos QR de pago.

## Visión General

PagoFácil es una pasarela de pagos boliviana que permite a los clientes pagar mediante códigos QR que pueden escanear con sus aplicaciones bancarias.

**Flujo de Pago**:

1. El ayudante/propietaria genera un QR para un pedido específico
2. El sistema se autentica con PagoFácil y obtiene un token
3. Se genera el QR con los datos del pedido
4. El QR se muestra al cliente (imagen base64)
5. El cliente escanea el QR y completa el pago en su app bancaria
6. PagoFácil envía una notificación al sistema (webhook/callback)
7. El sistema actualiza el estado del pago a "PAID"

## Configuración

### Variables de Entorno

Agregar al archivo `.env`:

```env
PAGOFACIL_TOKEN_SERVICE=tu_token_service_aqui
PAGOFACIL_TOKEN_SECRET=tu_token_secret_aqui
PAGOFACIL_BASE_URL=https://masterqr.pagofacil.com.bo/api/services/v2
```

**Obtención de Credenciales**:
Contactar a PagoFácil para obtener las credenciales `tcTokenService` y `tcTokenSecret`.

## Servicio PagoFacilService

Ubicación: `app/Services/PagoFacilService.php`

### Métodos Disponibles

#### 1. `authenticate()`

Autentica con la API de PagoFácil y obtiene un access token.

**Endpoint**: `POST /login`

**Headers**:
```json
{
  "tcTokenService": "xxx",
  "tcTokenSecret": "yyy"
}
```

**Respuesta**:
```json
{
  "error": 0,
  "status": 1,
  "message": "Autenticación exitosa",
  "values": {
    "accessToken": "eyJ0eXAi...",
    "tokenType": "bearer",
    "expiresInMinutes": 789.71
  }
}
```

**Uso en el Servicio**:
```php
$token = $this->pagoFacilService->authenticate();
```

---

#### 2. `generateQr(array $data)`

Genera un código QR para una transacción.

**Endpoint**: `POST /generate-qr`

**Headers**:
```json
{
  "Authorization": "Bearer {token}"
}
```

**Body**:
```json
{
  "paymentMethod": 4,
  "clientName": "Juan Pérez",
  "documentType": 1,
  "documentId": "123456",
  "phoneNumber": "75123456",
  "email": "juan@example.com",
  "paymentNumber": "CONF-3-1642512000",
  "amount": 150.00,
  "currency": 2,
  "clientCode": "2",
  "callbackUrl": "https://tu-dominio.com/payments/callback",
  "orderDetail": [
    {
      "serial": 1,
      "product": "Pedido #3 - Arreglo de vestido",
      "quantity": 1,
      "price": 150.00,
      "discount": 0,
      "total": 150.00
    }
  ]
}
```

**Parámetros**:
- `paymentMethod`: Método de pago (4 = QR Simple)
- `documentType`: Tipo de documento (1 = CI)
- `currency`: Moneda (2 = BOB)
- `callbackUrl`: URL del webhook para notificaciones
- `orderDetail`: Array con detalles de los items

**Respuesta**:
```json
{
  "error": 0,
  "status": 2007,
  "message": "QR Generado Correctamente",
  "values": {
    "transactionId": "PF-12345",
    "paymentMethodTransactionId": "CONF-3-1642512000",
    "status": 1,
    "expirationDate": "2024-01-18 12:00:00",
    "qrBase64": "iVBORw0KGgoAAAANS...",
    "checkoutUrl": "https://...",
    "deepLink": "app://...",
    "qrContentUrl": "https://...",
    "universalUrl": "https://..."
  }
}
```

**Uso en el Controller**:
```php
$qrData = [
    'paymentMethod' => 4,
    'clientName' => $pedido->cliente->nombre_completo,
    'documentType' => 1,
    'documentId' => '000000',
    'phoneNumber' => $pedido->cliente->telefono,
    'email' => $pedido->cliente->email,
    'paymentNumber' => 'CONF-' . $pedido->pedido_id . '-' . time(),
    'amount' => (float) $saldo_pendiente,
    'currency' => 2,
    'clientCode' => (string) $pedido->cliente_id,
    'callbackUrl' => route('payments.callback'),
    'orderDetail' => [...]
];

$response = $this->pagoFacilService->generateQr($qrData);
```

---

#### 3. `consultarTransaccion($transactionId)`

Consulta el estado de una transacción.

**Endpoint**: `POST /query-transaction`

**Body**:
```json
{
  "companyTransactionId": "CONF-3-1642512000"
}
```

**Respuesta**:
```json
{
  "error": 0,
  "status": 2008,
  "message": "Consulta realizada",
  "values": {
    "pagofacilTransactionId": "PF-12345",
    "companyTransactionId": "CONF-3-1642512000",
    "paymentMethodId": 4,
    "paymentStatus": 2,
    "amount": "150.00",
    "currencyId": 2,
    "paymentDate": "2024-01-18 11:30:00",
    "paymentTime": "2024-01-18 11:30:00"
  }
}
```

**Estados de Pago**:
- `1`: Pendiente
- `2`: Pagado/Completado
- `3`: Expirado

---

## Webhook (Callback)

### Endpoint del Sistema

```
POST /payments/callback
```

**Acceso**: Público (sin middleware `auth`)

### Estructura de la Notificación

PagoFácil envía una solicitud POST cuando el pago es completado:

```json
{
  "PedidoID": "CONF-3-1642512000",
  "Fecha": "2024-01-18",
  "Hora": "11:30:00",
  "MetodoPago": "QR Simple",
  "Estado": "Completado"
}
```

### Respuesta Esperada

El sistema debe responder con HTTP 200:

```json
{
  "error": 0,
  "status": 1,
  "message": "Notificación recibida correctamente",
  "values": true
}
```

### Lógica Implementada

1. Se recibe el callback de PagoFácil
2. Se busca el pago por `company_transaction_id`
3. Se valida que el estado sea "Completado" o "PAID"
4. Se actualiza el campo `qr_status` a `'PAID'`
5. Se actualiza la `fecha_pago` al momento actual
6. Se responde con formato estándar

**Código Implementado** (`PaymentController::callback`):

```php
public function callback(Request $request)
{
    $pedidoId = $request->input('PedidoID');
    $estado = $request->input('Estado');

    $payment = Payment::where('company_transaction_id', $pedidoId)->first();

    if (!$payment) {
        return response()->json(['error' => 1, 'message' => 'Pago no encontrado'], 404);
    }

    if ($estado === 'Completado' || $estado === 'PAID') {
        $payment->update([
            'qr_status' => 'PAID',
            'fecha_pago' => now(),
        ]);
    }

    return response()->json([
        'error' => 0,
        'status' => 1,
        'message' => 'Notificación recibida correctamente',
        'values' => true
    ], 200);
}
```

---

## Base de Datos - Tabla `pagos`

Los campos relacionados con PagoFácil:

| Campo | Tipo | Descripción |
|------|------|-------------|
| `pagofacil_transaction_id` | string | ID de transacción de PagoFácil (ej. PF-12345) |
| `company_transaction_id` | string | ID de transacción generado por el sistema (ej. CONF-3-1642512000) |
| `qr_base64` | text | Imagen del QR en base64 |
| `qr_status` | string | Estado del QR (PENDING, PAID, EXPIRED) |
| `qr_expiration` | timestamp | Fecha y hora de expiración del QR |

---

## Frontend - Mostrar el QR

En `resources/js/Pages/Payments/Index.vue`:

```vue
<template>
  <div v-if="qrData" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
    <h4 class="font-bold text-green-800 mb-3">✓ QR Generado Exitosamente</h4>
    <div class="flex items-start gap-4">
      <div class="flex-shrink-0">
        <img :src="`data:image/png;base64,${qrData.qrBase64}`" 
             alt="QR Code" 
             class="w-48 h-48 border-2 border-gray-300 rounded" />
      </div>
      <div class="flex-1">
        <p><strong>ID Transacción:</strong> {{ qrData.transactionId }}</p>
        <p><strong>Expira:</strong> {{ new Date(qrData.expirationDate).toLocaleString() }}</p>
        <p class="text-sm text-gray-500">
          El cliente debe escanear este código QR con su app bancaria para realizar el pago.
        </p>
      </div>
    </div>
  </div>
</template>
```

---

## Testing

Para testear la integración sin realizar pagos reales, PagoFácil cuenta con un ambiente de pruebas (sandbox).

**Nota**: Se recomienda usar las credenciales de prueba proporcionadas por PagoFácil para testing.

---

## Errores Comunes

### 1. Error de Autenticación

```json
{
  "error": 1,
  "message": "Token inválido"
}
```

**Solución**: Verificar que las credenciales en `.env` sean correctas.

### 2. QR Expirado

Si el cliente tarda mucho en pagar, el QR puede expirar (típicamente 15-30 minutos).

**Solución**: Generar un nuevo QR.

### 3. Callback no recibido

Si el callback no se recibe, verificar:
- La URL del callback es accesible públicamente (no `localhost`)
- El firewall/servidor permite solicitudes POST al endpoint
- El endpoint `/payments/callback` está fuera del middleware `auth`

---

## Recursos Adicionales

- **Documentación oficial**: [metododepago.txt](../metododepago.txt)
- **Postman Collection**: [MasterQR.postman_collection.json](../Material Generar Qr/...)
- **Soporte PagoFácil**: atencion.cliente@pagofacil.com.bo | (+591) 75353593

---

**Última actualización**: 2024-01-18
