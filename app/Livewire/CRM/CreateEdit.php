<?php

namespace App\Livewire\CRM;

use App\Src\CRM\Enums\TipoCliente;
use App\Src\CRM\Models\Cliente;
use Livewire\Component;

class CreateEdit extends Component
{
    public $clienteId = null;
    
    // Form fields
    public $dni, $nombre, $apellido, $celular, $celular_referencia, $email, $tipo_cliente = 'ambos';

    public function mount($id = null)
    {
        if ($id) {
            $cliente = Cliente::findOrFail($id);
            $this->clienteId = $cliente->id;
            $this->dni = $cliente->dni;
            $this->nombre = $cliente->nombre;
            $this->apellido = $cliente->apellido;
            $this->celular = $cliente->celular;
            $this->celular_referencia = $cliente->celular_referencia;
            $this->email = $cliente->email;
            $this->tipo_cliente = $cliente->tipo_cliente->value;
        }
    }

    protected function rules()
    {
        return [
            'dni' => 'required|string|max:20|unique:clientes,dni,' . $this->clienteId,
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'celular' => 'required|string|max:50',
            'celular_referencia' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'tipo_cliente' => 'required|in:vendedor,comprador,ambos',
        ];
    }

    public function save()
    {
        $this->validate();

        Cliente::updateOrCreate(
            ['id' => $this->clienteId],
            [
                'dni' => $this->dni,
                'nombre' => $this->nombre,
                'apellido' => $this->apellido,
                'celular' => $this->celular,
                'celular_referencia' => $this->celular_referencia,
                'email' => $this->email,
                'tipo_cliente' => $this->tipo_cliente,
            ]
        );

        session()->flash('message', $this->clienteId ? 'Información actualizada correctamente.' : 'Cliente registrado exitosamente.');
        
        return $this->redirect(route('crm.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.crm.create-edit')->layout('layouts.app');
    }
}
