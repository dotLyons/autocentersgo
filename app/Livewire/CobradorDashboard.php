<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Venta;
use App\Models\Cobro;
use App\Models\CajaMovimiento;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CobradorDashboard extends Component
{
    use WithPagination;

    // Estado del Modal
    public $isOpen = false;

    // Datos de la Venta seleccionada para cobrar
    public $venta_seleccionada = null;
    public $cliente_nombre = '';
    public $vehiculo_info = '';

    // Lógica de Pagos (Igual que en CobrosCreate)
    public $itemsPago = [];
    public $observaciones = '';
    public $total_a_pagar = 0;

    public function render()
    {
        // Buscamos ventas cuya fecha de cobro sea HOY o ANTERIOR (deudas pendientes)
        // y que el vehículo no esté dado de baja (opcional)
        $ventasPendientes = Venta::with(['cliente', 'vehiculo'])
            ->whereDate('fecha_cobro', '<=', Carbon::today())
            ->orderBy('fecha_cobro', 'asc')
            ->paginate(10);

        return view('livewire.cobrador-dashboard', compact('ventasPendientes'))
            ->layout('layouts.app');
    }

    // --- ABRIR MODAL Y PRECARGAR DATOS ---
    public function abrirModalCobro($ventaId)
    {
        $this->venta_seleccionada = Venta::with(['cliente', 'vehiculo'])->findOrFail($ventaId);

        // Precarga visual
        $this->cliente_nombre = $this->venta_seleccionada->cliente->apellido . ' ' . $this->venta_seleccionada->cliente->nombre;
        $this->vehiculo_info = $this->venta_seleccionada->vehiculo->brand . ' ' . $this->venta_seleccionada->vehiculo->model . ' (' . $this->venta_seleccionada->vehiculo->patent . ')';

        // Precarga del pago sugerido (Monto de la cuota en Efectivo por defecto)
        $this->itemsPago = [
            [
                'metodo' => 'efectivo',
                'monto' => $this->venta_seleccionada->monto_cuota, // Sugerimos el valor de la cuota
                'referencia' => ''
            ]
        ];

        $this->calculateTotal();
        $this->isOpen = true;
    }

    // --- GESTIÓN DE MÉTODOS DE PAGO (Dinámico) ---
    public function agregarMetodoPago()
    {
        $this->itemsPago[] = ['metodo' => 'transferencia', 'monto' => '', 'referencia' => ''];
    }

    public function quitarMetodoPago($index)
    {
        unset($this->itemsPago[$index]);
        $this->itemsPago = array_values($this->itemsPago);
        $this->calculateTotal();
    }

    public function updatedItemsPago()
    {
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->total_a_pagar = 0;
        foreach ($this->itemsPago as $item) {
            $this->total_a_pagar += (float) ($item['monto'] ?? 0);
        }
    }

    // --- CONFIRMAR COBRO ---
    public function store()
    {
        $this->validate([
            'itemsPago' => 'required|array|min:1',
            'itemsPago.*.metodo' => 'required|in:efectivo,transferencia',
            'itemsPago.*.monto' => 'required|numeric|min:0.01',
            'itemsPago.*.referencia' => 'required_if:itemsPago.*.metodo,transferencia',
        ], [
            'itemsPago.*.referencia.required_if' => 'Falta la referencia de la transferencia.'
        ]);

        DB::transaction(function () {

            // 1. Crear Cabecera Cobro
            $cobro = Cobro::create([
                'cliente_id' => $this->venta_seleccionada->cliente_id,
                'user_id' => Auth::id(),
                'monto_total' => $this->total_a_pagar,
                'observaciones' => $this->observaciones,
            ]);

            // 2. Crear Detalles
            foreach ($this->itemsPago as $item) {
                $cobro->detalles()->create([
                    'metodo_pago' => $item['metodo'],
                    'monto' => $item['monto'],
                    'referencia' => $item['referencia'] ?? null,
                ]);
            }

            // 3. Registrar en CAJA
            CajaMovimiento::create([
                'user_id' => Auth::id(),
                'tipo' => 'ingreso',
                'concepto' => 'Cobro a cliente: ' . $this->cliente_nombre, // Formato solicitado
                'monto' => $this->total_a_pagar,
                'origen_type' => Cobro::class,
                'origen_id' => $cobro->id,
            ]);

            // 4. ACTUALIZAR LA FECHA DE COBRO DE LA VENTA (Para que salga de la lista de hoy)
            // Movemos la fecha de cobro 1 mes adelante
            $nuevaFecha = Carbon::parse($this->venta_seleccionada->fecha_cobro)->addMonth();
            $this->venta_seleccionada->fecha_cobro = $nuevaFecha;
            $this->venta_seleccionada->save();
        });

        session()->flash('message', 'Cobro registrado correctamente. Próximo vencimiento: ' . $this->venta_seleccionada->fecha_cobro->format('d/m/Y'));

        $this->closeModal();
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->venta_seleccionada = null;
        $this->itemsPago = [];
        $this->observaciones = '';
    }
}
