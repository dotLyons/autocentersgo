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
                                <button type="button" wire:click="openClienteSelector" class="w-full bg-white border-2 border-dashed border-gray-300 rounded-lg p-4 text-left hover:border-indigo-500 hover:bg-indigo-50 transition">
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-500"><i class="fas fa-search mr-2"></i>Buscar cliente...</span>
                                        <span class="text-indigo-600 font-medium">Click para abrir</span>
                                    </div>
                                </button>
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
                                <button type="button" wire:click="openVehiculoSelector" class="w-full bg-white border-2 border-dashed border-gray-300 rounded-lg p-4 text-left hover:border-green-500 hover:bg-green-50 transition">
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-500"><i class="fas fa-search mr-2"></i>Buscar vehículo...</span>
                                        <span class="text-green-600 font-medium">Click para abrir</span>
                                    </div>
                                </button>
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
                                        
                                        <div class="bg-indigo-50 p-4 border border-indigo-200 rounded shadow-inner relative">
                                            <label class="block text-sm font-bold text-indigo-900 mb-1">Crédito Proporcionado por DE LA CASA ($)</label>
                                            <div class="flex items-center space-x-2 mb-3">
                                                <input type="number" step="0.01" wire:model.live="financiacion_casa" class="w-full border-indigo-300 rounded focus:ring-indigo-500 bg-white">
                                                @if($this->diferencia > 0 && $this->financiacion_casa == 0)
                                                    <button type="button" wire:click="$set('financiacion_casa', {{ $this->diferencia }})" class="whitespace-nowrap bg-indigo-600 text-white px-3 py-2 rounded text-xs font-bold hover:bg-indigo-700 shadow-sm transition">
                                                        <i class="fas fa-magic"></i> Auto Financiar
                                                    </button>
                                                @endif
                                            </div>
                                            
                                            <div class="grid grid-cols-2 gap-4 mt-2 border-t border-indigo-200 pt-3">
                                                <div>
                                                    <label class="block text-xs font-bold text-indigo-700 mb-1" title="Del 1 al 48">Cant. Cuotas (1-48):</label>
                                                    <input type="number" wire:model.live="cant_cuotas_casa" min="0" max="48" class="w-full border-indigo-300 rounded focus:ring-indigo-500 bg-white text-sm" placeholder="Ej: 12">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-bold text-indigo-700 mb-1">Monto de la Cuota ($):</label>
                                                    <input type="number" wire:model.live="monto_cuota_casa" step="0.01" class="w-full border-indigo-300 rounded focus:ring-indigo-500 bg-white text-sm font-bold text-indigo-900" placeholder="0.00">
                                                </div>
                                            </div>
                                            <div class="text-[10px] text-gray-500 mt-2">Puede modificar manualmente el monto de la cuota si incluye interés u otros gastos en la financiación.</div>
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
                                        $cumpleEntregaMinima = $this->aportes_iniciales >= $this->monto_entrega_requerido;
                                    @endphp

                                    <div class="mt-6 p-4 rounded-lg {{ $cumpleEntregaMinima ? 'bg-green-800 border border-green-600' : 'bg-yellow-800 border border-yellow-600' }}">
                                        <div class="text-xs uppercase tracking-widest font-bold mb-1 {{ $cumpleEntregaMinima ? 'text-green-300' : 'text-yellow-300' }}">
                                            Estado de la Operación
                                        </div>
                                        <div class="text-lg font-bold">
                                            @if($cumpleEntregaMinima)
                                                <i class="fas fa-check-circle mr-1"></i> Entrega Mínima CUMPLIDA
                                                @if($diferencia > 0)
                                                    <div class="text-sm font-normal mt-1 text-yellow-200">
                                                        <i class="fas fa-clock mr-1"></i> Saldo Pendiente: $ {{ number_format($diferencia, 2) }}
                                                    </div>
                                                @endif
                                            @else
                                                <i class="fas fa-exclamation-triangle mr-1"></i> Falta Entrega Mínima
                                                <div class="text-sm font-normal mt-1">
                                                    Debe entregar al menos: $ {{ number_format($this->monto_entrega_requerido, 2) }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-8 relative z-10">
                                    <button type="submit" 
                                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-lg font-bold text-white relative transition {{ $this->cliente_id && $this->vehiculo_comprado_id ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-gray-600 cursor-not-allowed border-dashed border-2 opacity-70' }}"
                                            @if(!$this->cliente_id || !$this->vehiculo_comprado_id) disabled @endif>
                                            <i class="fas fa-signature mr-2 mt-1"></i> EFECTUAR VENTA
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
            
            @livewire(\App\Livewire\Shared\ClienteSelector::class)
            @livewire(\App\Livewire\Shared\VehiculoSelector::class)
        </div>
    </div>
</div>
