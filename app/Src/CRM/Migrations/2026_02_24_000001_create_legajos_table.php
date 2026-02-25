<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legajos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('tipo_legajo'); // vendedor, comprador
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legajos');
    }
};
