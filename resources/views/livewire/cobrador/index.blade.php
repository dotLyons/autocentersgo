<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Cobranzas y Gestión de Créditos') }}
            </h2>
            <div class="flex items-center space-x-2">
                <button wire:click="$set('vista', 'agrupada')"
                    class="px-3 py-1.5 rounded-md text-sm font-medium transition {{ $vista === 'agrupada' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    <i class="fas fa-layer-group mr-1"></i> Agrupado
                </button>
                <button wire:click="$set('vista', 'lista')"
                    class="px-3 py-1.5 rounded-md text-sm font-medium transition {{ $vista === 'lista' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    <i class="fas fa-list mr-1"></i> Lista
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session()->has('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm" role="alert">
                    <p class="font-bold">Error</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            @if (session()->has('message'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm" role="alert">
                    <p class="font-bold">Éxito</p>
                    <p>{{ session('message') }}</p>
                </div>
            @endif

            {{-- Panel de Filtros --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-4 flex flex-col md:flex-row gap-4 items-center">
                    <div class="relative flex-1 w-full">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input wire:model.live.debounce.300ms="search" type="text"
                            class="pl-10 w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Buscar por cliente (DNI, Apellido, Nombre)...">
                    </div>

                    <select wire:model.live="filtro_estado"
                        class="w-full md:w-48 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="pendientes">Pendientes</option>
                        <option value="cobradas">Cobradas</option>
                        <option value="todas">Todas</option>
                    </select>
                </div>
            </div>

            @if($vista === 'agrupada')
                {{-- VISTA AGRUPADA POR VEHÍCULO (ACORDEÓN) --}}
                <div class="space-y-4">
                    @forelse($agrupadas as $grupo)
                        @php
                            $cliente = $grupo['cliente'];
                            $vehiculo = $grupo['vehiculo'];
                            $porcentajePagado = $grupo['total_cuotas'] > 0 ? ($grupo['cuotas_pagadas'] / $grupo['total_cuotas']) * 100 : 0;
                            $isOpen = isset($tarjetasAbiertas[$grupo['legajo_vehiculo_id']]) && $tarjetasAbiertas[$grupo['legajo_vehiculo_id']];
                            $paginaActual = $paginasPorTarjeta[$grupo['legajo_vehiculo_id']] ?? 1;
                            $cuotasPorPagina = 12;
                            $totalPaginas = ceil($grupo['total_cuotas'] / $cuotasPorPagina);
                            $cuotasPaginadas = $grupo['cuotas']->forPage($paginaActual, $cuotasPorPagina);
                        @endphp

                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                            {{-- Header de la tarjeta (siempre visible) --}}
                            <div wire:click="toggleTarjeta({{ $grupo['legajo_vehiculo_id'] }})"
                                 class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4 flex flex-col md:flex-row md:items-center justify-between gap-4 cursor-pointer hover:from-indigo-700 hover:to-indigo-800 transition">
                                <div class="flex items-center gap-4">
                                    <div class="bg-white/20 p-3 rounded-lg">
                                        <i class="fas fa-car text-white text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-white">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</h3>
                                        <div class="flex items-center gap-2 text-indigo-100 text-sm">
                                            <span class="bg-white/20 px-2 py-0.5 rounded font-mono">{{ $vehiculo->patente }}</span>
                                            <span>•</span>
                                            <span>{{ $vehiculo->anio }}</span>
                                            <span class="bg-white/20 px-2 py-0.5 rounded text-xs ml-2">
                                                {{ $grupo['total_cuotas'] }} cuotas
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between md:justify-end gap-6">
                                    <div class="text-right">
                                        <p class="text-indigo-200 text-xs uppercase tracking-wide">Cliente</p>
                                        <p class="text-white font-semibold">{{ $cliente->apellido }}, {{ $cliente->nombre }}</p>
                                    </div>
                                    <div class="text-right hidden md:block">
                                        <p class="text-indigo-200 text-xs uppercase tracking-wide">Pendiente</p>
                                        <p class="text-white font-bold text-lg">$ {{ number_format($grupo['total_deuda'], 2) }}</p>
                                    </div>
                                    <div class="bg-white/20 p-2 rounded-lg">
                                        <i class="fas {{ $isOpen ? 'fa-chevron-up' : 'fa-chevron-down' }} text-white text-lg transition-transform"></i>
                                    </div>
                                </div>
                            </div>

                            {{-- Progress bar (siempre visible) --}}
                            <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-700">
                                        {{ $grupo['cuotas_pagadas'] }} / {{ $grupo['total_cuotas'] }} cuotas pagadas
                                    </span>
                                    <span class="text-sm font-bold text-indigo-600">{{ number_format($porcentajePagado, 0) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-indigo-600 h-2 rounded-full transition-all" style="width: {{ $porcentajePagado }}%"></div>
                                </div>
                            </div>

                            {{-- Contenido del acordeón (expandible) --}}
                            @if($isOpen)
                                <div class="border-t border-gray-200">
                                    {{-- Paginación interna de cuotas --}}
                                    <div class="px-6 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                                        <span class="text-sm text-gray-600">
                                            Mostrando {{ $cuotasPaginadas->count() }} de {{ $grupo['total_cuotas'] }} cuotas
                                        </span>
                                        @if($totalPaginas > 1)
                                            <nav class="flex items-center gap-1">
                                                <button wire:click="setPaginaTarjeta({{ $grupo['legajo_vehiculo_id'] }}, {{ $paginaActual - 1 }})"
                                                    class="px-2 py-1 rounded text-sm {{ $paginaActual <= 1 ? 'text-gray-300 cursor-not-allowed' : 'text-gray-600 hover:bg-gray-200' }}"
                                                    {{ $paginaActual <= 1 ? 'disabled' : '' }}>
                                                    <i class="fas fa-chevron-left"></i>
                                                </button>
                                                @for($p = 1; $p <= $totalPaginas; $p++)
                                                    <button wire:click="setPaginaTarjeta({{ $grupo['legajo_vehiculo_id'] }}, {{ $p }})"
                                                        class="px-2 py-1 rounded text-sm {{ $paginaActual == $p ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-200' }}">
                                                        {{ $p }}
                                                    </button>
                                                @endfor
                                                <button wire:click="setPaginaTarjeta({{ $grupo['legajo_vehiculo_id'] }}, {{ $paginaActual + 1 }})"
                                                    class="px-2 py-1 rounded text-sm {{ $paginaActual >= $totalPaginas ? 'text-gray-300 cursor-not-allowed' : 'text-gray-600 hover:bg-gray-200' }}"
                                                    {{ $paginaActual >= $totalPaginas ? 'disabled' : '' }}>
                                                    <i class="fas fa-chevron-right"></i>
                                                </button>
                                            </nav>
                                        @endif
                                    </div>

                                    {{-- Grid de cuotas --}}
                                    <div class="p-4">
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                                            @foreach($cuotasPaginadas as $cuota)
                                                @php
                                                    $estaAtrasada = !$cuota->pagada && \Carbon\Carbon::parse($cuota->fecha_vencimiento)->isPast();
                                                    $montoRestante = $cuota->monto - $cuota->monto_pagado;
                                                @endphp
                                                <div class="relative p-3 rounded-lg border-2 transition-all duration-200
                                                    {{ $cuota->pagada ? 'border-green-200 bg-green-50' : ($estaAtrasada ? 'border-red-200 bg-red-50' : 'border-gray-200 bg-white hover:border-indigo-300') }}">

                                    <div class="flex justify-between items-start mb-2 gap-2">
                                        <div>
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-xs font-bold text-gray-500 uppercase">Cuota {{ $cuota->numero_cuota }}</span>
                                                @if($cuota->pagada)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 whitespace-nowrap">
                                                        <i class="fas fa-check-circle mr-1"></i> Pagada
                                                    </span>
                                                @elseif($estaAtrasada)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 whitespace-nowrap">
                                                        <i class="fas fa-exclamation-triangle mr-1"></i> Atrasada
                                                    </span>
                                                @endif
                                            </div>
                                                        </div>
                                                        <div class="text-right">
                                                            <p class="text-sm font-bold {{ $cuota->pagada ? 'text-green-600' : 'text-gray-900' }}">
                                                                $ {{ number_format($cuota->monto, 2) }}
                                                            </p>
                                                        </div>
                                                    </div>

                                                    @if($cuota->monto_pagado > 0 && !$cuota->pagada)
                                                        <div class="text-xs text-orange-600">
                                                            Parcial: $ {{ number_format($cuota->monto_pagado, 2) }}
                                                        </div>
                                                    @endif

                                                    @if($cuota->pagada)
                                                        <div class="text-xs text-green-600 mb-2">
                                                            <i class="fas fa-check mr-1"></i> Pagado: {{ \Carbon\Carbon::parse($cuota->fecha_pago)->format('d/m/Y') }}
                                                        </div>
                                                        <a href="{{ route('cobrador.imprimir_recibo', $cuota->id) }}" target="_blank"
                                                            class="mt-1 w-full bg-green-600 hover:bg-green-700 text-white text-xs font-medium py-1.5 px-2 rounded transition flex items-center justify-center gap-1">
                                                            <i class="fas fa-file-pdf"></i> Ver Comprobante
                                                        </a>
                                                    @else
                                                        <button wire:click="abrirModalCobro({{ $cuota->id }}, {{ $montoRestante }})"
                                                            class="mt-2 w-full bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium py-1.5 px-2 rounded transition flex items-center justify-center gap-1">
                                                            <i class="fas fa-cash-register"></i> Cobrar
                                                        </button>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                            <div class="p-12 text-center">
                                <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-1">No se encontraron registros</h3>
                                <p class="text-gray-500">No hay cuotas que coincidan con los filtros aplicados.</p>
                            </div>
                        </div>
                    @endforelse

                    @if($agrupadasTotal > 20)
                        <div class="mt-6 flex justify-center">
                            <nav class="flex items-center gap-2">
                                @for($i = 1; $i <= ceil($agrupadasTotal / 20); $i++)
                                    <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}"
                                        class="px-3 py-1.5 rounded-lg text-sm font-medium transition {{ request('page', 1) == $i ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                                        {{ $i }}
                                    </a>
                                @endfor
                            </nav>
                        </div>
                    @endif
                </div>

            @else
                {{-- VISTA EN LISTA (anterior) --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-indigo-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-indigo-900 uppercase tracking-wider">Cuota</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-indigo-900 uppercase tracking-wider">Cliente</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-indigo-900 uppercase tracking-wider">Vehículo</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-indigo-900 uppercase tracking-wider">Monto</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-indigo-900 uppercase tracking-wider">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($cuotas as $cuota)
                                    @php
                                        $cliente = $cuota->legajoVehiculo->legajo->cliente;
                                        $vehiculo = $cuota->legajoVehiculo->vehiculo;
                                        $estaAtrasada = !$cuota->pagada && \Carbon\Carbon::parse($cuota->fecha_vencimiento)->isPast();
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition {{ $estaAtrasada ? 'bg-red-50' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold {{ $estaAtrasada ? 'text-red-600' : 'text-gray-900' }}">
                                                @if($estaAtrasada) <i class="fas fa-exclamation-circle mr-1"></i> @endif
                                                {{ \Carbon\Carbon::parse($cuota->fecha_vencimiento)->format('d/m/Y') }}
                                            </div>
                                            <div class="text-xs text-indigo-600 font-bold mt-1 bg-indigo-100 rounded inline-block px-2">
                                                Cuota {{ $cuota->numero_cuota }} / {{ $cuota->legajoVehiculo->cant_cuotas_casa }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-bold text-gray-900">{{ $cliente->apellido }}, {{ $cliente->nombre }}</div>
                                            <div class="text-xs text-gray-500">DNI: {{ $cliente->dni }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 font-bold">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</div>
                                            <div class="text-xs text-gray-500 font-mono">{{ $vehiculo->patente }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            @if($cuota->pagada)
                                                <div class="text-sm line-through text-gray-400">$ {{ number_format($cuota->monto, 2) }}</div>
                                                <div class="text-sm font-bold text-green-600">Pagó: $ {{ number_format($cuota->monto_pagado, 2) }}</div>
                                            @else
                                                <div class="text-lg font-bold text-gray-900">$ {{ number_format($cuota->monto - $cuota->monto_pagado, 2) }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if($cuota->pagada)
                                                <span class="bg-green-100 text-green-800 font-bold px-3 py-1 rounded-full text-xs uppercase">
                                                    <i class="fas fa-check-circle mr-1"></i> Cobrada
                                                </span>
                                                <a href="{{ route('cobrador.imprimir_recibo', $cuota->id) }}" target="_blank"
                                                    class="mt-2 block text-indigo-600 hover:text-indigo-900 text-xs">
                                                    <i class="fas fa-file-pdf mr-1"></i> Ver Comprobante
                                                </a>
                                            @else
                                                <button wire:click="abrirModalCobro({{ $cuota->id }}, {{ $cuota->monto - $cuota->monto_pagado }})"
                                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-sm shadow-sm transition">
                                                    Cobrar
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                            <i class="fas fa-file-invoice-dollar fa-3x mb-3 text-gray-300"></i><br>
                                            No se encontraron cuotas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 bg-gray-50 border-t">
                        {{ $cuotas->links() }}
                    </div>
                </div>
            @endif

            {{-- Modal de Cobro --}}
            @if($isOpenModalCobro && $cuota_seleccionada)
                <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="$set('isOpenModalCobro', false)"></div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">

                            <form wire:submit.prevent="procesarCobro">
                                <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 px-6 py-4">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg leading-6 font-bold text-white">
                                            <i class="fas fa-cash-register mr-2"></i> Cobrar Cuota
                                        </h3>
                                        <button type="button" wire:click="$set('isOpenModalCobro', false)" class="text-white/70 hover:text-white">
                                            <i class="fas fa-times text-xl"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="px-6 py-5">
                                    {{-- Info de la operación --}}
                                    <div class="bg-gray-50 rounded-lg p-4 mb-5">
                                        <div class="flex items-center gap-3 mb-3">
                                            <div class="bg-indigo-100 p-2 rounded-lg">
                                                <i class="fas fa-car text-indigo-600"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-900">{{ $cuota_seleccionada->legajoVehiculo->vehiculo->marca }} {{ $cuota_seleccionada->legajoVehiculo->vehiculo->modelo }}</p>
                                                <p class="text-sm text-gray-500 font-mono">{{ $cuota_seleccionada->legajoVehiculo->vehiculo->patente }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="bg-indigo-100 p-2 rounded-lg">
                                                <i class="fas fa-user text-indigo-600"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-900">{{ $cuota_seleccionada->legajoVehiculo->legajo->cliente->apellido }}, {{ $cuota_seleccionada->legajoVehiculo->legajo->cliente->nombre }}</p>
                                                <p class="text-sm text-gray-500">DNI: {{ $cuota_seleccionada->legajoVehiculo->legajo->cliente->dni }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-1">Monto a Cobrar ($)</label>
                                            <input type="number" step="0.01" wire:model="monto_a_cobrar"
                                                class="w-full text-lg font-bold border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            @if($cuota_seleccionada)
                                                <p class="text-xs text-gray-500 mt-1">
                                                    Cuota: ${{ number_format($cuota_seleccionada->monto, 2) }} |
                                                    Ya pagado: ${{ number_format($cuota_seleccionada->monto_pagado, 2) }} |
                                                    Falta: ${{ number_format(max(0, $cuota_seleccionada->monto - $cuota_seleccionada->monto_pagado), 2) }}
                                                </p>
                                            @endif
                                        </div>

                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-1">Interés por Mora ($)</label>
                                            <input type="number" step="0.01" wire:model="interes_mora"
                                                class="w-full text-lg font-bold border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-1">Método de Pago</label>
                                            <select wire:model="metodo_pago"
                                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="efectivo">💵 Efectivo</option>
                                                <option value="transferencia">🏦 Transferencia</option>
                                                <option value="debito_credito">💳 Tarjeta Débito/Crédito</option>
                                                <option value="cheque">📝 Cheque</option>
                                            </select>
                                        </div>

                                        {{-- Opción de Pago Diferido --}}
                                        <div class="flex items-center bg-blue-50 p-3 rounded-lg border-2 border-blue-200">
                                            <input type="checkbox" wire:model.live="habilitar_segundo_pago" id="habilitar_segundo_pago"
                                                class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                            <label for="habilitar_segundo_pago" class="ml-2 block text-sm text-gray-700 font-medium">
                                                <i class="fas fa-split mr-1"></i> Pago Dividido (Diferido)
                                            </label>
                                        </div>

                                        @if($habilitar_segundo_pago)
                                            <div class="bg-blue-50 p-4 rounded-lg border-2 border-blue-200 space-y-3">
                                                <p class="text-sm font-bold text-blue-900">Segundo Método de Pago</p>
                                                <div>
                                                    <label class="block text-sm font-bold text-gray-700 mb-1">Monto Segundo Pago ($)</label>
                                                    <input type="number" step="0.01" wire:model="monto_segundo_pago"
                                                        class="w-full text-lg font-bold border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                    @if($monto_a_cobrar && $monto_segundo_pago)
                                                        <p class="text-xs text-blue-600 mt-1">
                                                            <i class="fas fa-calculator mr-1"></i>
                                                            Total: ${{ number_format(floatval($monto_a_cobrar) + floatval($monto_segundo_pago), 2) }}
                                                        </p>
                                                    @endif
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-bold text-gray-700 mb-1">Método de Pago</label>
                                                    <select wire:model="metodo_pago_segundo"
                                                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                        <option value="efectivo">💵 Efectivo</option>
                                                        <option value="transferencia">🏦 Transferencia</option>
                                                        <option value="debito_credito">💳 Tarjeta Débito/Crédito</option>
                                                        <option value="cheque">📝 Cheque</option>
                                                    </select>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="flex items-center bg-yellow-50 p-3 rounded-lg">
                                            <input type="checkbox" wire:model="es_pagada_total" id="es_pagada_total"
                                                class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                                {{ $habilitar_segundo_pago ? 'disabled' : '' }}>
                                            <label for="es_pagada_total" class="ml-2 block text-sm text-gray-700 font-medium">
                                                <i class="fas fa-check-double mr-1"></i> Cuota Completamente Pagada
                                            </label>
                                            @if($habilitar_segundo_pago)
                                                <span class="ml-auto text-xs text-gray-500">(Desmarcar "Pago Dividido" para habilitar)</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-gray-50 px-6 py-4 flex gap-3">
                                    <button type="submit"
                                        class="flex-1 justify-center inline-flex items-center px-4 py-3 bg-indigo-600 border border-transparent rounded-lg font-bold text-white hover:bg-indigo-700 focus:outline-none transition">
                                        <i class="fas fa-check-circle mr-2"></i> Confirmar Cobro
                                    </button>
                                    <button type="button" wire:click="$set('isOpenModalCobro', false)"
                                        class="px-4 py-3 bg-white border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition">
                                        Cancelar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
