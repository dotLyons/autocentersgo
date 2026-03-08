<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Src\CRM\Models\CuotaCreditoCasa;
use App\Src\CRM\Models\LegajoVehiculo;

class ReceiptController extends Controller
{
    public function cobranza($id)
    {
        $cuota = CuotaCreditoCasa::with(['legajoVehiculo.legajo.cliente', 'legajoVehiculo.vehiculo'])->findOrFail($id);
        
        return view('reports.recibo_cuota', compact('cuota'));
    }

    public function ventaResumen($legajoVehiculoId)
    {
        $venta = LegajoVehiculo::with(['legajo.cliente', 'vehiculo', 'cuotasCasa'])->findOrFail($legajoVehiculoId);
        
        return view('reports.resumen_venta', compact('venta'));
    }
}
