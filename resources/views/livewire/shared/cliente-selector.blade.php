<div>
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <div class="bg-indigo-600 px-4 py-3 sm:px-6 flex justify-between items-center">
                        <h3 class="text-lg leading-6 font-bold text-white" id="modal-title">
                            <i class="fas fa-users mr-2"></i>Seleccionar Cliente
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
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Escriba DNI, Apellido o Nombre...">
                            </div>
                            <div class="sm:w-48">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Filtrar por Tipo</label>
                                <select wire:model="filtro" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="todos">Todos</option>
                                    <option value="comprador">Comprador</option>
                                    <option value="vendedor">Vendedor</option>
                                    <option value="ambos">Ambos</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto max-h-96">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DNI</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Apellido</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Celular</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($clientes as $cliente)
                                    <tr class="hover:bg-indigo-50 cursor-pointer transition" wire:click="selectCliente({{ $cliente->id }})">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono">{{ $cliente->dni }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $cliente->apellido }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $cliente->nombre }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $cliente->celular }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($cliente->tipo_cliente->value === 'comprador') bg-green-100 text-green-800
                                                @elseif($cliente->tipo_cliente->value === 'vendedor') bg-blue-100 text-blue-800
                                                @else bg-purple-100 text-purple-800 @endif">
                                                {{ ucfirst($cliente->tipo_cliente->value) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <button class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">
                                                <i class="fas fa-check-circle"></i> Seleccionar
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                            <i class="fas fa-user-slash text-4xl mb-2"></i>
                                            <p>No se encontraron clientes</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($clientes->hasPages())
                        <div class="px-4 py-3 bg-gray-50 border-t">
                            {{ $clientes->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
