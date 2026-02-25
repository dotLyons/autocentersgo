<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vehiculo;
use App\Models\Cliente;
use App\Models\Venta;
use App\Models\Cobro;
use App\Models\CajaMovimiento;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. USUARIOS
        // Creamos usuarios con contraseña encriptada
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@demo.com',
            'password' => Hash::make('password'),
            // Si usas Spatie o el campo role manual, descomenta esto:
            // 'role' => 'admin',
        ]);

        $cobrador = User::create([
            'name' => 'Juan Cobrador',
            'email' => 'cobrador@demo.com',
            'password' => Hash::make('password'),
            // 'role' => 'collector',
        ]);
    }
}
