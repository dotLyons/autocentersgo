<?php

namespace App\Livewire\Shared;

use App\Src\CRM\Models\Cliente;
use Livewire\Component;
use Livewire\WithPagination;

class ClienteSelector extends Component
{
    use WithPagination;

    public $search = '';
    public $filtro = 'todos';
    public $isOpen = false;

    protected $listeners = ['open-cliente-selector' => 'openModal', 'close-cliente-selector' => 'closeModal'];

    public function openModal()
    {
        $this->resetPage();
        $this->search = '';
        $this->filtro = 'todos';
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function selectCliente($id)
    {
        $this->dispatch('cliente-seleccionado', $id);
        $this->closeModal();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFiltro()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Cliente::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('dni', 'like', "%{$this->search}%")
                  ->orWhere('apellido', 'like', "%{$this->search}%")
                  ->orWhere('nombre', 'like', "%{$this->search}%");
            });
        }

        if ($this->filtro !== 'todos') {
            $query->where('tipo_cliente', $this->filtro);
        }

        $clientes = $query->orderBy('apellido')->orderBy('nombre')->paginate(10);

        return view('livewire.shared.cliente-selector', compact('clientes'));
    }
}
