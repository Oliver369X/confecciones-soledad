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
        Schema::table('movimientos_inventario', function (Blueprint $table) {
            // Renombrar columnas
            $table->renameColumn('tipo', 'tipo_movimiento');
            $table->renameColumn('fecha', 'fecha_movimiento');
            
            // Agregar columna motivo si no existe
            if (!Schema::hasColumn('movimientos_inventario', 'motivo')) {
                $table->text('motivo')->nullable()->after('costo_unitario_ingreso');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movimientos_inventario', function (Blueprint $table) {
            // Revertir los cambios
            $table->renameColumn('tipo_movimiento', 'tipo');
            $table->renameColumn('fecha_movimiento', 'fecha');
            
            if (Schema::hasColumn('movimientos_inventario', 'motivo')) {
                $table->dropColumn('motivo');
            }
        });
    }
};
