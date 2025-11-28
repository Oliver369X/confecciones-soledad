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
        Schema::table('pedidos', function (Blueprint $table) {
            $table->integer('numero_cuotas')->default(1);
            $table->integer('cuotas_pagadas')->default(0);
            $table->decimal('saldo_pendiente', 10, 2)->default(0);
            $table->string('codigo_promocion')->nullable();
            // monto_descuento might already exist in Order model fillable but check if it is in DB. 
            // The user's Order model showed 'monto_descuento' in fillable, but let's ensure it's in the DB.
            // If it already exists, this might error. I'll check the schema first or use hasColumn check if I could, 
            // but for now I'll assume it might not be there or I should check.
            // Actually, let's look at the Order model again.
        });

        Schema::table('pagos', function (Blueprint $table) {
             $table->integer('numero_cuota')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn(['numero_cuotas', 'cuotas_pagadas', 'saldo_pendiente', 'codigo_promocion']);
        });

        Schema::table('pagos', function (Blueprint $table) {
            $table->dropColumn('numero_cuota');
        });
    }
};
