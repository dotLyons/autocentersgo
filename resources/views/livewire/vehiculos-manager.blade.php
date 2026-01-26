<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Vehículos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                {{-- Mensajes de éxito --}}
                @if (session()->has('message'))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition.duration.500ms
                        class="bg-green-100 border-t-4 border-green-500 rounded-b text-green-900 px-4 py-3 shadow-md mb-4"
                        role="alert">
                        <div class="flex">
                            <div>
                                <p class="text-sm">{{ session('message') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Controles --}}
                <div class="flex flex-col md:flex-row justify-between items-center mb-4 space-y-4 md:space-y-0">
                    <div class="flex w-full md:w-1/2 space-x-2">
                        <select wire:model.live="searchField"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                            <option value="patent">Patente</option>
                            <option value="brand">Marca</option>
                            <option value="model">Modelo</option>
                            <option value="color">Color</option>
                        </select>
                        <input type="text" wire:model.live="search" placeholder="Buscar..."
                            class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    </div>

                    <button wire:click="create()"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded my-3">
                        Nuevo Vehículo
                    </button>
                </div>

                {{-- Tabla --}}
                <div class="overflow-x-auto">
                    <table class="table-auto w-full border-collapse border border-gray-200">
                        <thead>
                            <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                                <th class="py-3 px-6 text-left">Marca / Modelo</th>
                                <th class="py-3 px-6 text-left">Patente</th>
                                <th class="py-3 px-6 text-center">Año</th>
                                <th class="py-3 px-6 text-center">Precio</th>
                                <th class="py-3 px-6 text-center">Estado</th>
                                <th class="py-3 px-6 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            @forelse($vehiculos as $vehiculo)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-3 px-6 text-left whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="font-medium">{{ $vehiculo->brand }}
                                                {{ $vehiculo->model }}</span>
                                        </div>
                                        <div class="text-xs text-gray-400">{{ $vehiculo->color }}</div>
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        <span
                                            class="bg-gray-200 text-gray-700 py-1 px-3 rounded-full text-xs font-bold">{{ strtoupper($vehiculo->patent) }}</span>
                                    </td>
                                    <td class="py-3 px-6 text-center">{{ $vehiculo->model_year }}</td>
                                    <td class="py-3 px-6 text-center font-bold text-green-600">
                                        ${{ number_format($vehiculo->price, 2) }}</td>
                                    <td class="py-3 px-6 text-center">
                                        <span
                                            class="{{ $vehiculo->is_available ? 'bg-green-200 text-green-600' : 'bg-red-200 text-red-600' }} py-1 px-3 rounded-full text-xs">
                                            {{ $vehiculo->is_available ? 'Disponible' : 'Vendido/Ocupado' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex item-center justify-center space-x-2">
                                            {{-- BOTÓN VER DETALLES --}}
                                            <button wire:click="view({{ $vehiculo->id }})"
                                                class="w-4 mr-2 transform hover:text-blue-500 hover:scale-110"
                                                title="Ver Detalles">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                </svg>
                                            </button>

                                            {{-- BOTÓN EDITAR --}}
                                            <button wire:click="edit({{ $vehiculo->id }})"
                                                class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110"
                                                title="Editar">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>

                                            {{-- BOTÓN ELIMINAR --}}
                                            <button wire:click="delete({{ $vehiculo->id }})"
                                                wire:confirm="¿Estás seguro de eliminar este vehículo?"
                                                class="w-4 mr-2 transform hover:text-red-500 hover:scale-110"
                                                title="Eliminar">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">No hay vehículos registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                <div class="mt-4">
                    {{ $vehiculos->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    @if ($isOpen)
        <div class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

                <div class="fixed inset-0 transition-opacity">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full"
                    role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-headline">
                            @if ($isViewMode)
                                Detalles del Vehículo
                            @else
                                {{ $vehiculo_id ? 'Editar Vehículo' : 'Crear Vehículo' }}
                            @endif
                        </h3>
                        <form>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Marca:</label>
                                    <input type="text" wire:model="brand" {{ $isViewMode ? 'disabled' : '' }}
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline disabled:bg-gray-100 disabled:text-gray-500">
                                    @error('brand')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Modelo:</label>
                                    <input type="text" wire:model="model" {{ $isViewMode ? 'disabled' : '' }}
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline disabled:bg-gray-100 disabled:text-gray-500">
                                    @error('model')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Patente (Unique):</label>
                                <input type="text" wire:model="patent" {{ $isViewMode ? 'disabled' : '' }}
                                    class="uppercase shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline disabled:bg-gray-100 disabled:text-gray-500">
                                @error('patent')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Año:</label>
                                    <input type="number" wire:model="model_year" {{ $isViewMode ? 'disabled' : '' }}
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline disabled:bg-gray-100 disabled:text-gray-500">
                                    @error('model_year')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Color:</label>
                                    <input type="text" wire:model="color" {{ $isViewMode ? 'disabled' : '' }}
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline disabled:bg-gray-100 disabled:text-gray-500">
                                    @error('color')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Precio:</label>
                                <input type="number" step="0.01" wire:model="price"
                                    {{ $isViewMode ? 'disabled' : '' }}
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline disabled:bg-gray-100 disabled:text-gray-500">
                                @error('price')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Detalles:</label>
                                <textarea wire:model="details" {{ $isViewMode ? 'disabled' : '' }}
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline disabled:bg-gray-100 disabled:text-gray-500"></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" wire:model="is_available"
                                        {{ $isViewMode ? 'disabled' : '' }}
                                        class="form-checkbox h-5 w-5 text-indigo-600 disabled:opacity-50">
                                    <span class="ml-2 text-gray-700">Disponible para la venta</span>
                                </label>
                            </div>
                        </form>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        @if (!$isViewMode)
                            <button wire:click.prevent="store()" type="button"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Guardar
                            </button>
                        @endif

                        <button wire:click="closeModal()" type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            {{ $isViewMode ? 'Cerrar' : 'Cancelar' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
