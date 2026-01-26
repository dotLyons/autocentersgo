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

        // 2. VEHÍCULOS
        // Usamos variables para relacionarlos fácilmente después
        $toyota = Vehiculo::create([
            'brand' => 'Toyota',
            'model' => 'Hilux SRX',
            'patent' => 'AE123CD',
            'model_year' => 2023,
            'color' => 'Blanco',
            'price' => 45000000.00,
            'details' => 'Impecable, única mano.',
            'is_available' => true
        ]);

        $ford = Vehiculo::create([
            'brand' => 'Ford',
            'model' => 'Ranger Raptor',
            'patent' => 'AF555GH',
            'model_year' => 2024,
            'color' => 'Azul Performance',
            'price' => 62000000.00,
            'details' => '0km lista para transferir.',
            'is_available' => true
        ]);

        $fiat = Vehiculo::create([
            'brand' => 'Fiat',
            'model' => 'Cronos Precision',
            'patent' => 'AD999ZZ',
            'model_year' => 2022,
            'color' => 'Rojo',
            'price' => 18500000.00,
            'details' => 'Ideal para Uber/Taxi.',
            'is_available' => false // Vendido
        ]);

        $vw = Vehiculo::create([
            'brand' => 'Volkswagen',
            'model' => 'Amarok V6',
            'patent' => 'AC111BB',
            'model_year' => 2021,
            'color' => 'Gris Plata',
            'price' => 38000000.00,
            'details' => 'Service oficiales.',
            'is_available' => false // Vendido
        ]);

        $peugeot = Vehiculo::create([
            'brand' => 'Peugeot',
            'model' => '208 Feline',
            'patent' => 'AF001AA',
            'model_year' => 2024,
            'color' => 'Negro',
            'price' => 22000000.00,
            'details' => 'Techo panorámico.',
            'is_available' => false // Vendido
        ]);

        $chevrolet = Vehiculo::create([
            'brand' => 'Chevrolet',
            'model' => 'Cruze RS',
            'patent' => 'AE888JK',
            'model_year' => 2023,
            'color' => 'Blanco',
            'price' => 24500000.00,
            'details' => 'Automático.',
            'is_available' => true
        ]);

        // 3. CLIENTES
        $carlos = Cliente::create([
            'dni' => '20123456',
            'nombre' => 'Carlos',
            'apellido' => 'Gomez',
            'telefono' => '3855123456',
            'direccion' => 'Av. Belgrano 1234, Santiago del Estero',
            'is_active' => true
        ]);

        $maria = Cliente::create([
            'dni' => '25987654',
            'nombre' => 'Maria',
            'apellido' => 'Rodriguez',
            'telefono' => '3855987654',
            'direccion' => 'Calle Independencia 500, La Banda',
            'is_active' => true
        ]);

        $jorge = Cliente::create([
            'dni' => '30112233',
            'nombre' => 'Jorge',
            'apellido' => 'Martinez',
            'telefono' => '3854112233',
            'direccion' => 'Barrio Cabildo, Santiago del Estero',
            'is_active' => true
        ]);

        // 4. VENTAS (Casos de prueba para el Cobrador)

        // Venta 1: AL DÍA (Vencimiento en 15 días)
        Venta::create([
            'cliente_id' => $carlos->id,
            'vehiculo_id' => $fiat->id,
            'precio_venta' => 18500000.00,
            'cantidad_cuotas' => 24,
            'monto_cuota' => 770000.00,
            'observaciones' => 'Plan adjudicado.',
            'fecha_cobro' => Carbon::now()->addDays(15),
        ]);

        // Venta 2: VENCE HOY (Debe aparecer en "Cobrar Hoy")
        Venta::create([
            'cliente_id' => $maria->id,
            'vehiculo_id' => $vw->id,
            'precio_venta' => 38000000.00,
            'cantidad_cuotas' => 36,
            'monto_cuota' => 1055000.00,
            'observaciones' => 'Cliente prefiere pagar por la tarde.',
            'fecha_cobro' => Carbon::today(),
        ]);

        // Venta 3: VENCIDO (Debe aparecer en rojo hace 5 días)
        Venta::create([
            'cliente_id' => $jorge->id,
            'vehiculo_id' => $peugeot->id,
            'precio_venta' => 22000000.00,
            'cantidad_cuotas' => 12,
            'monto_cuota' => 1833000.00,
            'observaciones' => 'Llamar urgente, se atrasó.',
            'fecha_cobro' => Carbon::now()->subDays(5),
        ]);

        // 5. HISTORIAL DE CAJA Y COBROS (Simulación de pasado)

        // --- Caso Histórico A: Cobro hace 1 mes (Efectivo) ---
        $fechaCobro1 = Carbon::now()->subMonth();

        $cobro1 = Cobro::create([
            'cliente_id' => $carlos->id,
            'user_id' => $admin->id,
            'monto_total' => 770000.00,
            'observaciones' => 'Pago cuota 1/24',
            'created_at' => $fechaCobro1,
            'updated_at' => $fechaCobro1,
        ]);

        $cobro1->detalles()->create([
            'metodo_pago' => 'efectivo',
            'monto' => 770000.00,
            'created_at' => $fechaCobro1,
            'updated_at' => $fechaCobro1,
        ]);

        CajaMovimiento::create([
            'user_id' => $admin->id,
            'tipo' => 'ingreso',
            'concepto' => 'Cobro a cliente: Gomez Carlos',
            'monto' => 770000.00,
            'origen_type' => Cobro::class,
            'origen_id' => $cobro1->id,
            'created_at' => $fechaCobro1,
            'updated_at' => $fechaCobro1,
        ]);

        // --- Caso Histórico B: Cobro hace 20 días (Mixto) ---
        $fechaCobro2 = Carbon::now()->subDays(20);

        $cobro2 = Cobro::create([
            'cliente_id' => $maria->id,
            'user_id' => $cobrador->id,
            'monto_total' => 500000.00,
            'observaciones' => 'Entrega a cuenta',
            'created_at' => $fechaCobro2,
            'updated_at' => $fechaCobro2,
        ]);

        $cobro2->detalles()->createMany([
            [
                'metodo_pago' => 'efectivo',
                'monto' => 200000.00,
                'created_at' => $fechaCobro2,
                'updated_at' => $fechaCobro2
            ],
            [
                'metodo_pago' => 'transferencia',
                'monto' => 300000.00,
                'referencia' => 'TRF-99887711',
                'created_at' => $fechaCobro2,
                'updated_at' => $fechaCobro2
            ],
        ]);

        CajaMovimiento::create([
            'user_id' => $cobrador->id,
            'tipo' => 'ingreso',
            'concepto' => 'Cobro a cliente: Rodriguez Maria',
            'monto' => 500000.00,
            'origen_type' => Cobro::class,
            'origen_id' => $cobro2->id,
            'created_at' => $fechaCobro2,
            'updated_at' => $fechaCobro2,
        ]);

        // --- Caso Histórico C: Gasto de Caja (Pago Luz) ---
        $fechaGasto = Carbon::now()->subDays(10);

        CajaMovimiento::create([
            'user_id' => $admin->id,
            'tipo' => 'egreso',
            'concepto' => 'Pago EDESE (Luz Oficina)',
            'monto' => 45000.00,
            'created_at' => $fechaGasto,
            'updated_at' => $fechaGasto,
        ]);
    }
}
