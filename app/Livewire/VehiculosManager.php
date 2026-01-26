<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Vehiculo;
use Livewire\WithPagination;

class VehiculosManager extends Component
{
    use WithPagination;

    public $brand, $model, $patent, $model_year, $color, $price, $details, $is_available = true;
    public $vehiculo_id;

    // Estados de la vista
    public $isOpen = false;
    public $isViewMode = false; // <--- NUEVA PROPIEDAD

    // Filtros
    public $search = '';
    public $searchField = 'patent';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $vehiculos = Vehiculo::where($this->searchField, 'like', '%' . $this->search . '%')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.vehiculos-manager', [
            'vehiculos' => $vehiculos
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isViewMode = false; // Aseguramos que NO sea modo lectura
        $this->openModal();
    }

    // NUEVO METODO PARA VER DETALLES
    public function view($id)
    {
        $this->edit($id); // Reutilizamos la lógica de cargar datos
        $this->isViewMode = true; // Activamos modo lectura
    }

    public function edit($id)
    {
        $vehiculo = Vehiculo::findOrFail($id);
        $this->vehiculo_id = $id;
        $this->brand = $vehiculo->brand;
        $this->model = $vehiculo->model;
        $this->patent = $vehiculo->patent;
        $this->model_year = $vehiculo->model_year;
        $this->color = $vehiculo->color;
        $this->price = $vehiculo->price;
        $this->details = $vehiculo->details;
        $this->is_available = $vehiculo->is_available;

        $this->isViewMode = false; // Si editamos, permitimos escritura
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->isViewMode = false; // Reseteamos el modo lectura al cerrar
        $this->resetErrorBag();
    }

    private function resetInputFields()
    {
        $this->brand = '';
        $this->model = '';
        $this->patent = '';
        $this->model_year = '';
        $this->color = '';
        $this->price = '';
        $this->details = '';
        $this->is_available = true;
        $this->vehiculo_id = null;
        $this->isViewMode = false;
    }

    public function store()
    {
        // Si estamos en modo ver, no hacemos nada (por seguridad extra)
        if ($this->isViewMode) {
            return;
        }

        $this->validate([
            'brand' => 'required',
            'model' => 'required',
            'patent' => 'required|unique:vehiculos,patent,' . $this->vehiculo_id,
            'model_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'price' => 'required|numeric',
            'color' => 'required',
        ]);

        Vehiculo::updateOrCreate(['id' => $this->vehiculo_id], [
            'brand' => $this->brand,
            'model' => $this->model,
            'patent' => $this->patent,
            'model_year' => $this->model_year,
            'color' => $this->color,
            'price' => $this->price,
            'details' => $this->details,
            'is_available' => $this->is_available,
        ]);

        session()->flash(
            'message',
            $this->vehiculo_id ? 'Vehículo actualizado correctamente.' : 'Vehículo creado correctamente.'
        );

        $this->closeModal();
        $this->resetInputFields();
    }

    public function delete($id)
    {
        Vehiculo::find($id)->delete();
        session()->flash('message', 'Vehículo eliminado correctamente.');
    }
}
