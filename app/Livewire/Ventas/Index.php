<?php

namespace App\Livewire\Ventas;

use App\Src\CRM\Models\LegajoVehiculo;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Traeremos los LegajoVehiculo donde el tipo de legajo sea 'comprador' (que son las Ventas que hace la agencia al exterior)
        $ventas = LegajoVehiculo::with(['legajo.cliente', 'vehiculo', 'vehiculoEntregado'])
            ->whereHas('legajo', function ($q) {
                $q->where('tipo_legajo', 'comprador');
            })
            ->when($this->search, function ($query) {
                // Buscamos por apellido del cliente o patente del auto vendido
                $query->whereHas('legajo.cliente', function ($q) {
                    $q->where('apellido', 'like', '%' . $this->search . '%')
                      ->orWhere('nombre', 'like', '%' . $this->search . '%')
                      ->orWhere('dni', 'like', '%' . $this->search . '%');
                })->orWhereHas('vehiculo', function ($q) {
                    $q->where('patente', 'like', '%' . $this->search . '%')
                      ->orWhere('modelo', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.ventas.index', compact('ventas'))->layout('layouts.app');
    }
}
