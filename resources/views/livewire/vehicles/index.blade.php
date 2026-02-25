<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Inventario de Vehículos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                @if (session()->has('message'))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
                        class="bg-green-100 border-t-4 border-green-500 rounded-b text-green-900 px-4 py-3 shadow-md mb-4">
                        <p class="text-sm font-bold">{{ session('message') }}</p>
                    </div>
                @endif

                <div class="flex flex-col md:flex-row justify-between items-center mb-6 space-y-4 md:space-y-0 md:space-x-4">
                    <div class="w-full md:w-2/5 relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fas fa-search text-gray-400"></i>
                        </span>
                        <input type="text" wire:model.live="search" placeholder="Buscar por Patente, Marca o Modelo..."
                            class="pl-10 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                    </div>
                    
                    <div class="w-full md:w-1/4">
                        <select wire:model.live="filtroTipo" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                            <option value="">Todos los Tipos</option>
                            <option value="auto">Autos</option>
                            <option value="camioneta">Camionetas</option>
                            <option value="furgon">Furgones</option>
                            <option value="moto">Motos</option>
                        </select>
                    </div>

                    <div class="w-full md:w-1/4">
                        <select wire:model.live="filtroPropiedad" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                            <option value="">Toda Propiedad</option>
                            <option value="propio">Propios de la Agencia</option>
                            <option value="consignacion">En Consignación</option>
                        </select>
                    </div>

                    <a href="{{ route('vehicles.create') }}" wire:navigate
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded shadow transition duration-150 w-full md:w-auto text-center text-sm whitespace-nowrap">
                        + Registrar Vehículo
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($vehiculos as $vehiculo)
                        <div class="bg-white border rounded-lg shadow-sm hover:shadow-md transition-shadow relative overflow-hidden flex flex-col">
                            
                            {{-- Etiquetas Superiores --}}
                            <div class="absolute top-0 right-0 p-3 flex space-x-1">
                                <span class="px-2 py-1 text-xs font-bold rounded-md bg-gray-800 text-white uppercase shadow">
                                    {{ $vehiculo->tipo_vehiculo->value }}
                                </span>
                                @if($vehiculo->categoria_propiedad->value == 'propio')
                                    <span class="px-2 py-1 text-xs font-bold rounded-md bg-green-100 text-green-800 border border-green-200 uppercase shadow">Local</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-bold rounded-md bg-purple-100 text-purple-800 border border-purple-200 uppercase shadow">Consign.</span>
                                @endif
                            </div>

                            {{-- Cuerpo de Tarjeta --}}
                            <div class="p-5 flex-1 mt-6">
                                <h4 class="text-xl font-bold text-gray-900 group-hover:text-indigo-600 transition">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</h4>
                            <div class="flex items-center space-x-2 mt-1">
                                <p class="text-sm font-medium text-gray-500">{{ $vehiculo->version ?? 'Standard' }} • {{ $vehiculo->anio }}</p>
                                @if(!$vehiculo->disponible)
                                    <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-0.5 text-xs font-bold text-red-700 ring-1 ring-inset ring-red-600/10 uppercase">
                                        Vendido
                                    </span>
                                @endif
                            </div>
                                
                                <div class="grid grid-cols-2 gap-y-2 text-sm text-gray-600 mb-4 border-t border-b py-3">
                                    <div><i class="fas fa-barcode text-gray-400 w-5"></i> <span class="font-mono font-bold">{{ $vehiculo->patente ?? 'S/P' }}</span></div>
                                    <div><i class="fas fa-palette text-gray-400 w-5"></i> {{ $vehiculo->color ?? '-' }}</div>
                                    <div><i class="fas fa-cogs text-gray-400 w-5"></i> {{ ucfirst($vehiculo->tipo_caja?->value ?? 'No esp.') }}</div>
                                    <div><i class="fas fa-gas-pump text-gray-400 w-5"></i> {{ $vehiculo->tiene_gnc ? 'GNC Gen ' . $vehiculo->generacion_gnc : 'Nafta/Diesel' }}</div>
                                </div>

                                {{-- Precio según estado --}}
                                <div class="text-center">
                                    <p class="text-xs text-gray-500 uppercase tracking-widest">Precio de Venta</p>
                                    @if($vehiculo->precio_venta_publico)
                                        <p class="text-2xl font-bold text-green-600">$ {{ number_format($vehiculo->precio_venta_publico, 2) }}</p>
                                    @else
                                        <p class="text-xl font-bold text-yellow-500 py-1">A Definir (Tasación)</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Botonera Inferior --}}
                            <div class="bg-gray-50 px-4 py-3 border-t flex justify-between items-center">
                                <span class="text-xs text-gray-500 flex items-center" title="Formularios Cargados">
                                    <i class="fas fa-folder-open mr-1"></i> {{ $vehiculo->formularios->count() }} Forms
                                </span>
                                
                                <div class="flex space-x-2">
                                    <a href="{{ route('vehicles.show', $vehiculo->id) }}" wire:navigate class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 p-2 rounded-full transition" title="Ver Historial/Detalle">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    <a href="{{ route('vehicles.edit', $vehiculo->id) }}" wire:navigate class="text-amber-600 hover:text-amber-900 bg-amber-50 hover:bg-amber-100 p-2 rounded-full transition" title="Editar Ficha">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-1 md:col-span-2 lg:col-span-3 py-12 text-center text-gray-500">
                            <i class="fas fa-car fa-3x mb-4 text-gray-300"></i>
                            <p class="text-lg font-medium">No hay vehículos registrados en el catálogo.</p>
                            <p class="text-sm mt-1">Intenta ajustando los filtros o registra uno nuevo.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-8">
                    {{ $vehiculos->links() }}
                </div>

            </div>
        </div>
    </div>
</div>
