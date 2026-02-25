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
            $table->decimal('saldo_entrega_pendiente', 15, 2)->default(0)->after('monto_entrega');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('legajo_vehiculos', function (Blueprint $table) {
            $table->dropColumn('saldo_entrega_pendiente');
        });
    }
};
