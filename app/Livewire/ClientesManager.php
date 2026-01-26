<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cliente;
use Livewire\WithPagination;

class ClientesManager extends Component
{
    use WithPagination;

    // Campos
    public $dni, $nombre, $apellido, $telefono, $direccion, $is_active = true;
    public $cliente_id;

    // Estados
    public $isOpen = false;
    public $isViewMode = false;

    // Filtros
    public $search = '';
    public $searchField = 'dni'; // Busqueda por defecto por DNI

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $clientes = Cliente::where($this->searchField, 'like', '%' . $this->search . '%')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.clientes-manager', [
            'clientes' => $clientes
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isViewMode = false;
        $this->openModal();
    }

    public function view($id)
    {
        $this->edit($id);
        $this->isViewMode = true;
    }

    public function edit($id)
    {
        $cliente = Cliente::findOrFail($id);
        $this->cliente_id = $id;
        $this->dni = $cliente->dni;
        $this->nombre = $cliente->nombre;
        $this->apellido = $cliente->apellido;
        $this->telefono = $cliente->telefono;
        $this->direccion = $cliente->direccion;
        $this->is_active = $cliente->is_active;

        $this->isViewMode = false;
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->isViewMode = false;
        $this->resetErrorBag();
    }

    private function resetInputFields()
    {
        $this->dni = '';
        $this->nombre = '';
        $this->apellido = '';
        $this->telefono = '';
        $this->direccion = '';
        $this->is_active = true;
        $this->cliente_id = null;
        $this->isViewMode = false;
    }

    public function store()
    {
        if ($this->isViewMode) {
            return;
        }

        $this->validate([
            'dni' => 'required|numeric|unique:clientes,dni,' . $this->cliente_id,
            'nombre' => 'required',
            'apellido' => 'required',
            'telefono' => 'nullable',
            'direccion' => 'nullable',
        ]);

        Cliente::updateOrCreate(['id' => $this->cliente_id], [
            'dni' => $this->dni,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
            'is_active' => $this->is_active,
        ]);

        session()->flash(
            'message',
            $this->cliente_id ? 'Cliente actualizado correctamente.' : 'Cliente creado correctamente.'
        );

        $this->closeModal();
        $this->resetInputFields();
    }

    public function delete($id)
    {
        Cliente::find($id)->delete();
        session()->flash('message', 'Cliente eliminado correctamente.');
    }
}
