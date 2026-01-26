<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cliente;
use App\Models\Cobro;
use App\Models\CajaMovimiento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CobrosCreate extends Component
{
    // Cliente
    public $searchCliente = '';
    public $cliente_id;
    public $cliente_seleccionado_nombre;

    // Métodos de Pago Dinámicos
    // Estructura: [['metodo' => 'efectivo', 'monto' => 0, 'referencia' => '']]
    public $itemsPago = [];

    public $total_a_pagar = 0;
    public $observaciones;

    public function mount()
    {
        // Iniciamos con una fila de efectivo por defecto
        $this->itemsPago[] = ['metodo' => 'efectivo', 'monto' => '', 'referencia' => ''];
    }

    // Buscador de Cliente
    public function seleccionarCliente($id, $nombre)
    {
        $this->cliente_id = $id;
        $this->cliente_seleccionado_nombre = $nombre;
        $this->searchCliente = '';
    }

    // Gestión del Array de Pagos
    public function agregarMetodoPago()
    {
        $this->itemsPago[] = ['metodo' => 'transferencia', 'monto' => '', 'referencia' => ''];
    }

    public function quitarMetodoPago($index)
    {
        unset($this->itemsPago[$index]);
        $this->itemsPago = array_values($this->itemsPago); // Reindexar
    }

    public function updatedItemsPago()
    {
        // Recalcular total visualmente si es necesario
        $this->total_a_pagar = 0;
        foreach ($this->itemsPago as $item) {
            $this->total_a_pagar += (float) ($item['monto'] ?? 0);
        }
    }

    public function guardarCobro()
    {
        $this->validate([
            'cliente_id' => 'required',
            'itemsPago' => 'required|array|min:1',
            'itemsPago.*.metodo' => 'required|in:efectivo,transferencia',
            'itemsPago.*.monto' => 'required|numeric|min:0.01',
            // La referencia es requerida SOLO si el método es transferencia
            'itemsPago.*.referencia' => 'required_if:itemsPago.*.metodo,transferencia',
        ], [
            'itemsPago.*.referencia.required_if' => 'El código de referencia es obligatorio para transferencias.'
        ]);

        // Transacción de BD: O se guarda todo (Cobro, Detalles y Caja) o nada.
        DB::transaction(function () {

            // 1. Calcular total real
            $montoTotal = collect($this->itemsPago)->sum('monto');

            // 2. Crear Cabecera Cobro
            $cobro = Cobro::create([
                'cliente_id' => $this->cliente_id,
                'user_id' => Auth::id(),
                'monto_total' => $montoTotal,
                'observaciones' => $this->observaciones,
            ]);

            // 3. Crear Detalles (Métodos mixtos)
            foreach ($this->itemsPago as $item) {
                $cobro->detalles()->create([
                    'metodo_pago' => $item['metodo'],
                    'monto' => $item['monto'],
                    'referencia' => $item['referencia'] ?? null,
                ]);
            }

            // 4. Registrar en CAJA (Movimiento Automático)
            CajaMovimiento::create([
                'user_id' => Auth::id(),
                'tipo' => 'ingreso',
                'concepto' => 'Cobro a cliente: ' . $this->cliente_seleccionado_nombre,
                'monto' => $montoTotal,
                'origen_type' => Cobro::class,
                'origen_id' => $cobro->id,
            ]);
        });

        session()->flash('message', 'Cobro registrado exitosamente.');
        return redirect()->route('caja.index');
    }

    public function render()
    {
        $clientes = [];
        if (strlen($this->searchCliente) > 2) {
            $clientes = Cliente::where('nombre', 'like', "%{$this->searchCliente}%")
                ->orWhere('apellido', 'like', "%{$this->searchCliente}%")
                ->orWhere('dni', 'like', "%{$this->searchCliente}%")
                ->take(5)->get();
        }

        return view('livewire.cobros-create', compact('clientes'))
            ->layout('layouts.app');
    }
}
