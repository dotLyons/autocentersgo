<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CajaMovimiento;
use Carbon\Carbon;

class CajaManager extends Component
{
    // Filtros
    public $fecha_inicio;
    public $fecha_fin;
    public $tipo_filtro = ''; // '' = Todos, 'ingreso', 'egreso'

    public function mount()
    {
        // Por defecto mostramos el mes actual
        $this->fecha_inicio = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->fecha_fin = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function render()
    {
        $movimientos = CajaMovimiento::with('user')
            ->whereBetween('created_at', [
                $this->fecha_inicio . ' 00:00:00',
                $this->fecha_fin . ' 23:59:59'
            ])
            ->when($this->tipo_filtro, function ($q) {
                $q->where('tipo', $this->tipo_filtro);
            })
            ->latest()
            ->get();

        // Cálculos rápidos para el dashboard de caja
        $totalIngresos = $movimientos->where('tipo', 'ingreso')->sum('monto');
        $totalEgresos = $movimientos->where('tipo', 'egreso')->sum('monto');
        $saldo = $totalIngresos - $totalEgresos;

        return view('livewire.caja-manager', compact('movimientos', 'totalIngresos', 'totalEgresos', 'saldo'))
            ->layout('layouts.app');
    }
}
