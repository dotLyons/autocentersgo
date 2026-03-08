<?php

namespace Database\Seeders;

use App\Src\CRM\Models\Cliente;
use App\Src\CRM\Models\Legajo;
use App\Src\CRM\Models\LegajoVehiculo;
use App\Src\CRM\Models\CuotaCreditoCasa;
use App\Src\CRM\Models\PagoEntrega;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CrmContextSeeder extends Seeder
{
    /**
     * Seed the CRM context with test data
     */
    public function run(): void
    {
        // ===== CLIENTES =====
        $cliente1 = Cliente::create([
            'dni' => '12345678',
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'celular' => '2901234567',
            'celular_referencia' => '2909876543',
            'email' => 'juan.perez@email.com',
            'tipo_cliente' => 'ambos', // vendedor y comprador
        ]);

        $cliente2 = Cliente::create([
            'dni' => '87654321',
            'nombre' => 'María',
            'apellido' => 'González',
            'celular' => '2912345678',
            'celular_referencia' => null,
            'email' => 'maria.gonzalez@email.com',
            'tipo_cliente' => 'comprador',
        ]);

        $cliente3 = Cliente::create([
            'dni' => '45678912',
            'nombre' => 'Carlos',
            'apellido' => 'López',
            'celular' => '2923456789',
            'celular_referencia' => '2919876543',
            'email' => 'carlos.lopez@email.com',
            'tipo_cliente' => 'vendedor',
        ]);

        $cliente4 = Cliente::create([
            'dni' => '56789123',
            'nombre' => 'Laura',
            'apellido' => 'Martínez',
            'celular' => '2934567890',
            'celular_referencia' => null,
            'email' => 'laura.martinez@email.com',
            'tipo_cliente' => 'comprador',
        ]);

        // ===== LEGAJOS =====
        $legajo1 = Legajo::create([
            'cliente_id' => $cliente1->id,
            'tipo_legajo' => 'vendedor',
        ]);

        $legajo2 = Legajo::create([
            'cliente_id' => $cliente1->id,
            'tipo_legajo' => 'comprador',
        ]);

        $legajo3 = Legajo::create([
            'cliente_id' => $cliente2->id,
            'tipo_legajo' => 'comprador',
        ]);

        $legajo4 = Legajo::create([
            'cliente_id' => $cliente3->id,
            'tipo_legajo' => 'vendedor',
        ]);

        $legajo5 = Legajo::create([
            'cliente_id' => $cliente4->id,
            'tipo_legajo' => 'comprador',
        ]);

        // ===== LEGAJO VEHICULOS (Transacciones) =====
        // Transacción 1: Juan vende y compra (concesionaria actúa como intermediario)
        $legajoVehiculo1 = LegajoVehiculo::create([
            'legajo_id' => $legajo1->id,
            'vehiculo_id' => 1, // Asume que hay vehículo con ID 1
            'precio_acordado' => 150000.00,
            'cargo_concesionaria' => 10000.00,
            'esta_vendido' => true,
            'metodo_pago_venta' => 'efectivo',
            'retirado_ahora' => true,
            'transferencia_a_cargo_comprador' => false,
        ]);

        // Transacción 2: María compra con financiación
        $legajoVehiculo2 = LegajoVehiculo::create([
            'legajo_id' => $legajo3->id,
            'vehiculo_id' => 2,
            'precio_compra' => 180000.00,
            'monto_efectivo' => 50000.00,
            'monto_transferencia' => 40000.00,
            'monto_entrega' => 20000.00,
            'vehiculo_entregado_id' => null,
            'valor_vehiculo_entregado' => null,
            'financiacion_banco' => 0.00,
            'financiacion_casa' => 70000.00,
            'cant_cuotas_casa' => 12,
            'monto_cuota_casa' => 5833.33,
            'total_pagado_casa' => 0.00,
            'retirado_ahora' => false,
            'transferencia_a_cargo_comprador' => true,
            'costo_transferencia' => 1500.00,
        ]);

        // Transacción 3: Carlos vende con consignación
        $legajoVehiculo3 = LegajoVehiculo::create([
            'legajo_id' => $legajo4->id,
            'vehiculo_id' => 3,
            'precio_acordado' => 120000.00,
            'cargo_concesionaria' => 8000.00,
            'esta_vendido' => false,
            'metodo_pago_venta' => null,
            'retirado_ahora' => true,
        ]);

        // Transacción 4: Laura compra con financiación parcial
        $legajoVehiculo4 = LegajoVehiculo::create([
            'legajo_id' => $legajo5->id,
            'vehiculo_id' => 4,
            'precio_compra' => 200000.00,
            'monto_efectivo' => 60000.00,
            'monto_transferencia' => 80000.00,
            'monto_entrega' => 15000.00,
            'vehiculo_entregado_id' => null,
            'valor_vehiculo_entregado' => null,
            'financiacion_banco' => 45000.00,
            'financiacion_casa' => 0.00,
            'cant_cuotas_casa' => null,
            'monto_cuota_casa' => null,
            'total_pagado_casa' => null,
            'retirado_ahora' => false,
            'transferencia_a_cargo_comprador' => false,
        ]);

        // ===== CUOTAS CREDITO CASA =====
        // Cuotas para María (12 cuotas de 5833.33)
        for ($i = 1; $i <= 12; $i++) {
            $pagada = $i <= 3; // Primeras 3 pagadas
            CuotaCreditoCasa::create([
                'legajo_vehiculo_id' => $legajoVehiculo2->id,
                'numero_cuota' => $i,
                'monto' => 5833.33,
                'fecha_vencimiento' => Carbon::now()->addMonths($i),
                'pagada' => $pagada,
                'fecha_pago' => $pagada ? Carbon::now()->subMonths(12 - $i) : null,
                'monto_pagado' => $pagada ? 5833.33 : null,
                'metodo_pago' => $pagada ? 'transferencia' : null,
                'cobrado_por_id' => $pagada ? 1 : null, // Usuario 1 (admin)
            ]);
        }

        // ===== PAGOS ENTREGAS =====
        // Pagos para retiro de vehículos
        PagoEntrega::create([
            'legajo_vehiculo_id' => $legajoVehiculo1->id,
            'monto' => 150000.00,
            'fecha_pago' => Carbon::now(),
            'metodo_pago' => 'efectivo',
            'movimiento_caja_id' => null,
            'registrado_por' => 'Admin',
        ]);

        PagoEntrega::create([
            'legajo_vehiculo_id' => $legajoVehiculo2->id,
            'monto' => 110000.00,
            'fecha_pago' => Carbon::now()->subDays(5),
            'metodo_pago' => 'transferencia',
            'movimiento_caja_id' => null,
            'registrado_por' => 'Admin',
        ]);

        PagoEntrega::create([
            'legajo_vehiculo_id' => $legajoVehiculo4->id,
            'monto' => 80000.00,
            'fecha_pago' => Carbon::now()->subDays(10),
            'metodo_pago' => 'transferencia',
            'movimiento_caja_id' => null,
            'registrado_por' => 'Admin',
        ]);
    }
}
