<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('dni')->unique();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('celular');
            $table->string('celular_referencia')->nullable();
            $table->string('email')->nullable();
            $table->string('tipo_cliente')->default('ambos'); // vendedor, comprador, ambos
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
