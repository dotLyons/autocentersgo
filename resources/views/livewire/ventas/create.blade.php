<div>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('ventas.index') }}" wire:navigate class="text-gray-500 hover:text-indigo-600 transition">
                <svg class="w-6 h-6 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Asistente de Venta (Nueva Operación)') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            @if (session()->has('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded shadow-sm" role="alert">
                    <p class="font-bold">Error en la transacción</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <form wire:submit.prevent="registrarVenta">
                
                {{-- PASO 1: Identificación de Partes --}}
                <div class="bg-white shadow sm:rounded-lg overflow-hidden border border-gray-200 mb-8">
                    <div class="px-5 py-4 bg-indigo-50 border-b border-indigo-200">
                        <h3 class="text-lg font-bold text-indigo-900"><i class="fas fa-users mr-2"></i> Paso 1: Partes de la Operación</h3>
                    </div>
                    
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                        
                        {{-- Seleccionador de Cliente --}}
                        <div class="bg-gray-50 p-4 rounded-lg border border-dashed border-gray-300">
                            <label class="block text-gray-700 font-bold mb-2">1. Seleccionar Cliente (Comprador)</label>
                            @if(!$clienteSeleccionado)
                                <input type="text" wire:model.live.debounce.300ms="searchCliente" class="w-full border-gray-300 shadow-sm rounded focus:ring-indigo-500" placeholder="Escriba DNI o Apellido para buscar...">
                                @if(count($clientesList) > 0)
                                    <ul class="bg-white border text-sm rounded mt-1 shadow-lg absolute z-10 w-full max-w-sm">
                                        @foreach($clientesList as $c)
                                            <li wire:click="seleccionarCliente({{ $c->id }})" class="p-2 border-b cursor-pointer hover:bg-indigo-50 hover:text-indigo-700 transition">
                                                <strong>{{ $c->apellido }}, {{ $c->nombre }}</strong> (DNI: {{ $c->dni }})
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                                <p class="text-xs text-gray-500 mt-2">¿No existe? <a href="{{ route('crm.create') }}" target="_blank" class="text-indigo-600 underline">Cárgalo en el CRM primero</a>.</p>
                            @else
                                <div class="bg-indigo-100 text-indigo-800 p-3 rounded flex justify-between items-center shadow-sm">
                                    <div>
                                        <div class="font-bold text-lg"><i class="fas fa-user-check mr-1"></i> {{ $clienteSeleccionado->apellido }}, {{ $clienteSeleccionado->nombre }}</div>
                                        <div class="text-sm">DNI: {{ $clienteSeleccionado->dni }}</div>
                                    </div>
                                    <button type="button" wire:click="$set('cliente_id', null)" class="text-indigo-500 hover:text-red-500 font-bold text-xl">&times;</button>
                                </div>
                            @endif
                            @error('cliente_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        {{-- Seleccionador de Vehículo a Vender --}}
                        <div class="bg-gray-50 p-4 rounded-lg border border-dashed border-gray-300 relative">
                            <label class="block text-gray-700 font-bold mb-2">2. Seleccionar Vehículo a Vender</label>
                            @if(!$vehiculoCompradoSeleccionado)
                                <input type="text" wire:model.live.debounce.300ms="searchVehiculo" class="w-full border-gray-300 shadow-sm rounded focus:ring-green-500" placeholder="Escriba Patente o Modelo...">
                                @if(count($vehiculosVenta) > 0)
                                    <ul class="bg-white border text-sm rounded mt-1 shadow-lg absolute z-10 w-full max-w-sm">
                                        @foreach($vehiculosVenta as $v)
                                            <li wire:click="seleccionarVehiculoComprado({{ $v->id }})" class="p-2 border-b cursor-pointer hover:bg-green-50 hover:text-green-700 transition">
                                                <span class="font-mono bg-gray-100 px-1 rounded mr-2">{{ $v->patente }}</span>
                                                <strong>{{ $v->marca }} {{ $v->modelo }}</strong>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            @else
                                <div class="bg-green-100 text-green-800 p-3 rounded flex justify-between items-center shadow-sm border border-green-200">
                                    <div>
                                        <div class="font-bold text-lg"><i class="fas fa-car mr-1"></i> {{ $vehiculoCompradoSeleccionado->marca }} {{ $vehiculoCompradoSeleccionado->modelo }}</div>
                                        <div class="text-sm font-mono mt-1 bg-white inline-block px-2 rounded border border-green-300">{{ $vehiculoCompradoSeleccionado->patente }}</div>
                                    </div>
                                    <button type="button" wire:click="$set('vehiculo_comprado_id', null)" class="text-green-600 hover:text-red-500 font-bold text-xl">&times;</button>
                                </div>
                            @endif
                            @error('vehiculo_comprado_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- PASO 2: Estructura Financiera --}}
                <div class="bg-white shadow sm:rounded-lg overflow-hidden border border-gray-200 mb-8">
                    <div class="px-5 py-4 bg-green-50 border-b border-green-200 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-green-900"><i class="fas fa-file-invoice-dollar mr-2"></i> Paso 2: Arreglo Económico (Cierre)</h3>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                            
                            {{-- Columna Izquierda: Aportes / Dinero entrante --}}
                            <div class="lg:col-span-8 space-y-6">
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="bg-gray-50 p-4 border rounded">
                                        <label class="block text-sm font-bold text-gray-700 mb-1"><i class="fas fa-money-bill-wave text-green-500 mr-1"></i> Entrega en Efectivo ($)</label>
                                        <input type="number" step="0.01" wire:model.live="monto_efectivo" class="w-full border-gray-300 rounded focus:ring-green-500 text-lg font-bold">
                                    </div>
                                    <div class="bg-gray-50 p-4 border rounded">
                                        <label class="block text-sm font-bold text-gray-700 mb-1"><i class="fas fa-university text-blue-500 mr-1"></i> Transferencias / Depésitos ($)</label>
                                        <input type="number" step="0.01" wire:model.live="monto_transferencia" class="w-full border-gray-300 rounded focus:ring-blue-500 text-lg font-bold">
                                    </div>
                                    <div class="bg-gray-50 p-4 border rounded relative">
                                        <label class="block text-sm font-bold text-gray-700 mb-1"><i class="fas fa-exchange-alt text-yellow-500 mr-1"></i> Vehículo en Parte de Pago</label>
                                        
                                        @if(!$vehiculoEntregadoSeleccionado)
                                            <input type="text" wire:model.live.debounce.300ms="searchVehiculoEntregado" class="w-full text-sm border-gray-300 shadow-sm rounded focus:ring-yellow-500 mb-2" placeholder="Buscar patente a entregar...">
                                            @if(count($vehiculosEntregados) > 0)
                                                <ul class="bg-white border text-sm rounded mt-1 shadow-lg absolute z-20 w-full mb-2">
                                                    @foreach($vehiculosEntregados as $v)
                                                        <li wire:click="seleccionarVehiculoEntregado({{ $v->id }})" class="p-2 border-b cursor-pointer hover:bg-yellow-50 hover:text-yellow-700 transition">
                                                            <span class="font-mono bg-gray-100 px-1 rounded mr-1">{{ $v->patente }}</span> {{ $v->marca }} {{ $v->modelo }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                            <div class="text-xs text-gray-500 mt-1">Debe estar cargado de antemano.</div>
                                        @else
                                            <div class="flex justify-between items-center text-sm bg-yellow-100 text-yellow-800 p-2 rounded border border-yellow-300 mb-2">
                                                <span>{{ $vehiculoEntregadoSeleccionado->marca }} {{ $vehiculoEntregadoSeleccionado->modelo }} ({{ $vehiculoEntregadoSeleccionado->patente }})</span>
                                                <button type="button" wire:click="removerVehiculoEntregado" class="text-red-500 hover:text-red-700 text-lg">&times;</button>
                                            </div>
                                            <input type="number" step="0.01" wire:model.live="valor_vehiculo_entregado" placeholder="Tasación $..." class="w-full border-gray-300 rounded focus:ring-yellow-500 font-bold">
                                        @endif
                                    </div>
                                    
                                    <div class="bg-gray-50 p-4 border rounded">
                                        <label class="block text-sm font-bold text-gray-700 mb-1"><i class="fas fa-money-check text-purple-500 mr-1"></i> Otros Medios (Cheques) ($)</label>
                                        <input type="number" step="0.01" wire:model.live="monto_entrega" class="w-full border-gray-300 rounded focus:ring-purple-500 text-lg font-bold">
                                    </div>
                                </div>

                                <div class="border-t pt-6">
                                    <h4 class="font-bold text-gray-800 mb-4"><i class="fas fa-file-contract text-indigo-500 mr-2"></i> Financiación Adicional y Cuotas</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="bg-blue-50 p-4 border border-blue-200 rounded">
                                            <label class="block text-sm font-bold text-blue-900 mb-1">Prendario / Banco Exterior ($)</label>
                                            <input type="number" step="0.01" wire:model.live="financiacion_banco" class="w-full border-blue-300 rounded focus:ring-blue-500 bg-white">
                                        </div>
                                        
                                        <div class="bg-indigo-50 p-4 border border-indigo-200 rounded shadow-inner">
                                            <label class="block text-sm font-bold text-indigo-900 mb-1">Crédito Proporcionado por DE LA CASA ($)</label>
                                            <input type="number" step="0.01" wire:model.live="financiacion_casa" class="w-full border-indigo-300 rounded focus:ring-indigo-500 mb-3 bg-white">
                                            
                                            <label class="block text-xs font-bold text-indigo-700 mb-1">Cantidad de Cuotas Fijas:</label>
                                            <select wire:model.live="cant_cuotas_casa" class="w-full border-indigo-300 rounded focus:ring-indigo-500 bg-white text-sm">
                                                <option value="0">Sin Financiación</option>
                                                <option value="3">3 Cuotas</option>
                                                <option value="6">6 Cuotas</option>
                                                <option value="12">12 Cuotas</option>
                                                <option value="18">18 Cuotas</option>
                                                <option value="24">24 Cuotas</option>
                                                <option value="36">36 Cuotas</option>
                                                <option value="48">48 Cuotas</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Columna Derecha: Balance General y Verificación --}}
                            <div class="lg:col-span-4 bg-gray-900 text-white rounded-lg p-6 shadow-2xl flex flex-col justify-between relative overflow-hidden">
                                <div class="absolute top-0 right-0 opacity-10 m-4">
                                    <i class="fas fa-calculator fa-6x"></i>
                                </div>
                                <div class="relative z-10">
                                    <h4 class="text-xl font-bold border-b border-gray-700 pb-2 mb-4 text-indigo-300">Resumen y Balance</h4>
                                    
                                    <div class="mb-4">
                                        <label class="block text-sm text-gray-400 font-bold mb-1 uppercase tracking-wider">Precio</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-gray-500 font-bold">$</span>
                                            <input type="number" step="0.01" wire:model.live="precio_compra" class="w-full bg-gray-800 border border-gray-700 text-white rounded pl-8 pr-3 py-2 text-xl font-bold focus:border-indigo-500 focus:ring-indigo-500">
                                            @error('precio_compra') <span class="text-red-400 text-xs block mt-1">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-3 pt-4 border-t border-gray-700 text-sm">
                                        <div class="flex justify-between items-center text-gray-300">
                                            <span><i class="fas fa-bullseye text-blue-400 mr-1"></i> Objetivo Entrega Requerida:</span>
                                            <span class="font-bold">$ {{ number_format($this->monto_entrega_requerido, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center text-gray-300">
                                            <span><i class="fas fa-hand-holding-usd text-green-400 mr-1"></i> Aporte Inicial (Subtotal):</span>
                                            <span class="font-bold border-b border-gray-600 pb-1">$ {{ number_format($this->aportes_iniciales, 2) }}</span>
                                        </div>
                                        @if($this->saldo_entrega_pendiente > 0)
                                            <div class="flex justify-between items-center text-yellow-300 bg-yellow-900 bg-opacity-30 p-2 rounded -mx-2">
                                                <span class="font-bold"><i class="fas fa-exclamation-triangle mr-1"></i> Saldo Entrega PENDIENTE:</span>
                                                <span class="font-bold">$ {{ number_format($this->saldo_entrega_pendiente, 2) }}</span>
                                            </div>
                                        @endif
                                        <div class="flex justify-between font-bold text-gray-400 pt-1">
                                            <span>Suma Financiada Adicional:</span>
                                            <span>$ {{ number_format((float)$this->financiacion_banco + (float)$this->financiacion_casa, 2) }}</span>
                                        </div>
                                        
                                        <div class="flex justify-between pt-2 border-t border-gray-700 mt-2">
                                            <span class="text-gray-400 uppercase tracking-wider font-bold">Total Calculado Cierre:</span>
                                            <span class="font-bold text-green-400 text-lg">$ {{ number_format($this->total_aportado, 2) }}</span>
                                        </div>
                                    </div>

                                    @php
                                        $diferencia = $this->diferencia;
                                    @endphp

                                    <div class="mt-6 p-4 rounded-lg {{ $diferencia == 0 ? 'bg-green-800 border border-green-600' : 'bg-red-900 border border-red-700' }}">
                                        <div class="text-xs uppercase tracking-widest font-bold mb-1 {{ $diferencia == 0 ? 'text-green-300' : 'text-red-300' }}">
                                            Evaluación de Balance
                                        </div>
                                        <div class="text-2xl font-bold">
                                            @if($diferencia == 0)
                                                <i class="fas fa-check-circle mr-1"></i> Balance Exacto
                                            @else
                                                Faltan/Sobran: <br>$ {{ number_format($diferencia, 2) }}
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-8 relative z-10">
                                    <button type="submit" 
                                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-lg font-bold text-white relative transition {{ $diferencia == 0 && $this->cliente_id && $this->vehiculo_comprado_id ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-gray-600 cursor-not-allowed border-dashed border-2 opacity-70' }}"
                                            @if($diferencia != 0 || !$this->cliente_id || !$this->vehiculo_comprado_id) disabled @endif>
                                            <i class="fas fa-signature mr-2 mt-1"></i> EFECTUAR VENTA
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
