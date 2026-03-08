<?php

namespace App\Livewire\Shared;

use App\Src\Vehicles\Models\Vehiculo;
use Livewire\Component;
use Livewire\WithPagination;

class VehiculoSelector extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;

    protected $listeners = ['open-vehiculo-selector' => 'openModal', 'close-vehiculo-selector' => 'closeModal'];

    public function openModal()
    {
        $this->resetPage();
        $this->search = '';
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function selectVehiculo($id)
    {
        $this->dispatch('vehiculo-seleccionado', $id);
        $this->closeModal();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Vehiculo::query()->where('disponible', true);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('patente', 'like', "%{$this->search}%")
                  ->orWhere('marca', 'like', "%{$this->search}%")
                  ->orWhere('modelo', 'like', "%{$this->search}%");
            });
        }

        $vehiculos = $query->orderBy('marca')->orderBy('modelo')->paginate(10);

        return view('livewire.shared.vehiculo-selector', compact('vehiculos'));
    }
}
