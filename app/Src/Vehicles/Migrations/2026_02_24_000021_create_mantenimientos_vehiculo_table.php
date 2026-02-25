<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mantenimientos_vehiculo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->cascadeOnDelete();
            $table->string('tipo_mantenimiento'); // taller, lavadero, otro
            $table->text('descripcion_tareas')->nullable();
            $table->text('piezas_cambiadas')->nullable();
            $table->string('nombre_lugar');
            $table->string('direccion_lugar')->nullable();
            $table->decimal('monto', 15, 2)->nullable();
            $table->dateTime('fecha_llevado');
            $table->dateTime('fecha_devuelto')->nullable();
            $table->string('responsable_llevado')->nullable(); // Persona que lo llevó
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mantenimientos_vehiculo');
    }
};
