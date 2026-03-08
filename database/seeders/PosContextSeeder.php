<?php

namespace Database\Seeders;

use App\Src\POS\Models\Caja;
use App\Src\POS\Models\Tarjeta;
use App\Src\POS\Models\PlanPagoTarjeta;
use App\Src\POS\Models\MovimientoCaja;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PosContextSeeder extends Seeder
{
    /**
     * Seed the POS context with test data
     */
    public function run(): void
    {
        // ===== TARJETAS =====
        $visa = Tarjeta::create([
            'nombre' => 'Visa',
            'tipo' => 'credito',
            'activa' => true,
        ]);

        $mastercard = Tarjeta::create([
            'nombre' => 'MasterCard',
            'tipo' => 'credito',
            'activa' => true,
        ]);

        $debitoVisa = Tarjeta::create([
            'nombre' => 'Visa Débito',
            'tipo' => 'debito',
            'activa' => true,
        ]);

        $americanExpress = Tarjeta::create([
            'nombre' => 'American Express',
            'tipo' => 'credito',
            'activa' => true,
        ]);

        // ===== PLANES DE PAGO TARJETAS =====
        // Planes para Visa
        PlanPagoTarjeta::create([
            'tarjeta_id' => $visa->id,
            'cuotas' => 1,
            'interes' => 0.00,
            'activo' => true,
        ]);

        PlanPagoTarjeta::create([
            'tarjeta_id' => $visa->id,
            'cuotas' => 3,
            'interes' => 8.50,
            'activo' => true,
        ]);

        PlanPagoTarjeta::create([
            'tarjeta_id' => $visa->id,
            'cuotas' => 6,
            'interes' => 12.75,
            'activo' => true,
        ]);

        PlanPagoTarjeta::create([
            'tarjeta_id' => $visa->id,
            'cuotas' => 12,
            'interes' => 18.50,
            'activo' => true,
        ]);

        // Planes para MasterCard
        PlanPagoTarjeta::create([
            'tarjeta_id' => $mastercard->id,
            'cuotas' => 1,
            'interes' => 0.00,
            'activo' => true,
        ]);

        PlanPagoTarjeta::create([
            'tarjeta_id' => $mastercard->id,
            'cuotas' => 3,
            'interes' => 9.00,
            'activo' => true,
        ]);

        PlanPagoTarjeta::create([
            'tarjeta_id' => $mastercard->id,
            'cuotas' => 6,
            'interes' => 13.50,
            'activo' => true,
        ]);

        // Planes para Débito (sin interés)
        PlanPagoTarjeta::create([
            'tarjeta_id' => $debitoVisa->id,
            'cuotas' => 1,
            'interes' => 0.00,
            'activo' => true,
        ]);

        // Planes para American Express
        PlanPagoTarjeta::create([
            'tarjeta_id' => $americanExpress->id,
            'cuotas' => 1,
            'interes' => 0.00,
            'activo' => true,
        ]);

        PlanPagoTarjeta::create([
            'tarjeta_id' => $americanExpress->id,
            'cuotas' => 3,
            'interes' => 10.00,
            'activo' => true,
        ]);

        // ===== CAJAS =====
        // Caja abierta del día actual
        $cajaActual = Caja::create([
            'monto_apertura' => 10000.00,
            'monto_cierre' => null,
            'fecha_apertura' => Carbon::now()->startOfDay(),
            'fecha_cierre' => null,
            'estado' => 'abierta',
            'usuario_id' => 1, // Admin user
        ]);

        // Caja cerrada del día anterior
        $cajaCerrada = Caja::create([
            'monto_apertura' => 10000.00,
            'monto_cierre' => 25840.50,
            'fecha_apertura' => Carbon::now()->subDays(1)->setHour(9)->setMinute(0),
            'fecha_cierre' => Carbon::now()->subDays(1)->setHour(18)->setMinute(30),
            'estado' => 'cerrada',
            'usuario_id' => 1,
        ]);

        // ===== MOVIMIENTOS CAJA PARA CAJA ACTUAL =====
        // Ingreso por efectivo
        MovimientoCaja::create([
            'caja_id' => $cajaActual->id,
            'tipo_movimiento' => 'ingreso',
            'monto' => 5000.00,
            'descripcion' => 'Pago cliente Juan Pérez',
            'metodo_pago' => 'efectivo',
            'tarjeta_id' => null,
            'plan_pago_tarjeta_id' => null,
            'referencia_transferencia' => null,
        ]);

        // Ingreso por transferencia
        MovimientoCaja::create([
            'caja_id' => $cajaActual->id,
            'tipo_movimiento' => 'ingreso',
            'monto' => 8500.00,
            'descripcion' => 'Transferencia cliente María González',
            'metodo_pago' => 'transferencia',
            'tarjeta_id' => null,
            'plan_pago_tarjeta_id' => null,
            'referencia_transferencia' => 'CBU_20260306_001',
        ]);

        // Ingreso por tarjeta (Visa 1 cuota sin interés)
        MovimientoCaja::create([
            'caja_id' => $cajaActual->id,
            'tipo_movimiento' => 'ingreso',
            'monto' => 3500.00,
            'descripcion' => 'Pago con Visa',
            'metodo_pago' => 'tarjeta',
            'tarjeta_id' => $visa->id,
            'plan_pago_tarjeta_id' => 1, // 1 cuota sin interés
            'referencia_transferencia' => null,
        ]);

        // Ingreso por tarjeta (MasterCard 3 cuotas con interés)
        MovimientoCaja::create([
            'caja_id' => $cajaActual->id,
            'tipo_movimiento' => 'ingreso',
            'monto' => 3000.00,
            'descripcion' => 'Pago con MasterCard 3 cuotas',
            'metodo_pago' => 'tarjeta',
            'tarjeta_id' => $mastercard->id,
            'plan_pago_tarjeta_id' => 6, // 3 cuotas con interés 9%
            'referencia_transferencia' => null,
        ]);

        // Egreso por pago a proveedor
        MovimientoCaja::create([
            'caja_id' => $cajaActual->id,
            'tipo_movimiento' => 'egreso',
            'monto' => 2000.00,
            'descripcion' => 'Pago a proveedor por repuestos',
            'metodo_pago' => 'efectivo',
            'tarjeta_id' => null,
            'plan_pago_tarjeta_id' => null,
            'referencia_transferencia' => null,
        ]);

        // Egreso por gastos operativos
        MovimientoCaja::create([
            'caja_id' => $cajaActual->id,
            'tipo_movimiento' => 'egreso',
            'monto' => 1500.00,
            'descripcion' => 'Gastos de oficina',
            'metodo_pago' => 'transferencia',
            'tarjeta_id' => null,
            'plan_pago_tarjeta_id' => null,
            'referencia_transferencia' => 'TRF_GASTOS_20260306',
        ]);

        // ===== MOVIMIENTOS CAJA PARA CAJA CERRADA =====
        // Varios movimientos para caja anterior
        MovimientoCaja::create([
            'caja_id' => $cajaCerrada->id,
            'tipo_movimiento' => 'ingreso',
            'monto' => 12000.00,
            'descripcion' => 'Pagos diversos del día',
            'metodo_pago' => 'efectivo',
            'tarjeta_id' => null,
            'plan_pago_tarjeta_id' => null,
            'referencia_transferencia' => null,
        ]);

        MovimientoCaja::create([
            'caja_id' => $cajaCerrada->id,
            'tipo_movimiento' => 'ingreso',
            'monto' => 4500.00,
            'descripcion' => 'Transferencias recibidas',
            'metodo_pago' => 'transferencia',
            'tarjeta_id' => null,
            'plan_pago_tarjeta_id' => null,
            'referencia_transferencia' => 'CBU_20260305_002',
        ]);

        MovimientoCaja::create([
            'caja_id' => $cajaCerrada->id,
            'tipo_movimiento' => 'ingreso',
            'monto' => 2800.00,
            'descripcion' => 'Pago tarjeta débito',
            'metodo_pago' => 'tarjeta',
            'tarjeta_id' => $debitoVisa->id,
            'plan_pago_tarjeta_id' => 8, // Débito
            'referencia_transferencia' => null,
        ]);

        MovimientoCaja::create([
            'caja_id' => $cajaCerrada->id,
            'tipo_movimiento' => 'egreso',
            'monto' => 3460.50,
            'descripcion' => 'Diversos gastos del día',
            'metodo_pago' => 'efectivo',
            'tarjeta_id' => null,
            'plan_pago_tarjeta_id' => null,
            'referencia_transferencia' => null,
        ]);
    }
}
