<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ventas y Operaciones') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                
                {{-- Header y Buscador --}}
                <div class="p-6 bg-white border-b border-gray-200 flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                    <div class="w-full md:w-1/3">
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input wire:model.live="search" type="text" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" placeholder="Buscar por cliente o patente...">
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                        <a href="{{ route('ventas.create') }}" wire:navigate class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Registrar Nueva Venta
                        </a>
                    </div>
                </div>

                {{-- Tabla de Ventas (Listado) --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha / Operación #</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente (Comprador)</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehículo Entregado</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Cierre</th>
                                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Acciones</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($ventas as $venta)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900">{{ $venta->created_at->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-400">ID Ref: #{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}</div>
                                    </td>
                                    
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-700 font-bold">
                                                {{ substr($venta->legajo->cliente->nombre, 0, 1) }}{{ substr($venta->legajo->cliente->apellido, 0, 1) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $venta->legajo->cliente->nombre }} {{ $venta->legajo->cliente->apellido }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    DNI: {{ $venta->legajo->cliente->dni }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4">
                                        @if($venta->vehiculo)
                                            <div class="text-sm font-bold text-gray-900">{{ $venta->vehiculo->marca }} {{ $venta->vehiculo->modelo }}</div>
                                            <div class="mt-1 flex items-center space-x-2">
                                                <span class="text-xs text-gray-500 uppercase font-mono ring-1 ring-inset ring-gray-200 bg-gray-50 px-2 py-0.5 rounded">
                                                    {{ $venta->vehiculo->patente }}
                                                </span>
                                                <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-bold ring-1 ring-inset {{ $venta->vehiculo->categoria_propiedad->value === 'propio' ? 'bg-green-50 text-green-700 ring-green-600/20' : 'bg-purple-50 text-purple-700 ring-purple-600/20' }}">
                                                    {{ strtoupper($venta->vehiculo->categoria_propiedad->value) }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-red-500 text-sm">No Encontrado</span>
                                        @endif
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-green-700 font-bold bg-green-50 px-3 py-1 rounded inline-block">
                                            $ {{ number_format($venta->precio_compra, 2) }}
                                        </div>
                                        @if($venta->financiacion_casa > 0)
                                            <div class="text-xs text-purple-600 font-bold mt-1">
                                                <i class="fas fa-hand-holding-usd"></i> Financiado (Casa)
                                            </div>
                                        @endif
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('crm.show', $venta->legajo->cliente->id) }}" wire:navigate class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded font-bold transition">
                                            Ver Resumen en Perfil
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                        <i class="fas fa-car-side fa-3x mb-3 text-gray-300"></i><br>
                                        No se encontraron ventas registradas en el sistema.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="p-4 bg-gray-50 border-t border-gray-200">
                    {{ $ventas->links() }}
                </div>
                
            </div>
        </div>
    </div>
</div>
