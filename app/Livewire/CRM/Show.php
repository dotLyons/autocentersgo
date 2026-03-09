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

        // Obtener la caja abierta del usuario
        $caja = \App\Src\POS\Models\Caja::where('usuario_id', auth()->id())
            ->where('estado', \App\Src\POS\Enums\EstadoCaja::ABIERTA->value)
            ->first();

        if (!$caja) {
            session()->flash('error', 'No tienes una caja abierta. Por favor, abre una caja en el módulo Administrativo para registrar este cobro.');
            return;
        }

        $metodoCaja = \App\Src\POS\Enums\TipoMetodoPago::EFECTIVO->value;
        if ($this->pago_entrega_metodo === 'transferencia') {
            $metodoCaja = \App\Src\POS\Enums\TipoMetodoPago::TRANSFERENCIA->value;
        }

        // Registrar ingreso en caja
        \App\Src\POS\Models\MovimientoCaja::create([
            'caja_id' => $caja->id,
            'tipo_movimiento' => \App\Src\POS\Enums\TipoMovimiento::INGRESO->value,
            'metodo_pago' => $metodoCaja,
            'monto' => $this->pago_entrega_monto,
            'descripcion' => "Abono Saldo Reserva: Unit. ID {$legajoVehiculo->vehiculo_id} (P/ {$this->pago_entrega_metodo})",
        ]);

        \App\Src\CRM\Models\PagoEntrega::create([
            'legajo_vehiculo_id' => $legajoVehiculo->id,
            'monto' => $this->pago_entrega_monto,
            'fecha_pago' => now()->format('Y-m-d'),
            'metodo_pago' => $this->pago_entrega_metodo,
            'registrado_por' => auth()->user() ? auth()->user()->name : 'Admin',
        ]);

        // Reducimos el saldo pendiente y aumentamos los montos aportados en LegajoVehiculo
        $legajoVehiculo->saldo_entrega_pendiente -= $this->pago_entrega_monto;
        
        switch ($this->pago_entrega_metodo) {
            case 'efectivo':
                $legajoVehiculo->monto_efectivo += $this->pago_entrega_monto;
                break;
            case 'transferencia':
                $legajoVehiculo->monto_transferencia += $this->pago_entrega_monto;
                break;
            default:
                $legajoVehiculo->monto_entrega += $this->pago_entrega_monto;
                break;
        }

        $legajoVehiculo->save();

        session()->flash('message', 'Pago registrado y descontado del saldo pendiente.');
        $this->isOpenModalPagoEntrega = false;
        
        // Refrescamos el cliente desde la db para renderizar
        $this->mount($this->cliente->id);
    }

    public function toggleEntregado($legajoVehiculoId)
    {
        $legajoVehiculo = \App\Src\CRM\Models\LegajoVehiculo::find($legajoVehiculoId);
        
        if ($legajoVehiculo) {
            if ($legajoVehiculo->saldo_entrega_pendiente <= 0) {
                // Hacer el toggle del estado
                $legajoVehiculo->entregado = !$legajoVehiculo->entregado;
                $legajoVehiculo->save();
                
                // Refrescar el estado del componente
                $this->mount($this->cliente->id);
                
                $estado = $legajoVehiculo->entregado ? 'Entregado' : 'No entregado';
                session()->flash('message', "El estado del vehículo se actualizó a: $estado.");
            } else {
                session()->flash('error', 'No se puede marcar como entregado porque aún hay un saldo pendiente de la entrega mínima requerida.');
            }
        }
    }

    public function render()
    {
        return view('livewire.crm.show')->layout('layouts.app');
    }
}
