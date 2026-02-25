<?php

namespace App\Livewire\Vehicles;

use App\Src\Vehicles\Models\Vehiculo;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filtroPropiedad = ''; // propio, consignacion
    public $filtroTipo = ''; // auto, camioneta, furgon, moto

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFiltroPropiedad()
    {
        $this->resetPage();
    }

    public function updatingFiltroTipo()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Vehiculo::query()
            ->with('formularios')
            ->when($this->search, function ($q) {
                $q->where('marca', 'like', '%' . $this->search . '%')
                  ->orWhere('modelo', 'like', '%' . $this->search . '%')
                  ->orWhere('patente', 'like', '%' . $this->search . '%');
            })
            ->when($this->filtroPropiedad, function ($q) {
                $q->where('categoria_propiedad', $this->filtroPropiedad);
            })
            ->when($this->filtroTipo, function ($q) {
                $q->where('tipo_vehiculo', $this->filtroTipo);
            })
            ->orderBy('id', 'desc');

        return view('livewire.vehicles.index', [
            'vehiculos' => $query->paginate(12)
        ])->layout('layouts.app');
    }
}
