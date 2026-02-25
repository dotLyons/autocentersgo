<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_pago_tarjetas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tarjeta_id')->constrained('tarjetas')->cascadeOnDelete();
            $table->integer('cuotas'); // 1 para debito o sin interes, >1 para cuotas
            $table->decimal('interes', 8, 2)->default(0); // Porcentaje de interes Ej: 15.5 para 15.5%
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_pago_tarjetas');
    }
};
