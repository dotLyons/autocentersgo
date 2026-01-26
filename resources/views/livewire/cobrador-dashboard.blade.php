<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cobros del Día') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                @if (session()->has('message'))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" class="bg-green-100 border-t-4 border-green-500 rounded-b text-green-900 px-4 py-3 shadow-md mb-4">
                        <p class="text-sm font-bold">{{ session('message') }}</p>
                    </div>
                @endif

                <h3 class="text-lg font-bold text-gray-700 mb-4">Lista de clientes a cobrar hoy (o vencidos)</h3>

                <div class="overflow-x-auto">
                    <table class="table-auto w-full border-collapse border border-gray-200">
                        <thead>
                            <tr class="bg-indigo-50 text-indigo-800 uppercase text-sm leading-normal">
                                <th class="py-3 px-6 text-left">Cliente</th>
                                <th class="py-3 px-6 text-left">Vehículo</th>
                                <th class="py-3 px-6 text-center">Vencimiento</th>
                                <th class="py-3 px-6 text-center">Monto Cuota</th>
                                <th class="py-3 px-6 text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            @forelse($ventasPendientes as $venta)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-3 px-6 text-left">
                                        <div class="font-bold text-gray-800">{{ $venta->cliente->apellido }}, {{ $venta->cliente->nombre }}</div>
                                        <div class="text-xs">DNI: {{ $venta->cliente->dni }}</div>
                                        <div class="text-xs text-gray-500">{{ $venta->cliente->direccion }}</div>
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        <span class="block font-medium">{{ $venta->vehiculo->brand }} {{ $venta->vehiculo->model }}</span>
                                        <span class="bg-gray-200 text-gray-700 px-2 py-0.5 rounded text-xs font-bold">{{ $venta->vehiculo->patent }}</span>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        @if(\Carbon\Carbon::parse($venta->fecha_cobro)->isPast() && !\Carbon\Carbon::parse($venta->fecha_cobro)->isToday())
                                            <span class="text-red-600 font-bold">Vencido: {{ \Carbon\Carbon::parse($venta->fecha_cobro)->format('d/m/Y') }}</span>
                                        @else
                                            <span class="text-green-600 font-bold">Hoy</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-6 text-center font-bold text-lg">
                                        ${{ number_format($venta->monto_cuota, 2) }}
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <button wire:click="abrirModalCobro({{ $venta->id }})" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow hover:shadow-md transition duration-150 transform hover:-translate-y-1">
                                            $ COBRAR
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-8">
                                        <div class="flex flex-col items-center justify-center text-gray-400">
                                            <svg class="w-16 h-16 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            <p class="text-lg font-medium text-gray-600">¡Todo al día!</p>
                                            <p class="text-sm">No hay cobros pendientes para la fecha actual.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $ventasPendientes->links() }}</div>
            </div>
        </div>
    </div>

    {{-- MODAL DE COBRO (ESTRUCTURA JETSTREAM) --}}
    @if($isOpen)
    <div class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-bold text-gray-900 mb-4 border-b pb-2">
                        Registrar Cobro
                    </h3>

                    {{-- DATOS PRECARGADOS (READONLY) --}}
                    <div class="bg-gray-50 p-3 rounded mb-4 border border-gray-200">
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div>
                                <span class="block text-gray-500 text-xs">Cliente:</span>
                                <span class="font-bold text-gray-800">{{ $cliente_nombre }}</span>
                            </div>
                            <div>
                                <span class="block text-gray-500 text-xs">Vehículo:</span>
                                <span class="font-bold text-gray-800">{{ $vehiculo_info }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- FORMULARIO DE PAGO (COPIADO Y ADAPTADO) --}}
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-sm font-bold text-gray-700">Métodos de Pago:</label>
                            <button wire:click="agregarMetodoPago" class="text-indigo-600 text-xs font-bold hover:underline">+ Agregar otro</button>
                        </div>

                        <div class="space-y-2 max-h-48 overflow-y-auto">
                            @foreach($itemsPago as $index => $item)
                                <div class="flex flex-col gap-2 bg-gray-100 p-2 rounded">
                                    <div class="flex gap-2">
                                        <select wire:model.live="itemsPago.{{ $index }}.metodo" class="w-1/2 border-gray-300 rounded shadow-sm text-sm p-1">
                                            <option value="efectivo">Efectivo</option>
                                            <option value="transferencia">Transferencia</option>
                                        </select>
                                        <input type="number" step="0.01" wire:model.live="itemsPago.{{ $index }}.monto" class="w-1/2 border-gray-300 rounded shadow-sm text-sm p-1 font-bold text-right">
                                    </div>

                                    @if($item['metodo'] == 'transferencia')
                                        <input type="text" wire:model="itemsPago.{{ $index }}.referencia" placeholder="Cód. Transferencia" class="w-full border-gray-300 rounded shadow-sm text-xs p-1">
                                        @error("itemsPago.{$index}.referencia") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    @endif

                                    @if(count($itemsPago) > 1)
                                        <button wire:click="quitarMetodoPago({{ $index }})" class="text-red-500 text-xs text-right hover:underline">Eliminar línea</button>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-between items-center border-t pt-2 mb-4">
                        <span class="text-gray-600">Total Recibido:</span>
                        <span class="text-2xl font-bold text-green-600">${{ number_format($total_a_pagar, 2) }}</span>
                    </div>

                    <div class="mb-2">
                        <label class="block text-xs font-bold text-gray-700">Observaciones (Opcional):</label>
                        <textarea wire:model="observaciones" class="w-full border-gray-300 rounded shadow-sm text-sm" rows="1"></textarea>
                    </div>

                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click.prevent="store()" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Confirmar Cobro
                    </button>
                    <button wire:click="closeModal()" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
