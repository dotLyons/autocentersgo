<?php

namespace App\Livewire;

use App\Src\CRM\Actions\CobrarCuotaCreditoCasaAction;
use App\Src\CRM\Models\CuotaCreditoCasa;
use App\Src\POS\Models\Caja;
use App\Src\POS\Enums\EstadoCaja;
use Livewire\Component;
use Livewire\WithPagination;

class Cobrador extends Component
{
    use WithPagination;

    public $search = '';
    public $filtro_estado = 'todas';
    public $vista = 'agrupada';

    public $tarjetasAbiertas = [];
    public $paginasPorTarjeta = [];

    public $isOpenModalCobro = false;
    public $cuota_a_cobrar_id = null;
    public $cuota_seleccionada = null;
    public $monto_a_cobrar = '';
    public $interes_mora = 0;
    public $es_pagada_total = true;
    public $metodo_pago = 'efectivo';

    // Segundo método de pago (pago diferido/dividido)
    public $habilitar_segundo_pago = false;
    public $monto_segundo_pago = '';
    public $metodo_pago_segundo = 'transferencia';

    protected $queryString = ['page', 'filtro_estado'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFiltroEstado()
    {
        $this->resetPage();
    }

    public function updatingMontoACobrar()
    {
        // Calcular automáticamente si se pagará completamente
        if ($this->cuota_seleccionada && $this->monto_a_cobrar) {
            $montoFaltante = $this->cuota_seleccionada->monto - $this->cuota_seleccionada->monto_pagado - floatval($this->monto_a_cobrar);
            // Si falta dinero, no marcar como pagada
            if ($montoFaltante > 0.01) { // Permitir pequeña diferencia por redondeo
                $this->es_pagada_total = false;
            } else {
                $this->es_pagada_total = true;
            }
        }
    }

    public function toggleTarjeta($legajoVehiculoId)
    {
        if (isset($this->tarjetasAbiertas[$legajoVehiculoId])) {
            unset($this->tarjetasAbiertas[$legajoVehiculoId]);
        } else {
            $this->tarjetasAbiertas[$legajoVehiculoId] = true;
            if (!isset($this->paginasPorTarjeta[$legajoVehiculoId])) {
                $this->paginasPorTarjeta[$legajoVehiculoId] = 1;
            }
        }
    }

    public function setPaginaTarjeta($legajoVehiculoId, $pagina)
    {
        $this->paginasPorTarjeta[$legajoVehiculoId] = $pagina;
    }

    public function abrirModalCobro($cuotaId, $monto)
    {
        $this->cuota_a_cobrar_id = $cuotaId;
        $this->cuota_seleccionada = CuotaCreditoCasa::with(['legajoVehiculo.legajo.cliente', 'legajoVehiculo.vehiculo'])->find($cuotaId);
        $this->monto_a_cobrar = $monto;
        $this->interes_mora = 0;
        $this->es_pagada_total = true;
        $this->metodo_pago = 'efectivo';
        $this->habilitar_segundo_pago = false;
        $this->monto_segundo_pago = '';
        $this->metodo_pago_segundo = 'transferencia';
        $this->isOpenModalCobro = true;
    }

    public function procesarCobro(CobrarCuotaCreditoCasaAction $action)
    {
        $this->validate([
            'cuota_a_cobrar_id' => 'required|exists:cuotas_credito_casa,id',
            'monto_a_cobrar' => 'required|numeric|min:1',
            'interes_mora' => 'required|numeric|min:0',
            'es_pagada_total' => 'boolean',
            'metodo_pago' => 'required|string',
            'habilitar_segundo_pago' => 'boolean',
            'monto_segundo_pago' => 'nullable|numeric|min:0',
            'metodo_pago_segundo' => 'nullable|string',
        ]);

        $caja = Caja::where('usuario_id', auth()->id())
            ->where('estado', EstadoCaja::ABIERTA->value)
            ->first();

        if (!$caja) {
            session()->flash('error', 'Debes abrir una Caja (POS) en tu turno actual antes de poder cobrar una cuota de crédito.');
            return;
        }

        try {
            $cuota = CuotaCreditoCasa::find($this->cuota_a_cobrar_id);
            $legajoVehiculoId = $cuota->legajo_vehiculo_id;

            // Permitir pago diferido solo si se indica que es pagada total
            // Si hay segundo pago, NO marcar como completamente pagada
            $marcarComoPagada = $this->es_pagada_total && !$this->habilitar_segundo_pago;

            // Primer pago
            $action->execute($this->cuota_a_cobrar_id, [
                'caja_id' => $caja->id,
                'usuario_id' => auth()->id(),
                'metodo_pago' => $this->metodo_pago,
                'monto_pagado' => $this->monto_a_cobrar,
                'interes_mora' => $this->interes_mora,
                'es_pagada_total' => $marcarComoPagada,
            ]);

            // Segundo pago (si aplica)
            if ($this->habilitar_segundo_pago && $this->monto_segundo_pago > 0) {
                $action->execute($this->cuota_a_cobrar_id, [
                    'caja_id' => $caja->id,
                    'usuario_id' => auth()->id(),
                    'metodo_pago' => $this->metodo_pago_segundo,
                    'monto_pagado' => $this->monto_segundo_pago,
                    'interes_mora' => 0, // El interés solo se aplica en el primer pago
                    'es_pagada_total' => true, // Ahora con el segundo pago, marcar como completamente pagada
                ]);
            }

            $this->isOpenModalCobro = false;
            $this->cuota_seleccionada = null;

            $this->tarjetasAbiertas[$legajoVehiculoId] = true;
            $this->filtro_estado = 'todas';

            $mesajeExtra = $this->habilitar_segundo_pago ? " (Pago dividido en 2 métodos)" : "";
            session()->flash('message', 'La cuota ha sido cobrada exitosamente e impactó en tu Caja.' . $mesajeExtra);

        } catch (\Exception $e) {
            session()->flash('error', 'Error al procesar: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = CuotaCreditoCasa::with([
            'legajoVehiculo.legajo.cliente',
            'legajoVehiculo.vehiculo',
            'legajoVehiculo'
        ])
        ->when($this->search, function ($query) {
            $query->whereHas('legajoVehiculo.legajo.cliente', function ($q) {
                $q->where('apellido', 'like', '%' . $this->search . '%')
                  ->orWhere('nombre', 'like', '%' . $this->search . '%')
                  ->orWhere('dni', 'like', '%' . $this->search . '%');
            });
        })
        ->orderBy('fecha_vencimiento', 'asc');

        $cuotas = $query->get();

        $agrupadas = $cuotas->groupBy('legajo_vehiculo_id')->map(function ($grupo) {
            $primerCuota = $grupo->first();
            $cliente = $primerCuota->legajoVehiculo->legajo->cliente;
            $vehiculo = $primerCuota->legajoVehiculo->vehiculo;
            $cuotasOrdenadas = $grupo->sortBy('numero_cuota')->values();

            return [
                'legajo_vehiculo_id' => $primerCuota->legajo_vehiculo_id,
                'cliente' => $cliente,
                'vehiculo' => $vehiculo,
                'precio_compra' => $primerCuota->legajoVehiculo->precio_compra,
                'cuotas' => $cuotasOrdenadas,
                'total_cuotas' => $grupo->count(),
                'cuotas_pagadas' => $grupo->where('pagada', true)->count(),
                'cuotas_pendientes' => $grupo->where('pagada', false)->count(),
                'total_pagado' => $grupo->sum('monto_pagado'),
                'total_deuda' => $grupo->sum(function ($c) {
                    return $c->monto - $c->monto_pagado + ($c->interes_mora ?? 0);
                }),
            ];
        });

        // Aplicar filtro en内存 después de agrupar
        if ($this->filtro_estado === 'pendientes') {
            $agrupadas = $agrupadas->filter(function ($item) {
                return $item['cuotas_pendientes'] > 0;
            });
        } elseif ($this->filtro_estado === 'cobradas') {
            $agrupadas = $agrupadas->filter(function ($item) {
                return $item['cuotas_pagadas'] > 0 && $item['cuotas_pendientes'] == 0;
            });
        }

        $agrupadas = $agrupadas->sortBy(function ($item) {
            return $item['vehiculo']->patente ?? '';
        });

        $agrupadasPaginadas = $agrupadas->forPage($this->getPage(), 20);

        return view('livewire.cobrador.index', [
            'agrupadas' => $agrupadasPaginadas,
            'agrupadasTotal' => $agrupadas->count(),
            'cuotas' => $query->paginate(20)
        ])->layout('layouts.app');
    }

    protected function getPage(): int
    {
        return request()->query('page', 1);
    }
}
