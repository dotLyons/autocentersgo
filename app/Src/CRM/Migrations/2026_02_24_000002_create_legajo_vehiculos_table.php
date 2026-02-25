<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legajo_vehiculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('legajo_id')->constrained('legajos')->cascadeOnDelete();
            $table->unsignedBigInteger('vehiculo_id')->nullable(); 

            // Vendedor specific
            $table->decimal('precio_acordado', 15, 2)->nullable();
            $table->decimal('cargo_concesionaria', 15, 2)->nullable();
            $table->boolean('esta_vendido')->default(false);
            $table->string('metodo_pago_venta')->nullable();
            
            // Comprador specific
            $table->decimal('precio_compra', 15, 2)->nullable();
            $table->decimal('monto_efectivo', 15, 2)->nullable();
            $table->decimal('monto_transferencia', 15, 2)->nullable();
            $table->decimal('monto_entrega', 15, 2)->nullable();
            $table->unsignedBigInteger('vehiculo_entregado_id')->nullable(); // FK to vehiculos delayed or handled manually due to contexts
            $table->decimal('valor_vehiculo_entregado', 15, 2)->nullable();
            $table->decimal('financiacion_banco', 15, 2)->nullable();
            $table->decimal('financiacion_casa', 15, 2)->nullable();
            $table->integer('cant_cuotas_casa')->nullable();
            $table->decimal('monto_cuota_casa', 15, 2)->nullable();
            $table->decimal('total_pagado_casa', 15, 2)->nullable();
            
            $table->boolean('retirado_ahora')->default(false);
            $table->boolean('transferencia_a_cargo_comprador')->default(false);
            $table->decimal('costo_transferencia', 15, 2)->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legajo_vehiculos');
    }
};
