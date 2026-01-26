<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cliente;

class LegajoCliente extends Component
{
    public $cliente;
    public $ventas;

    public function mount($id)
    {
        // Buscamos el cliente y cargamos sus ventas junto con los vehículos
        $this->cliente = Cliente::with(['ventas.vehiculo'])->findOrFail($id);
        $this->ventas = $this->cliente->ventas;
    }

    public function render()
    {
        return view('livewire.legajo-cliente')
            ->layout('layouts.app');
    }

    // Método auxiliar para calcular cuotas pagadas (FUTURO)
    public function getCuotasPagadas($ventaId)
    {
        // TODO: Cuando tengamos la tabla 'cobros', contaremos los pagos aquí.
        // return Cobro::where('venta_id', $ventaId)->count();
        return 0; // Por ahora retorna 0
    }

    public function getProgresoPorcentaje($pagadas, $totales)
    {
        if ($totales == 0) return 0;
        return round(($pagadas / $totales) * 100);
    }
}
