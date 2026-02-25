<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuotas_credito_casa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('legajo_vehiculo_id')->constrained('legajo_vehiculos')->cascadeOnDelete();
            $table->integer('numero_cuota');
            $table->decimal('monto', 15, 2);
            $table->date('fecha_vencimiento');
            $table->boolean('pagada')->default(false);
            $table->date('fecha_pago')->nullable();
            $table->decimal('monto_pagado', 15, 2)->nullable();
            $table->string('metodo_pago')->nullable(); // efectivo, transferencia, tarjeta
            $table->unsignedBigInteger('cobrado_por_id')->nullable(); // User who collected
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuotas_credito_casa');
    }
};
