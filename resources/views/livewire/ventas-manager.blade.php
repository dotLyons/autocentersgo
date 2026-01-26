<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Ventas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                @if (session()->has('message'))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
                        class="bg-green-100 border-t-4 border-green-500 rounded-b text-green-900 px-4 py-3 shadow-md mb-4">
                        <p class="text-sm">{{ session('message') }}</p>
                    </div>
                @endif

                <div class="flex justify-between items-center mb-4">
                    <input type="text" wire:model.live="search" placeholder="Buscar venta por cliente..."
                        class="w-1/2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <button wire:click="create()"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        Registrar Venta
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="table-auto w-full border-collapse border border-gray-200">
                        <thead>
                            <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                                <th class="py-3 px-6 text-left">Cliente</th>
                                <th class="py-3 px-6 text-left">Vehículo</th>
                                <th class="py-3 px-6 text-center">Precio Venta</th>
                                <th class="py-3 px-6 text-center">Plan</th>
                                <th class="py-3 px-6 text-center">Fecha Compra</th>
                                <th class="py-3 px-6 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            @forelse($ventas as $venta)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-3 px-6 text-left">
                                        <div class="font-bold">{{ $venta->cliente->apellido }},
                                            {{ $venta->cliente->nombre }}</div>
                                        <div class="text-xs text-gray-400">DNI: {{ $venta->cliente->dni }}</div>
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        <div>{{ $venta->vehiculo->brand }} {{ $venta->vehiculo->model }}</div>
                                        <div class="text-xs text-gray-400 font-mono">{{ $venta->vehiculo->patent }}
                                        </div>
                                    </td>
                                    <td class="py-3 px-6 text-center font-bold text-green-700">
                                        ${{ number_format($venta->precio_venta, 2) }}
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <span class="bg-blue-100 text-blue-600 py-1 px-3 rounded-full text-xs">
                                            {{ $venta->cantidad_cuotas }} x
                                            ${{ number_format($venta->monto_cuota, 0) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        {{ $venta->fecha_cobro ? $venta->fecha_cobro->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex item-center justify-center space-x-2">
                                            {{-- BOTON EDITAR CON PIN --}}
                                            <button wire:click="tryEdit({{ $venta->id }})"
                                                class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110"
                                                title="Editar (Requiere PIN)">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            <button wire:click="delete({{ $venta->id }})"
                                                wire:confirm="¿Borrar esta venta?"
                                                class="w-4 mr-2 transform hover:text-red-500 hover:scale-110">
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
                                    <td colspan="5" class="text-center py-4">No hay ventas registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $ventas->links() }}</div>
            </div>
        </div>
    </div>

    {{-- MODAL PRINCIPAL (CREAR/EDITAR) --}}
    @if ($isOpen)
        <div class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>

                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                            {{ $venta_id ? 'Editar Venta' : 'Nueva Venta' }}</h3>

                        <form>
                            <div class="grid grid-cols-2 gap-6">

                                {{-- SELECCION DE CLIENTE --}}
                                <div class="mb-4 relative">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Cliente:</label>
                                    @if ($selectedClienteName)
                                        <div class="flex justify-between items-center bg-gray-100 p-2 rounded border">
                                            <span>{{ $selectedClienteName }}</span>
                                            <button type="button" wire:click="$set('selectedClienteName', null)"
                                                class="text-red-500 font-bold">X</button>
                                        </div>
                                    @else
                                        <input type="text" wire:model.live="searchClienteInput"
                                            placeholder="Buscar por DNI o Apellido..."
                                            class="w-full border-gray-300 rounded shadow-sm focus:ring-indigo-500">
                                        @if (!empty($clientesFound))
                                            <ul
                                                class="absolute z-50 bg-white border border-gray-300 w-full mt-1 rounded shadow-lg max-h-40 overflow-y-auto">
                                                @foreach ($clientesFound as $cliente)
                                                    <li wire:click="selectCliente({{ $cliente->id }}, '{{ $cliente->nombre }} {{ $cliente->apellido }}')"
                                                        class="p-2 hover:bg-indigo-100 cursor-pointer border-b">
                                                        <span class="font-bold">{{ $cliente->dni }}</span> -
                                                        {{ $cliente->apellido }} {{ $cliente->nombre }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    @endif
                                    @error('cliente_id')
                                        <span class="text-red-500 text-xs">Seleccione un cliente</span>
                                    @enderror
                                </div>

                                {{-- SELECCION DE VEHICULO --}}
                                <div class="mb-4 relative">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Vehículo:</label>
                                    @if ($selectedVehiculoName)
                                        <div class="flex justify-between items-center bg-gray-100 p-2 rounded border">
                                            <span>{{ $selectedVehiculoName }}</span>
                                            <button type="button" wire:click="$set('selectedVehiculoName', null)"
                                                class="text-red-500 font-bold">X</button>
                                        </div>
                                    @else
                                        <input type="text" wire:model.live="searchVehiculoInput"
                                            placeholder="Buscar Patente o Modelo..."
                                            class="w-full border-gray-300 rounded shadow-sm focus:ring-indigo-500">
                                        @if (!empty($vehiculosFound))
                                            <ul
                                                class="absolute z-50 bg-white border border-gray-300 w-full mt-1 rounded shadow-lg max-h-40 overflow-y-auto">
                                                @foreach ($vehiculosFound as $vehiculo)
                                                    <li wire:click="selectVehiculo({{ $vehiculo->id }}, '{{ $vehiculo->brand }} {{ $vehiculo->model }}', {{ $vehiculo->price }})"
                                                        class="p-2 hover:bg-indigo-100 cursor-pointer border-b">
                                                        <span
                                                            class="font-bold text-blue-600">{{ $vehiculo->patent }}</span>
                                                        - {{ $vehiculo->brand }} {{ $vehiculo->model }}
                                                        (${{ $vehiculo->price }})
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    @endif
                                    @error('vehiculo_id')
                                        <span class="text-red-500 text-xs">Seleccione un vehículo</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- PRECIO Y OBSERVACIONES --}}
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Precio de Venta:</label>
                                <input type="number" step="0.01" wire:model="precio_venta"
                                    class="w-full border-gray-300 rounded shadow-sm focus:ring-indigo-500">
                                @error('precio_venta')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Cant. Cuotas:</label>
                                    <input type="number" wire:model="cantidad_cuotas"
                                        class="w-full border-gray-300 rounded shadow-sm focus:ring-indigo-500">
                                    @error('cantidad_cuotas')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Monto por Cuota:</label>
                                    <input type="number" step="0.01" wire:model="monto_cuota"
                                        class="w-full border-gray-300 rounded shadow-sm focus:ring-indigo-500">
                                    @error('monto_cuota')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Fecha de Primer
                                    Cobro:</label>
                                <input type="date" wire:model="fecha_cobro"
                                    class="w-full border-gray-300 rounded shadow-sm focus:ring-indigo-500">
                                @error('fecha_cobro')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Observaciones:</label>
                                <textarea wire:model="observaciones" class="w-full border-gray-300 rounded shadow-sm focus:ring-indigo-500"
                                    rows="3"></textarea>
                            </div>

                        </form>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click.prevent="store()" type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm">Guardar</button>
                        <button wire:click="closeModal()" type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL DE SEGURIDAD (PIN) --}}
    @if ($isPinModalOpen)
        <div class="fixed z-20 inset-0 overflow-y-auto ease-out duration-400">
            <div class="flex items-center justify-center min-h-screen px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity">
                    <div class="absolute inset-0 bg-gray-900 opacity-80"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>

                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm w-full">
                    <div class="bg-red-50 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Seguridad requerida</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">Ingrese el PIN de seguridad para editar esta
                                        venta.</p>
                                    <input type="password" wire:model="pinInput"
                                        class="mt-3 w-full text-center text-2xl tracking-widest border-gray-300 rounded shadow-sm focus:ring-red-500 focus:border-red-500"
                                        placeholder="****" maxlength="4">
                                    @error('pin')
                                        <span class="text-red-500 text-xs block mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="verifyPin()" type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">Verificar</button>
                        <button wire:click="closeModal()" type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
