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
        Schema::table('legajo_vehiculos', function (Blueprint $table) {
            $table->boolean('entregado')->default(false)->after('retirado_ahora');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('legajo_vehiculos', function (Blueprint $table) {
            $table->dropColumn('entregado');
        });
    }
};
