<?php

namespace App\Livewire\CRM;

use App\Src\CRM\Models\Cliente;
use Livewire\Component;

class Show extends Component
{
    public Cliente $cliente;

    public function mount($id)
    {
        // Traemos el cliente con sus legajos (vendedor y comprador) resueltos con los vehículos y cuotas
        $this->cliente = Cliente::with([
            'legajos.vehiculos.vehiculo', 
            'legajos.vehiculos.cuotasCreditoCasa',
            'legajos.vehiculos.vehiculoEntregado',
            'legajos.vehiculos.pagosEntrega'
        ])->findOrFail($id);
    }

    public $isOpenModalPagoEntrega = false;
    public $pago_entrega_legajo_vehiculo_id = null;
    public $pago_entrega_monto = '';
    public $pago_entrega_metodo = 'efectivo';
    
    public function abrirModalPago($legajoVehiculoId)
    {
        $this->pago_entrega_legajo_vehiculo_id = $legajoVehiculoId;
        $this->pago_entrega_monto = '';
        $this->pago_entrega_metodo = 'efectivo';
        $this->isOpenModalPagoEntrega = true;
    }

    public function guardarPagoEntrega()
    {
        $this->validate([
            'pago_entrega_legajo_vehiculo_id' => 'required|exists:legajo_vehiculos,id',
            'pago_entrega_monto' => 'required|numeric|min:1',
            'pago_entrega_metodo' => 'required|string',
        ]);

        $legajoVehiculo = \App\Src\CRM\Models\LegajoVehiculo::find($this->pago_entrega_legajo_vehiculo_id);
        
        if ($this->pago_entrega_monto > $legajoVehiculo->saldo_entrega_pendiente) {
            session()->flash('error', 'El monto no puede superar el saldo pendiente.');
            return;
        }

        \App\Src\CRM\Models\PagoEntrega::create([
            'legajo_vehiculo_id' => $legajoVehiculo->id,
            'monto' => $this->pago_entrega_monto,
            'fecha_pago' => now()->format('Y-m-d'),
            'metodo_pago' => $this->pago_entrega_metodo,
            'registrado_por' => auth()->user() ? auth()->user()->name : 'Admin',
        ]);

        // Reducimos el saldo pendiente
        $legajoVehiculo->saldo_entrega_pendiente -= $this->pago_entrega_monto;
        $legajoVehiculo->save();

        session()->flash('message', 'Pago registrado y descontado del saldo pendiente.');
        $this->isOpenModalPagoEntrega = false;
        
        // Refrescamos el cliente desde la db para renderizar
        $this->mount($this->cliente->id);
    }

    public function render()
    {
        return view('livewire.crm.show')->layout('layouts.app');
    }
}
