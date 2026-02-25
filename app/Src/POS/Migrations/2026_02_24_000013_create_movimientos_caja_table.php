<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja_id')->constrained('cajas')->cascadeOnDelete();
            $table->string('tipo_movimiento'); // ingreso, egreso
            $table->decimal('monto', 15, 2);
            $table->string('descripcion')->nullable();
            
            $table->string('metodo_pago'); // efectivo, transferencia, tarjeta

            // Datos si es tarjeta
            $table->foreignId('tarjeta_id')->nullable()->constrained('tarjetas')->nullOnDelete();
            $table->foreignId('plan_pago_tarjeta_id')->nullable()->constrained('plan_pago_tarjetas')->nullOnDelete();
            
            // Datos si es transferencia u otro
            $table->string('referencia_transferencia')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_caja');
    }
};
