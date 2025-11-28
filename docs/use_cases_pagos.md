# Casos de Uso: Gestión de Pedidos y Pagos

## 1. Definición de Costos y Confirmación de Pedido

### Actores
- Cliente
- Propietario (Admin)

### Flujo Principal
1.  **Solicitud:** El Cliente envía una solicitud de pedido (Estado: `PENDIENTE_PRESUPUESTO`).
2.  **Presupuesto:** El Propietario revisa la solicitud y establece el `presupuesto_total` (Costo del pedido).
    -   *Nuevo:* El sistema debe permitir ingresar este monto en la vista de administración del pedido.
3.  **Notificación:** El Cliente recibe notificación del presupuesto.
4.  **Aceptación:** El Cliente revisa el presupuesto y lo "Acepta".
    -   Estado cambia a `CONFIRMADO` (o `PENDIENTE_PAGO` si se requiere anticipo).

## 2. Aplicación de Descuentos

### Actores
- Cliente (al solicitar o al pagar)
- Propietario (al presupuestar)

### Flujo
1.  El sistema dispone de un campo "Código de Descuento".
2.  El usuario ingresa un código (ej. `VERANO2025`).
3.  El sistema valida el código contra la tabla `promociones`.
    -   Verifica vigencia y límite de uso.
4.  Si es válido, se calcula el `monto_descuento`.
5.  El `total_a_pagar` se actualiza: `presupuesto_total - monto_descuento`.

## 3. Pago en Cuotas (NUEVO)

### Descripción
Permitir dividir el pago total en múltiples cuotas (ej. 2 o 3 pagos).

### Reglas de Negocio
-   El número de cuotas se define al momento de confirmar el pedido o iniciar el pago.
-   El monto de cada cuota es: `total_a_pagar / numero_cuotas`.
-   El QR generado corresponde al monto de la **cuota actual pendiente**.

### Flujo
1.  **Selección de Plan:** Al momento de pagar, el Cliente (o el Admin al configurar) selecciona "Pago en Cuotas" y define la cantidad (ej. 2).
2.  **Cálculo:** El sistema divide el monto total.
    -   Ejemplo: Total 1000bs, 2 Cuotas. Cuota 1 = 500bs, Cuota 2 = 500bs.
3.  **Primer Pago:**
    -   Cliente selecciona "Pagar Cuota 1".
    -   Sistema genera QR por 500bs.
    -   Cliente paga.
    -   Estado del Pedido: `EN_PROCESO` (si la regla es iniciar con el primer pago).
    -   Estado de Pagos: "Cuota 1/2 Pagada".
4.  **Siguientes Pagos:**
    -   Cliente ingresa a ver su pedido.
    -   Sistema muestra "Saldo Pendiente: 500bs".
    -   Cliente selecciona "Pagar Cuota 2".
    -   Sistema genera QR por 500bs.
    -   Cliente paga.
    -   Estado de Pagos: "Completado".

## 4. Cambios Requeridos en Base de Datos

### Tabla `pedidos`
-   `numero_cuotas` (integer, default 1): Cantidad total de cuotas.
-   `cuotas_pagadas` (integer, default 0): Cantidad de cuotas ya pagadas.
-   `saldo_pendiente` (decimal): Monto restante por pagar.

### Tabla `pagos`
-   `numero_cuota` (integer): Indica qué número de cuota es este pago (ej. 1, 2).
