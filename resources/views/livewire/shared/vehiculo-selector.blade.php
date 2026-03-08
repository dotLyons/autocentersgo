<div>
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <div class="bg-green-600 px-4 py-3 sm:px-6 flex justify-between items-center">
                        <h3 class="text-lg leading-6 font-bold text-white" id="modal-title">
                            <i class="fas fa-car mr-2"></i>Seleccionar Vehículo (Solo disponibles)
                        </h3>
                        <button wire:click="closeModal" class="text-white hover:text-gray-200 focus:outline-none">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <div class="px-4 py-4 sm:px-6 border-b">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                                <input type="text" wire:model.live.debounce.300ms="search" 
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                                    placeholder="Escriba Patente, Marca o Modelo...">
                            </div>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto max-h-96">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marca</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modelo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Año</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Color</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Venta</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($vehiculos as $vehiculo)
                                    <tr class="hover:bg-green-50 cursor-pointer transition" wire:click="selectVehiculo({{ $vehiculo->id }})">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-bold">{{ $vehiculo->patente }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $vehiculo->marca }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $vehiculo->modelo }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $vehiculo->anio }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $vehiculo->color }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-green-600">
                                            $ {{ number_format($vehiculo->precio_venta_publico, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <button class="text-green-600 hover:text-green-900 font-medium text-sm">
                                                <i class="fas fa-check-circle"></i> Seleccionar
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                            <i class="fas fa-car-slash text-4xl mb-2"></i>
                                            <p>No se encontraron vehículos disponibles</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($vehiculos->hasPages())
                        <div class="px-4 py-3 bg-gray-50 border-t">
                            {{ $vehiculos->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
