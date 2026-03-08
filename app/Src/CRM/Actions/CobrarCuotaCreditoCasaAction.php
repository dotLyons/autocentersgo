<?php

namespace App\Src\CRM\Actions;

use App\Src\CRM\Models\CuotaCreditoCasa;
use App\Src\POS\Models\MovimientoCaja;
use App\Src\POS\Enums\TipoMovimiento;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class CobrarCuotaCreditoCasaAction
{
    /**
     * @param int $cuotaId
     * @param array $data => [
     *     'caja_id' => int,
     *     'usuario_id' => int, // Quien cobró
     *     'metodo_pago' => string, // Ejemplo: efectivo, transferencia
     *     'monto_pagado' => float|null, // Opcional por si no paga exacta la cuota
     * ]
     * @return CuotaCreditoCasa
     */
    public function execute(int $cuotaId, array $data): CuotaCreditoCasa
    {
        return DB::transaction(function () use ($cuotaId, $data) {
            $cuota = CuotaCreditoCasa::with('legajoVehiculo')->findOrFail($cuotaId);

            if ($cuota->pagada) {
                throw new Exception("Esta cuota ya se encuentra pagada.");
            }

            $montoAbonado = $data['monto_pagado'] ?? $cuota->monto;
            $interesMora = $data['interes_mora'] ?? 0;
            $marcarComoPagada = $data['es_pagada_total'] ?? true;

            // Actualizar estado de cuota
            $cuota->update([
                'pagada' => $marcarComoPagada,
                'fecha_pago' => Carbon::now(),
                'monto_pagado' => $cuota->monto_pagado + $montoAbonado,
                'interes_mora' => $cuota->interes_mora + $interesMora,
                'metodo_pago' => $data['metodo_pago'],
                'cobrado_por_id' => $data['usuario_id'],
            ]);

            // Actualizar el acumulado en el legajo ("cuanto va pagando")
            $legajoVehiculo = $cuota->legajoVehiculo;
            $legajoVehiculo->increment('total_pagado_casa', $montoAbonado); // Solo el capital amortiza deuda de capital
            
            $montoTotalImpactoCaja = $montoAbonado + $interesMora;

            // Generar el movimiento de caja
            MovimientoCaja::create([
                'caja_id' => $data['caja_id'],
                'tipo_movimiento' => TipoMovimiento::INGRESO->value,
                'metodo_pago' => $data['metodo_pago'],
                'monto' => $montoTotalImpactoCaja,
                'descripcion' => "Cobro de cuota nro {$cuota->numero_cuota} correspondiente al legajo de vehiculo {$legajoVehiculo->id}" . ($interesMora > 0 ? " (Incluye \$" . number_format($interesMora, 2) . " de interés)" : ""),
            ]);

            return $cuota;
        });
    }
}
