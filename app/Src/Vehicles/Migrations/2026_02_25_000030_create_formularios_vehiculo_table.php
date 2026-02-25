<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formularios_vehiculo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->cascadeOnDelete();
            $table->string('tipo_formulario'); // Enum: Formulario 02, 04, 08, 12, Libre de Deuda
            $table->boolean('presentado')->default(false);
            $table->string('archivo_path')->nullable(); // Para guardar PDFs o imágenes
            $table->date('fecha_presentacion')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formularios_vehiculo');
    }
};
