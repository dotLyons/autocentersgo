<div>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('crm.index') }}" wire:navigate class="text-gray-500 hover:text-indigo-600 transition">
                <svg class="w-6 h-6 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Legajo interactivo: ') }} {{ $cliente->apellido }}, {{ $cliente->nombre }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Resumen del Cliente --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 flex flex-col md:flex-row justify-between items-start md:items-center border-l-4 border-indigo-500">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $cliente->nombre }} {{ $cliente->apellido }}</h3>
                    <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                        <div><i class="fas fa-id-card text-gray-400"></i> DNI: <strong>{{ $cliente->dni }}</strong></div>
                        <div><i class="fas fa-phone text-gray-400"></i> {{ $cliente->celular }}</div>
                        @if($cliente->email) <div><i class="fas fa-envelope text-gray-400"></i> {{ $cliente->email }}</div> @endif
                    </div>
                </div>
                <div class="mt-4 md:mt-0 text-right">
                    <span class="inline-flex items-center rounded-md px-3 py-1 text-sm font-medium ring-1 ring-inset {{ $cliente->tipo_cliente->value === 'vendedor' ? 'bg-blue-50 text-blue-700 ring-blue-700/10' : ($cliente->tipo_cliente->value === 'comprador' ? 'bg-green-50 text-green-700 ring-green-600/20' : 'bg-purple-50 text-purple-700 ring-purple-700/10') }}">
                        CLIENTE {{ strtoupper($cliente->tipo_cliente->value) }}
                    </span>
                    <div class="mt-2">
                        <a href="{{ route('crm.edit', $cliente->id) }}" wire:navigate class="text-indigo-600 hover:text-indigo-900 text-sm font-bold underline">Modificar datos personales</a>
                    </div>
                </div>
            </div>

            {{-- Iterar sobre los Legajos del Cliente --}}
            @forelse($cliente->legajos as $legajo)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b {{ $legajo->tipo_legajo->value === 'vendedor' ? 'bg-blue-50 border-blue-200' : 'bg-green-50 border-green-200' }}">
                        <h4 class="font-bold text-lg {{ $legajo->tipo_legajo->value === 'vendedor' ? 'text-blue-800' : 'text-green-800' }}">
                            <i class="fas {{ $legajo->tipo_legajo->value === 'vendedor' ? 'fa-handshake' : 'fa-shopping-cart' }} mr-2"></i>
                            Legajo de {{ ucfirst($legajo->tipo_legajo->value) }}
                        </h4>
                    </div>

                    <div class="p-6">
                        @forelse($legajo->vehiculos as $LV)
                            <div class="mb-6 p-4 rounded-lg shadow-sm border {{ $loop->last ? '' : 'border-b-2 bg-gray-50' }}">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h5 class="text-lg font-bold text-gray-800">
                                            @if($LV->vehiculo)
                                                {{ $LV->vehiculo->marca }} {{ $LV->vehiculo->modelo }} (Patente: <span class="uppercase">{{ $LV->vehiculo->patente }}</span>)
                                            @else
                                                <span class="text-red-500">Vehículo no encontrado</span>
                                            @endif
                                        </h5>
                                        <p class="text-xs text-gray-400 mt-1">Registrado el: {{ $LV->created_at->format('d/m/Y') }}</p>
                                    </div>
                                    @if($legajo->tipo_legajo->value === 'vendedor')
                                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $LV->esta_vendido ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ $LV->esta_vendido ? 'VENDIDO' : 'EN ESPERA (Stock)' }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Detalle Vendedor --}}
                                @if($legajo->tipo_legajo->value === 'vendedor')
                                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 text-sm mt-4">
                                        <div>
                                            <span class="block text-gray-500 text-xs uppercase tracking-wide">Acordado con el Cliente</span>
                                            <span class="font-bold text-gray-800">$ {{ number_format($LV->precio_acordado, 2) }}</span>
                                        </div>
                                        <div>
                                            <span class="block text-gray-500 text-xs uppercase tracking-wide">Cargo/Comisión Agencia</span>
                                            <span class="font-bold text-gray-800">$ {{ number_format($LV->cargo_concesionaria, 2) }}</span>
                                        </div>
                                        @if($LV->esta_vendido)
                                            <div class="col-span-2 text-right">
                                                <span class="block text-gray-500 text-xs uppercase tracking-wide">Método Pago Entrega</span>
                                                <span class="font-bold text-indigo-600 uppercase">{{ $LV->metodo_pago_venta ?? 'No def.' }}</span>
                                            </div>
                                        @endif
                                    </div>

                                {{-- Detalle Comprador --}}
                                @elseif($legajo->tipo_legajo->value === 'comprador')
                                    <div class="bg-white border rounded p-4">
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4 border-b pb-4">
                                            <div>
                                                <span class="block text-gray-500 text-xs uppercase tracking-wide">Precio Cierre Operación</span>
                                                <span class="font-bold text-green-600 text-lg">$ {{ number_format($LV->precio_compra, 2) }}</span>
                                            </div>
                                            <div>
                                                <span class="block text-gray-500 text-xs uppercase tracking-wide">Efectivo Aportado</span>
                                                <span class="font-bold text-gray-800">$ {{ number_format($LV->monto_efectivo, 2) }}</span>
                                            </div>
                                            <div>
                                                <span class="block text-gray-500 text-xs uppercase tracking-wide">Transferencias Aportadas</span>
                                                <span class="font-bold text-gray-800">$ {{ number_format($LV->monto_transferencia, 2) }}</span>
                                            </div>
                                            <div>
                                                <span class="block text-gray-500 text-xs uppercase tracking-wide">Otras Entregas/Cheques</span>
                                                <span class="font-bold text-gray-800">$ {{ number_format($LV->monto_entrega, 2) }}</span>
                                            </div>
                                        </div>
                                        
                                        @if($LV->vehiculoEntregado)
                                            <div class="bg-gray-50 rounded p-3 mb-4 text-sm flex justify-between items-center border border-gray-200">
                                                <div>
                                                    <span class="font-bold text-gray-700">Vehículo entregado en parte de pago:</span>
                                                    <span class="text-blue-600">{{ $LV->vehiculoEntregado->marca }} {{ $LV->vehiculoEntregado->modelo }}</span>
                                                </div>
                                                <div class="text-right">
                                                    <span class="text-xs text-gray-500 uppercase tracking-wide block">Tasación</span>
                                                    <span class="font-bold text-gray-800">$ {{ number_format($LV->valor_vehiculo_entregado, 2) }}</span>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            @if($LV->financiacion_banco > 0)
                                            <div class="bg-blue-50 border border-blue-200 rounded p-3">
                                                <p class="text-sm font-bold text-blue-800 mb-1">Prendario / Banco</p>
                                                <p class="text-lg font-bold text-blue-900">$ {{ number_format($LV->financiacion_banco, 2) }}</p>
                                            </div>
                                            @endif

                                            @if($LV->financiacion_casa > 0)
                                            <div class="bg-indigo-50 border border-indigo-200 rounded p-3">
                                                <div class="flex justify-between items-center mb-1">
                                                    <p class="text-sm font-bold text-indigo-800">Crédito de Casa</p>
                                                    <span class="bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded-full font-bold">
                                                        $ {{ number_format($LV->total_pagado_casa, 2) }} Pagados de $ {{ number_format($LV->financiacion_casa, 2) }}
                                                    </span>
                                                </div>
                                                <p class="text-xl font-bold text-indigo-900 mb-2">$ {{ number_format($LV->financiacion_casa, 2) }}</p>
                                                <p class="text-sm text-indigo-700">Plan: {{ $LV->cant_cuotas_casa }} cuotas de $ {{ number_format($LV->monto_cuota_casa, 2) }}</p>

                                                @if($LV->cuotasCreditoCasa->count() > 0)
                                                    <div class="mt-3">
                                                        <details class="text-sm border-t border-indigo-200 pt-2">
                                                            <summary class="cursor-pointer text-indigo-600 font-bold hover:text-indigo-800">Ver Estado de Cuotas</summary>
                                                            <ul class="mt-2 space-y-1">
                                                                @foreach($LV->cuotasCreditoCasa->sortBy('numero_cuota') as $cuota)
                                                                    <li class="flex justify-between p-2 rounded {{ $cuota->esta_pagada ? 'bg-green-100 text-green-800' : 'bg-white border text-gray-600' }}">
                                                                        <span>Cuota #{{ $cuota->numero_cuota }} - Vence: {{ \Carbon\Carbon::parse($cuota->fecha_vencimiento)->format('d/m/Y') }}</span>
                                                                        <span class="font-bold">
                                                                            @if($cuota->esta_pagada) Pagada @else Pendiente @endif
                                                                        </span>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </details>
                                                    </div>
                                                @endif

                                            </div>
                                            @endif
                                            @if($LV->saldo_entrega_pendiente > 0 || $LV->pagosEntrega->count() > 0)
                                                <div class="col-span-1 md:col-span-2 bg-yellow-50 border border-yellow-200 rounded p-4 mt-2">
                                                    <div class="flex justify-between items-center mb-2">
                                                        <p class="text-sm font-bold text-yellow-800"><i class="fas fa-exclamation-triangle mr-1"></i> Saldo Pendiente de Entrega</p>
                                                        @if($LV->saldo_entrega_pendiente > 0)
                                                            <button wire:click="abrirModalPago({{ $LV->id }})" class="bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-bold py-1 px-3 rounded shadow transition">
                                                                + Ingresar Pago
                                                            </button>
                                                        @else
                                                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full font-bold">SALDADO</span>
                                                        @endif
                                                    </div>
                                                    <p class="text-2xl font-bold text-yellow-900 mb-2">$ {{ number_format($LV->saldo_entrega_pendiente, 2) }}</p>
                                                    <p class="text-xs text-yellow-700">Restante para completar la entrega pactada inicialmente.</p>
                                                    
                                                    @if($LV->pagosEntrega->count() > 0)
                                                        <details class="text-sm border-t border-yellow-200 mt-3 pt-2">
                                                            <summary class="cursor-pointer text-yellow-800 font-bold hover:text-yellow-900">Historial de Pagos de Entrega</summary>
                                                            <ul class="mt-2 space-y-1">
                                                                @foreach($LV->pagosEntrega->sortByDesc('fecha_pago') as $pago)
                                                                    <li class="flex justify-between p-2 rounded bg-white border text-gray-700 text-xs shadow-sm">
                                                                        <span><i class="fas fa-calendar-day text-gray-400 mr-1"></i> {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }} • Método: {{ ucfirst($pago->metodo_pago) }}</span>
                                                                        <span class="font-bold text-green-600">+ $ {{ number_format($pago->monto, 2) }}</span>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </details>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                            </div>
                        @empty
                            <p class="text-gray-500 italic text-sm text-center py-4">No hay vehículos asociados a este legajo.</p>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-10 text-center">
                    <i class="fas fa-folder-open fa-3x text-gray-300 mb-4"></i>
                    <p class="text-gray-500 font-medium text-lg">Este cliente aún no registra ningún legajo de compra o venta en el sistema.</p>
                </div>
            @endforelse

            {{-- Modal Pago de Entrega --}}
            @if($isOpenModalPagoEntrega)
                <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="$set('isOpenModalPagoEntrega', false)"></div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <form wire:submit.prevent="guardarPagoEntrega">
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                                        Registrar Pago a Cuenta Corriente (Entrega)
                                    </h3>
                                    
                                    @if (session()->has('error'))
                                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-2 mb-4 text-sm" role="alert">
                                            {{ session('error') }}
                                        </div>
                                    @endif

                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Monto Aportado Hoy ($)</label>
                                            <input type="number" step="0.01" wire:model="pago_entrega_monto" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                                            @error('pago_entrega_monto') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Método de Pago</label>
                                            <select wire:model="pago_entrega_metodo" class="mt-1 block w-full pl-3 pr-10 py-2 border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                                                <option value="efectivo">Efectivo</option>
                                                <option value="transferencia">Transferencia Bancaria</option>
                                                <option value="cheque">Cheque</option>
                                            </select>
                                            @error('pago_entrega_metodo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-yellow-600 text-base font-medium text-white hover:bg-yellow-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                        Guardar Pago
                                    </button>
                                    <button type="button" wire:click="$set('isOpenModalPagoEntrega', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
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
