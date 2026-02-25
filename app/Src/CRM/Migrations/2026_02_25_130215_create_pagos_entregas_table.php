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
        Schema::create('pagos_entregas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('legajo_vehiculo_id')->constrained('legajo_vehiculos')->onDelete('cascade');
            $table->decimal('monto', 15, 2);
            $table->date('fecha_pago');
            $table->string('metodo_pago')->nullable(); // efectivo, transferencia, etc.
            $table->unsignedBigInteger('movimiento_caja_id')->nullable(); // Opcional, si se enlaza a un mov. de caja
            $table->string('registrado_por')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos_entregas');
    }
};
