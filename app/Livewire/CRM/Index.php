<?php

namespace App\Livewire\CRM;

use App\Src\CRM\Models\Cliente;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filteroTipo = ''; // Vendedor, Comprador, Ambos

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Cliente::query()
            ->when($this->search, function ($q) {
                $q->where('dni', 'like', '%' . $this->search . '%')
                  ->orWhere('nombre', 'like', '%' . $this->search . '%')
                  ->orWhere('apellido', 'like', '%' . $this->search . '%');
            })
            ->when($this->filteroTipo, function ($q) {
                $q->where('tipo_cliente', $this->filteroTipo);
            })
            ->orderBy('id', 'desc');

        return view('livewire.crm.index', [
            'clientes' => $query->paginate(10)
        ])->layout('layouts.app');
    }
}
