<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->string('marca');
            $table->string('modelo');
            $table->year('anio');
            $table->string('patente')->nullable();
            $table->string('color')->nullable();
            $table->string('tipo_vehiculo'); // auto, camioneta, furgon, moto
            
            // Moto o auto attributes
            $table->string('codigo_motor')->nullable();
            $table->string('codigo_chasis_o_marco')->nullable();
            $table->integer('puertas')->nullable();
            $table->string('tipo_caja')->nullable(); // manual, automatica
            $table->string('version')->nullable();
            $table->string('version_motor')->nullable();
            
            // Gas logic
            $table->boolean('tiene_gnc')->default(false);
            $table->integer('generacion_gnc')->nullable(); // 1 al 5
            
            // Propiedad
            $table->string('categoria_propiedad')->default('propio'); // propio, consignacion
            $table->unsignedBigInteger('vendedor_id')->nullable(); // El dueño a pagar si es consignacion
            
            // Precios
            $table->decimal('precio_venta_publico', 15, 2)->nullable();
            $table->decimal('precio_venta_consignacion', 15, 2)->nullable(); // Lo que se lleva el Vendedor
            $table->decimal('ganancia_concesionaria', 15, 2)->nullable(); // Profit
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};
