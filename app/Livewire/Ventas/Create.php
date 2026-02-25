<?php

namespace App\Livewire\Ventas;

use App\Src\CRM\Actions\RegistrarCompraVehiculoAction;
use App\Src\CRM\Models\Cliente;
use App\Src\CRM\Models\Legajo;
use App\Src\Vehicles\Models\Vehiculo;
use App\Src\POS\Models\Caja;
use App\Src\POS\Enums\EstadoCaja;
use Livewire\Component;

class Create extends Component
{
    // Search properties for dynamically loading options
    public $searchCliente = '';
    public $searchVehiculo = '';
    public $searchVehiculoEntregado = '';

    // Selected Entities
    public $cliente_id = null;
    public $vehiculo_comprado_id = null;
    public $vehiculo_entregado_id = null;

    // Financial Data Form
    public $precio_compra = 0;
    public $monto_efectivo = 0;
    public $monto_transferencia = 0;
    public $monto_entrega = 0;
    public $valor_vehiculo_entregado = 0;
    public $monto_entrega_requerido = 0;
    public $saldo_entrega_pendiente = 0;
    
    public $financiacion_banco = 0;
    public $financiacion_casa = 0;
    public $cant_cuotas_casa = 0;

    public $retirado_ahora = false;
    public $transferencia_a_cargo_comprador = false;
    public $costo_transferencia = 0;

    public function seleccionarCliente($id)
    {
        $this->cliente_id = $id;
        $this->searchCliente = '';
    }

    public function seleccionarVehiculoComprado($id)
    {
        $this->vehiculo_comprado_id = $id;
        $this->searchVehiculo = '';
        
        // Auto pull public price
        $v = Vehiculo::find($id);
        if ($v) {
            $this->precio_compra = $v->precio_venta_publico ?? 0;
            $this->monto_entrega_requerido = $v->monto_entrega_requerido ?? 0;
        }
    }

    public function seleccionarVehiculoEntregado($id)
    {
        $this->vehiculo_entregado_id = $id;
        $this->searchVehiculoEntregado = '';
    }

    public function removerVehiculoEntregado()
    {
        $this->vehiculo_entregado_id = null;
        $this->valor_vehiculo_entregado = 0;
    }

    public function getTotalAportadoProperty()
    {
        return (float)$this->monto_efectivo + 
               (float)$this->monto_transferencia + 
               (float)$this->monto_entrega + 
               (float)$this->valor_vehiculo_entregado;
               
        // The remaining amount after Upfront items is what can be financed.
        $totalAportado = $aportesIniciales +
               (float)$this->financiacion_banco + 
               (float)$this->financiacion_casa;
               
        // Additionally, if there is a pending upfront payment, it is also a form of financing / "aportado" to total closing price
        $totalAportado += $this->getSaldoEntregaPendienteProperty();
        
        return $totalAportado;
    }

    public function getAportesInicialesProperty()
    {
        return (float)$this->monto_efectivo + 
               (float)$this->monto_transferencia + 
               (float)$this->monto_entrega + 
               (float)$this->valor_vehiculo_entregado;
    }

    public function getSaldoEntregaPendienteProperty()
    {
        $aportes = $this->getAportesInicialesProperty();
        $faltante = (float)$this->monto_entrega_requerido - $aportes;
        return $faltante > 0 ? $faltante : 0;
    }

    public function getDiferenciaProperty()
    {
        return (float)$this->precio_compra - $this->getTotalAportadoProperty();
    }

    public function registrarVenta(RegistrarCompraVehiculoAction $action)
    {
        $this->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'vehiculo_comprado_id' => 'required|exists:vehiculos,id',
            'precio_compra' => 'required|numeric|min:1',
            'monto_efectivo' => 'nullable|numeric|min:0',
            'monto_transferencia' => 'nullable|numeric|min:0',
            'valor_vehiculo_entregado' => 'nullable|numeric|min:0',
            'financiacion_casa' => 'nullable|numeric|min:0',
            'cant_cuotas_casa' => 'nullable|integer|min:0',
        ]);

        if (round($this->getDiferenciaProperty(), 2) !== 0.0) {
            session()->flash('error', 'El total aportado/financiado debe ser exactamente igual al Precio de Cierre de la operación. Diferencia actual: $ ' . round($this->getDiferenciaProperty(), 2));
            return;
        }

        if ($this->financiacion_casa > 0 && $this->cant_cuotas_casa <= 0) {
            session()->flash('error', 'Si hay financiación propia (De la Casa), debes indicar la cantidad de cuotas (mínimo 1).');
            return;
        }

        // Obtener la caja abierta del usuario
        $caja = Caja::where('usuario_id', auth()->id())
            ->where('estado', EstadoCaja::ABIERTA->value)
            ->first();

        // Obtener o Crear el Legajo "Comprador" para este Cliente
        $legajo = Legajo::firstOrCreate(
            ['cliente_id' => $this->cliente_id, 'tipo_legajo' => 'comprador'],
        );

        // Execute action
        $action->execute([
            'legajo_id' => $legajo->id,
            'vehiculo_comprado_id' => $this->vehiculo_comprado_id,
            'precio_compra' => $this->precio_compra,
            'monto_efectivo' => $this->monto_efectivo,
            'monto_transferencia' => $this->monto_transferencia,
            'monto_entrega' => $this->monto_entrega,
            'vehiculo_entregado_id' => $this->vehiculo_entregado_id,
            'valor_vehiculo_entregado' => $this->valor_vehiculo_entregado,
            'saldo_entrega_pendiente' => $this->getSaldoEntregaPendienteProperty(),
            'financiacion_banco' => $this->financiacion_banco,
            'financiacion_casa' => $this->financiacion_casa,
            'cant_cuotas_casa' => $this->cant_cuotas_casa,
            'retirado_ahora' => $this->retirado_ahora,
            'transferencia_a_cargo_comprador' => $this->transferencia_a_cargo_comprador,
            'costo_transferencia' => $this->costo_transferencia,
            
            // POS
            'caja_id' => $caja ? $caja->id : null,
            'usuario_id' => auth()->id(),
        ]);

        session()->flash('message', 'Operación de Venta registrada con éxito.');
        return $this->redirectRoute('ventas.index', navigate: true);
    }

    public function render()
    {
        $clientesList = [];
        if (strlen($this->searchCliente) > 2) {
            $clientesList = Cliente::where('nombre', 'like', "%{$this->searchCliente}%")
                ->orWhere('apellido', 'like', "%{$this->searchCliente}%")
                ->orWhere('dni', 'like', "%{$this->searchCliente}%")
                ->take(5)->get();
        }

        $vehiculosVenta = [];
        if (strlen($this->searchVehiculo) > 2) {
            // Vehiculos that can be sold (usually those that are not sold yet. Assumed filtering by empty search)
            $vehiculosVenta = Vehiculo::where('disponible', true)
                ->where(function ($q) {
                    $q->where('patente', 'like', "%{$this->searchVehiculo}%")
                      ->orWhere('marca', 'like', "%{$this->searchVehiculo}%")
                      ->orWhere('modelo', 'like', "%{$this->searchVehiculo}%");
                })
                ->take(5)->get();
        }

        $vehiculosEntregados = [];
        if (strlen($this->searchVehiculoEntregado) > 2) {
            // Buscando un vehiculo que el cliente esté entregando (debería estar cargado previamente en el catálogo)
            $vehiculosEntregados = Vehiculo::where('patente', 'like', "%{$this->searchVehiculoEntregado}%")
                ->orWhere('marca', 'like', "%{$this->searchVehiculoEntregado}%")
                ->orWhere('modelo', 'like', "%{$this->searchVehiculoEntregado}%")
                ->take(5)->get();
        }
        
        // Load selected objects for visualization
        $clienteSeleccionado = $this->cliente_id ? Cliente::find($this->cliente_id) : null;
        $vehiculoCompradoSeleccionado = $this->vehiculo_comprado_id ? Vehiculo::find($this->vehiculo_comprado_id) : null;
        $vehiculoEntregadoSeleccionado = $this->vehiculo_entregado_id ? Vehiculo::find($this->vehiculo_entregado_id) : null;

        return view('livewire.ventas.create', compact(
            'clientesList', 
            'vehiculosVenta', 
            'vehiculosEntregados',
            'clienteSeleccionado',
            'vehiculoCompradoSeleccionado',
            'vehiculoEntregadoSeleccionado'
        ))->layout('layouts.app');
    }
}
