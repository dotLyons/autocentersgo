<?php

namespace App\Src\CRM\Actions;

use App\Src\CRM\Models\Legajo;
use App\Src\CRM\Models\LegajoVehiculo;
use App\Src\CRM\Models\CuotaCreditoCasa;
use App\Src\Vehicles\Models\Vehiculo;
use App\Src\Vehicles\Enums\CategoriaPropiedad;
use App\Src\POS\Models\MovimientoCaja;
use App\Src\POS\Enums\TipoMovimiento;
use App\Src\POS\Enums\TipoMetodoPago;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RegistrarCompraVehiculoAction
{
    /**
     * @param array $data => [
     *     'legajo_id' => int,
     *     'vehiculo_comprado_id' => int,
     *     'precio_compra' => float,
     *     'monto_efectivo' => float,
     *     'monto_transferencia' => float,
     *     'monto_entrega' => float, // Other generic upfront entries
     *     'vehiculo_entregado_id' => int|null,
     *     'valor_vehiculo_entregado' => float|null,
     *     'financiacion_banco' => float,
     *     'financiacion_casa' => float,
     *     'cant_cuotas_casa' => int,
     *     'retirado_ahora' => bool,
     *     'transferencia_a_cargo_comprador' => bool,
     *     'costo_transferencia' => float|null,
     *     
     *     'cant_cuotas_casa' => int,
     *     'saldo_entrega_pendiente' => float,
     *     'retirado_ahora' => bool,
     *     'transferencia_a_cargo_comprador' => bool,
     *     'costo_transferencia' => float|null,
     *     'caja_id' => int,
     *     'usuario_id' => int,
     * ]
     */
    public function execute(array $data): LegajoVehiculo
    {
        return DB::transaction(function () use ($data) {
            
            // 1. Calculate the values
            $financiacionCasa = $data['financiacion_casa'] ?? 0;
            $cantCuotasCasa = $data['cant_cuotas_casa'] ?? 0;
            $montoCuotaCasa = $data['monto_cuota_casa'] ?? 0;

            if ($financiacionCasa > 0 && $cantCuotasCasa > 0) {
                if ($montoCuotaCasa <= 0) {
                    $montoCuotaCasa = $financiacionCasa / $cantCuotasCasa;
                }
            }

            // 2. Insert LegajoVehiculo record for the buyer
            $legajoVehiculo = LegajoVehiculo::create([
                'legajo_id' => $data['legajo_id'],
                'vehiculo_id' => $data['vehiculo_comprado_id'],
                
                'precio_compra' => $data['precio_compra'],
                'monto_efectivo' => $data['monto_efectivo'] ?? 0,
                'monto_transferencia' => $data['monto_transferencia'] ?? 0,
                'monto_entrega' => $data['monto_entrega'] ?? 0,
                'saldo_entrega_pendiente' => $data['saldo_entrega_pendiente'] ?? 0,
                
                'vehiculo_entregado_id' => $data['vehiculo_entregado_id'] ?? null,
                'valor_vehiculo_entregado' => $data['valor_vehiculo_entregado'] ?? 0,
                
                'financiacion_banco' => $data['financiacion_banco'] ?? 0,
                'financiacion_casa' => $financiacionCasa,
                'cant_cuotas_casa' => $cantCuotasCasa,
                'monto_cuota_casa' => $montoCuotaCasa,
                'total_pagado_casa' => 0,

                'retirado_ahora' => $data['retirado_ahora'] ?? false,
                'transferencia_a_cargo_comprador' => $data['transferencia_a_cargo_comprador'] ?? false,
                'costo_transferencia' => ($data['transferencia_a_cargo_comprador'] ?? false) ? ($data['costo_transferencia'] ?? 0) : 0,
            ]);

            // 3. Mark the purchased vehicle as sold (if stock logic applies)
            // Can be implemented later, but logic usually is like this:
            $vehiculoComprado = Vehiculo::find($data['vehiculo_comprado_id']);
            if ($vehiculoComprado) {
                $vehiculoComprado->update(['disponible' => false]);
            }

            // 4. If a vehicle was traded in, update its status so it becomes part of the dealer's stock
            if (!empty($data['vehiculo_entregado_id'])) {
                $vehiculoTraded = Vehiculo::find($data['vehiculo_entregado_id']);
                if ($vehiculoTraded) {
                    $vehiculoTraded->update([
                        'categoria_propiedad' => CategoriaPropiedad::PROPIO->value,
                        'precio_venta_publico' => null, // Needs to be priced manually later
                        'vendedor_id' => null, // Resets if needed
                    ]);
                }
            }

            // 5. Generate house credit installments automatically if applied
            if ($financiacionCasa > 0 && $cantCuotasCasa > 0) {
                $fechasVencimientoBase = Carbon::now();
                
                for ($i = 1; $i <= $cantCuotasCasa; $i++) {
                    CuotaCreditoCasa::create([
                        'legajo_vehiculo_id' => $legajoVehiculo->id,
                        'numero_cuota' => $i,
                        'monto' => $montoCuotaCasa,
                        'fecha_vencimiento' => $fechasVencimientoBase->copy()->addMonths($i),
                        'pagada' => false,
                    ]);
                }
            }

            // 6. Registrar en caja los movimientos iniciales de Efectivo y Transferencia 
            // (El banco y el vehículo entregado no entran en caja)
            
            $cajaId = $data['caja_id'] ?? null;
            if ($cajaId) {
                // Pago en Efectivo
                if (($data['monto_efectivo'] ?? 0) > 0) {
                    MovimientoCaja::create([
                        'caja_id' => $cajaId,
                        'tipo_movimiento' => TipoMovimiento::INGRESO->value,
                        'metodo_pago' => TipoMetodoPago::EFECTIVO->value,
                        'monto' => $data['monto_efectivo'],
                        'descripcion' => "Entrega en efectivo por compra de Vehiculo ID: " . $data['vehiculo_comprado_id'],
                    ]);
                }
                
                // Pago en Transferencia
                if (($data['monto_transferencia'] ?? 0) > 0) {
                    MovimientoCaja::create([
                        'caja_id' => $cajaId,
                        'tipo_movimiento' => TipoMovimiento::INGRESO->value,
                        'metodo_pago' => TipoMetodoPago::TRANSFERENCIA->value,
                        'monto' => $data['monto_transferencia'],
                        'descripcion' => "Entrega en transferencia por compra de Vehiculo ID: " . $data['vehiculo_comprado_id'],
                    ]);
                }
                
                // Otras entregas que podrían ser en efectivo (ej. tarjetas/cheques)
                // Se dejan a consideración, pero si 'monto_entrega' es un adicional monetario directo:
                if (($data['monto_entrega'] ?? 0) > 0) {
                    MovimientoCaja::create([
                        'caja_id' => $cajaId,
                        'tipo_movimiento' => TipoMovimiento::INGRESO->value,
                        'metodo_pago' => TipoMetodoPago::EFECTIVO->value, // Podría ser otro, fallback a efectivo
                        'monto' => $data['monto_entrega'],
                        'descripcion' => "Otras entregas (monetarias) por compra de Vehiculo ID: " . $data['vehiculo_comprado_id'],
                    ]);
                }
            }

            return $legajoVehiculo;
        });
    }
}
