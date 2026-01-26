<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Vehiculo;
use Livewire\WithPagination;

class VentasManager extends Component
{
    use WithPagination;

    // Datos del formulario
    public $cliente_id, $vehiculo_id, $precio_venta, $cantidad_cuotas, $monto_cuota, $fecha_cobro, $observaciones;
    public $venta_id;

    // Buscadores internos del Modal
    public $searchClienteInput = '';
    public $searchVehiculoInput = '';
    public $selectedClienteName = null; // Para mostrar nombre seleccionado
    public $selectedVehiculoName = null; // Para mostrar vehiculo seleccionado

    // Estados de Modales
    public $isOpen = false;
    public $isPinModalOpen = false; // Modal para pedir clave
    public $pinInput = ''; // Campo del PIN
    public $pendingEditId = null; // ID que se intentó editar

    // Filtro general de la tabla
    public $search = '';

    public function render()
    {
        $ventas = Venta::with(['cliente', 'vehiculo'])
            ->whereHas('cliente', function ($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%')
                    ->orWhere('apellido', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        // Lógica para buscadores del modal (solo si está abierto para ahorrar recursos)
        $clientesResult = [];
        $vehiculosResult = [];

        if ($this->isOpen) {
            if (!empty($this->searchClienteInput)) {
                $clientesResult = Cliente::where('dni', 'like', '%' . $this->searchClienteInput . '%')
                    ->orWhere('apellido', 'like', '%' . $this->searchClienteInput . '%')
                    ->limit(5)->get();
            }

            if (!empty($this->searchVehiculoInput)) {
                $vehiculosResult = Vehiculo::where('is_available', true) // Solo disponibles
                    ->where(function ($q) {
                        $q->where('patent', 'like', '%' . $this->searchVehiculoInput . '%')
                            ->orWhere('model', 'like', '%' . $this->searchVehiculoInput . '%');
                    })
                    ->limit(5)->get();
            }
        }

        return view('livewire.ventas-manager', [
            'ventas' => $ventas,
            'clientesFound' => $clientesResult,
            'vehiculosFound' => $vehiculosResult
        ])->layout('layouts.app');
    }

    // --- SELECCIÓN PREDICTIVA ---

    public function selectCliente($id, $name)
    {
        $this->cliente_id = $id;
        $this->selectedClienteName = $name;
        $this->searchClienteInput = ''; // Limpiar busqueda
    }

    public function selectVehiculo($id, $name, $price)
    {
        $this->vehiculo_id = $id;
        $this->selectedVehiculoName = $name;
        $this->precio_venta = $price; // Auto-asignar precio
        $this->searchVehiculoInput = '';
    }

    // --- GESTIÓN DE MODALES Y CRUD ---

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    // Intento de Edición (Pide PIN)
    public function tryEdit($id)
    {
        $this->pendingEditId = $id;
        $this->pinInput = '';
        $this->isPinModalOpen = true; // Abre modal de PIN
    }

    // Verificar PIN
    public function verifyPin()
    {
        if ($this->pinInput === '2468') {
            $this->isPinModalOpen = false;
            $this->edit($this->pendingEditId); // Si es correcto, abre el editor
        } else {
            $this->addError('pin', 'Clave incorrecta.');
        }
    }

    public function edit($id)
    {
        $venta = Venta::findOrFail($id);
        $this->venta_id = $id;

        // Cargar datos
        $this->cliente_id = $venta->cliente_id;
        $this->selectedClienteName = $venta->cliente->nombre . ' ' . $venta->cliente->apellido;

        $this->vehiculo_id = $venta->vehiculo_id;
        // Permitimos buscar el vehículo actual aunque ya no esté disponible (porque ya se vendió)
        $this->selectedVehiculoName = $venta->vehiculo->brand . ' ' . $venta->vehiculo->model . ' (' . $venta->vehiculo->patent . ')';

        $this->precio_venta = $venta->precio_venta;
        $this->cantidad_cuotas = $venta->cantidad_cuotas;
        $this->monto_cuota = $venta->monto_cuota;
        $this->observaciones = $venta->observaciones;

        $this->fecha_cobro = $venta->fecha_cobro ? $venta->fecha_cobro->format('Y-m-d') : null;

        $this->openModal();
    }

    public function store()
    {
        $this->validate([
            'cliente_id' => 'required',
            'vehiculo_id' => 'required',
            'precio_venta' => 'required|numeric',
            'cantidad_cuotas' => 'required|integer|min:1',
            'monto_cuota' => 'required|numeric',
            'fecha_cobro' => 'required|date',
        ]);

        $venta = Venta::updateOrCreate(['id' => $this->venta_id], [
            'cliente_id' => $this->cliente_id,
            'vehiculo_id' => $this->vehiculo_id,
            'precio_venta' => $this->precio_venta,
            'cantidad_cuotas' => $this->cantidad_cuotas,
            'monto_cuota' => $this->monto_cuota,
            'observaciones' => $this->observaciones,
            'fecha_cobro' => $this->fecha_cobro,
        ]);

        // Opcional: Marcar vehículo como no disponible al crear venta nueva
        if (!$this->venta_id) {
            $vehiculo = Vehiculo::find($this->vehiculo_id);
            if ($vehiculo) {
                $vehiculo->is_available = false;
                $vehiculo->save();
            }
        }

        session()->flash('message', $this->venta_id ? 'Venta actualizada.' : 'Venta registrada con éxito.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function delete($id)
    {
        // Opcional: Al borrar venta, liberar vehículo?
        // Por ahora solo borramos la venta
        Venta::find($id)->delete();
        session()->flash('message', 'Venta eliminada.');
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->isPinModalOpen = false;
        $this->resetErrorBag();
    }

    private function resetInputFields()
    {
        $this->cliente_id = null;
        $this->vehiculo_id = null;
        $this->precio_venta = '';
        $this->cantidad_cuotas = '';
        $this->monto_cuota = '';
        $this->observaciones = '';
        $this->fecha_cobro = null;
        $this->venta_id = null;
        $this->searchClienteInput = '';
        $this->searchVehiculoInput = '';
        $this->selectedClienteName = null;
        $this->selectedVehiculoName = null;
    }
}
