<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // CU1: Tabla de Usuarios
        Schema::create('users', function (Blueprint $table) {
            $table->id('usuario_id'); // Primary Key
            $table->string('nombre_completo');
            $table->string('email')->unique();
            $table->string('password')->nullable(); // Nullable for SSO
            $table->string('rol', 20); // PROPIETARIO, AYUDANTE, CLIENTE
            $table->string('sso_provider', 50)->nullable();
            $table->string('sso_id')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->timestamp('email_verified_at')->nullable(); // Laravel default
            $table->rememberToken(); // Laravel default
            $table->timestamps(); // created_at (fecha_registro), updated_at
        });

        // CU5: Tabla de Promociones
        Schema::create('promociones', function (Blueprint $table) {
            $table->id('promocion_id');
            $table->string('codigo', 50)->unique();
            $table->text('descripcion')->nullable();
            $table->string('tipo_descuento', 20); // PORCENTAJE, MONTO_FIJO
            $table->decimal('valor_descuento', 10, 2);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });

        // CU3: Tabla de Pedidos
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id('pedido_id');
            $table->foreignId('cliente_id')->constrained('users', 'usuario_id');
            $table->foreignId('ayudante_id')->nullable()->constrained('users', 'usuario_id');
            $table->string('tipo_servicio', 20); // ARREGLO, CONFECCION
            $table->text('descripcion_prenda');
            $table->string('estado', 30)->default('PENDIENTE_PRESUPUESTO');
            $table->timestamp('fecha_solicitud')->useCurrent();
            $table->date('fecha_entrega_estimada')->nullable();
            $table->timestamp('fecha_entregado')->nullable();
            $table->decimal('presupuesto_total', 10, 2);
            $table->decimal('costo_materiales_total', 10, 2)->default(0.00);
            $table->foreignId('promocion_id')->nullable()->constrained('promociones', 'promocion_id');
            $table->decimal('monto_descuento', 10, 2)->default(0.00);
            $table->timestamps();
        });

        // CU4: Tabla de Ítems de Inventario
        Schema::create('inventario_items', function (Blueprint $table) {
            $table->id('item_id');
            $table->string('nombre');
            $table->decimal('stock_actual', 10, 2)->default(0.00);
            $table->string('unidad_medida', 20);
            $table->decimal('costo_promedio_ponderado', 10, 2)->default(0.00);
            $table->timestamps();
        });

        // CU4: Tabla de Movimientos de Inventario
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id('movimiento_id');
            $table->foreignId('item_id')->constrained('inventario_items', 'item_id');
            $table->foreignId('pedido_id')->nullable()->constrained('pedidos', 'pedido_id');
            $table->string('tipo', 20); // INGRESO, SALIDA_PEDIDO, AJUSTE
            $table->decimal('cantidad', 10, 2);
            $table->decimal('costo_unitario_ingreso', 10, 2)->nullable();
            $table->timestamp('fecha')->useCurrent();
            $table->foreignId('usuario_id')->nullable()->constrained('users', 'usuario_id');
            $table->timestamps();
        });

        // CU7: Tabla de Pagos
        Schema::create('pagos', function (Blueprint $table) {
            $table->id('pago_id');
            $table->foreignId('pedido_id')->constrained('pedidos', 'pedido_id')->onDelete('cascade');
            $table->decimal('monto', 10, 2);
            $table->string('metodo_pago', 50); // EFECTIVO, TRANSFERENCIA, QR
            $table->timestamp('fecha_pago')->useCurrent();
            $table->foreignId('confirmado_por_id')->nullable()->constrained('users', 'usuario_id');
            
            // Campos para integración con PagoFácil QR
            $table->string('pagofacil_transaction_id')->nullable()->unique(); // ID de transacción de PagoFácil
            $table->string('company_transaction_id')->nullable(); // ID de transacción de la empresa
            $table->text('qr_base64')->nullable(); // QR en base64
            $table->string('qr_status')->nullable(); // Estado del QR (PENDING, PAID, EXPIRED)
            $table->timestamp('qr_expiration')->nullable(); // Fecha de expiración del QR
            $table->string('comprobante_url')->nullable();
            
            $table->timestamps();
        });

        // CU6: Tabla de Reseñas
        Schema::create('resenas', function (Blueprint $table) {
            $table->id('resena_id');
            $table->foreignId('pedido_id')->unique()->constrained('pedidos', 'pedido_id');
            $table->foreignId('cliente_id')->constrained('users', 'usuario_id');
            $table->integer('calificacion');
            $table->text('comentario')->nullable();
            $table->timestamp('fecha')->useCurrent();
            $table->boolean('publicada')->default(false);
            $table->timestamps();
        });

        // CU2: Tabla de Portafolio
        Schema::create('portafolio_items', function (Blueprint $table) {
            $table->id('portafolio_id');
            $table->foreignId('pedido_id')->nullable()->constrained('pedidos', 'pedido_id');
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('imagen_url_principal');
            $table->string('imagen_url_antes')->nullable();
            $table->string('imagen_url_despues')->nullable();
            $table->boolean('publicado')->default(true);
            $table->timestamps();
        });
        
        // Password Reset Tokens (Standard Laravel)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Sessions (Standard Laravel)
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('portafolio_items');
        Schema::dropIfExists('resenas');
        Schema::dropIfExists('pagos');
        Schema::dropIfExists('movimientos_inventario');
        Schema::dropIfExists('inventario_items');
        Schema::dropIfExists('pedidos');
        Schema::dropIfExists('promociones');
        Schema::dropIfExists('users');
    }
};
