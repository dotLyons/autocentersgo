<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->decimal('monto_apertura', 15, 2);
            $table->decimal('monto_cierre', 15, 2)->nullable();
            $table->dateTime('fecha_apertura');
            $table->dateTime('fecha_cierre')->nullable();
            $table->string('estado')->default('abierta'); // Enum: abierta, cerrada
            $table->unsignedBigInteger('usuario_id')->nullable(); // Reference to users table later
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cajas');
    }
};
