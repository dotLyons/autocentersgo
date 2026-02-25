<?php

namespace App\Livewire\POS;

use Livewire\Component;
use Livewire\WithPagination;
use App\Src\POS\Models\Caja;
use App\Src\POS\Models\MovimientoCaja;
use App\Src\POS\Enums\EstadoCaja;
use App\Src\POS\Enums\TipoMovimiento;
use App\Src\POS\Enums\TipoMetodoPago;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    use WithPagination;

    public $cajaActiva;

    // Modals states
    public $isOpenAbrirCaja = false;
    public $isOpenCerrarCaja = false;
    public $isOpenMovimiento = false;

    // For Abrir Caja
    public $montoApertura = 0;

    // For Cerrar Caja
    public $montoCierreInformado = null;

    // For Movimiento Manual
    public $movTipo = 'ingreso';
    public $movMetodo = 'efectivo';
    public $movMonto = '';
    public $movDescripcion = '';

    public function mount()
    {
        $this->loadCajaActiva();
    }

    public function loadCajaActiva()
    {
        $this->cajaActiva = Caja::where('estado', EstadoCaja::ABIERTA->value)->latest()->first();
    }

    public function abrirModalCaja()
    {
        $this->montoApertura = '';
        $this->isOpenAbrirCaja = true;
    }

    public function abrirCaja()
    {
        $this->validate([
            'montoApertura' => 'required|numeric|min:0'
        ]);

        Caja::create([
            'monto_apertura' => $this->montoApertura,
            'fecha_apertura' => Carbon::now(),
            'estado' => EstadoCaja::ABIERTA->value,
            'usuario_id' => Auth::id(),
        ]);

        $this->isOpenAbrirCaja = false;
        $this->loadCajaActiva();
        session()->flash('message', 'Caja abierta exitosamente.');
    }

    public function abrirModalMovimiento()
    {
        $this->movTipo = 'ingreso';
        $this->movMetodo = 'efectivo';
        $this->movMonto = '';
        $this->movDescripcion = '';
        $this->isOpenMovimiento = true;
    }

    public function registrarMovimiento()
    {
        $this->validate([
            'movTipo' => 'required|in:ingreso,egreso',
            'movMetodo' => 'required|in:efectivo,transferencia,tarjeta',
            'movMonto' => 'required|numeric|min:0.01',
            'movDescripcion' => 'required|string|max:255',
        ]);

        if (!$this->cajaActiva) {
            session()->flash('error', 'No hay caja abierta.');
            return;
        }

        MovimientoCaja::create([
            'caja_id' => $this->cajaActiva->id,
            'tipo_movimiento' => $this->movTipo,
            'metodo_pago' => $this->movMetodo,
            'monto' => $this->movMonto,
            'descripcion' => $this->movDescripcion,
        ]);

        $this->isOpenMovimiento = false;
        session()->flash('message', 'Movimiento registrado con éxito.');
        $this->resetPage(); // refrescar lista
    }

    public function abrirModalCerrar()
    {
        $agrupados = $this->calcularTotalesPorMetodo();
        $esperadoEfectivo = $this->cajaActiva->monto_apertura + $agrupados['efectivo_ingreso'] - $agrupados['efectivo_egreso'];
        
        $this->montoCierreInformado = str_replace(',', '', number_format($esperadoEfectivo, 2, '.', ''));
        $this->isOpenCerrarCaja = true;
    }

    public function cerrarCaja()
    {
        $this->validate([
            'montoCierreInformado' => 'required|numeric|min:0',
        ]);

        if (!$this->cajaActiva) {
            return;
        }

        $this->cajaActiva->update([
            'monto_cierre' => $this->montoCierreInformado,
            'fecha_cierre' => Carbon::now(),
            'estado' => EstadoCaja::CERRADA->value,
        ]);

        $this->isOpenCerrarCaja = false;
        $this->loadCajaActiva();
        session()->flash('message', 'Caja cerrada exitosamente.');
    }

    // Calcular montos agrupados para métricas y cierre
    public function calcularTotalesPorMetodo()
    {
        if (!$this->cajaActiva) return [];

        $movs = MovimientoCaja::where('caja_id', $this->cajaActiva->id)->get();

        $totales = [
            'efectivo_ingreso' => 0,
            'efectivo_egreso' => 0,
            'transferencia_ingreso' => 0,
            'transferencia_egreso' => 0,
            'tarjeta_ingreso' => 0,
            'tarjeta_egreso' => 0,
        ];

        foreach($movs as $m) {
            $key = $m->metodo_pago->value . '_' . $m->tipo_movimiento->value;
            if(isset($totales[$key])){
                $totales[$key] += $m->monto;
            }
        }

        return $totales;
    }

    public function render()
    {
        $movimientos = [];
        $resumen = [];

        if ($this->cajaActiva) {
            $movimientos = MovimientoCaja::where('caja_id', $this->cajaActiva->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            
            $resumen = $this->calcularTotalesPorMetodo();
        }

        return view('livewire.pos.index', [
            'movimientos' => $movimientos,
            'resumen' => $resumen
        ])->layout('layouts.app');
    }
}
