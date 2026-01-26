<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registrar Nuevo Cobro') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">

                {{-- 1. SELECCIÓN DE CLIENTE --}}
                <div class="mb-6 border-b pb-6">
                    <h3 class="text-lg font-bold text-gray-700 mb-2">1. Cliente</h3>
                    @if ($cliente_id)
                        <div class="flex justify-between items-center bg-green-50 p-3 rounded border border-green-200">
                            <span class="font-bold text-green-800">{{ $cliente_seleccionado_nombre }}</span>
                            <button wire:click="$set('cliente_id', null)"
                                class="text-red-500 font-bold text-sm">Cambiar</button>
                        </div>
                    @else
                        <div class="relative">
                            <input type="text" wire:model.live="searchCliente"
                                placeholder="Buscar por Nombre, Apellido o DNI..."
                                class="w-full border-gray-300 rounded shadow-sm">
                            @if (!empty($clientes))
                                <ul class="absolute z-10 bg-white border w-full mt-1 rounded shadow-lg">
                                    @foreach ($clientes as $c)
                                        <li wire:click="seleccionarCliente({{ $c->id }}, '{{ $c->apellido }} {{ $c->nombre }}')"
                                            class="p-2 hover:bg-gray-100 cursor-pointer border-b">
                                            {{ $c->apellido }} {{ $c->nombre }} ({{ $c->dni }})
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                        @error('cliente_id')
                            <span class="text-red-500 text-xs">Debe seleccionar un cliente.</span>
                        @enderror
                    @endif
                </div>

                {{-- 2. MÉTODOS DE PAGO (MIXTO) --}}
                <div class="mb-6 border-b pb-6">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="text-lg font-bold text-gray-700">2. Detalles del Pago</h3>
                        <button wire:click="agregarMetodoPago"
                            class="text-indigo-600 text-sm font-bold hover:underline">+ Agregar otro método</button>
                    </div>

                    <div class="space-y-3">
                        @foreach ($itemsPago as $index => $item)
                            <div class="flex flex-col md:flex-row gap-3 items-start bg-gray-50 p-3 rounded">
                                {{-- Tipo --}}
                                <div class="w-full md:w-1/3">
                                    <select wire:model.live="itemsPago.{{ $index }}.metodo"
                                        class="w-full border-gray-300 rounded shadow-sm text-sm">
                                        <option value="efectivo">Efectivo</option>
                                        <option value="transferencia">Transferencia</option>
                                    </select>
                                </div>

                                {{-- Referencia (Condicional) --}}
                                <div class="w-full md:w-1/3">
                                    @if ($item['metodo'] == 'transferencia')
                                        <input type="text" wire:model="itemsPago.{{ $index }}.referencia"
                                            placeholder="Cód. Transferencia"
                                            class="w-full border-gray-300 rounded shadow-sm text-sm">
                                        @error("itemsPago.{$index}.referencia")
                                            <span class="text-red-500 text-xs block">{{ $message }}</span>
                                        @enderror
                                    @else
                                        <input type="text" disabled
                                            class="w-full bg-gray-200 border-gray-300 rounded shadow-sm text-sm cursor-not-allowed"
                                            placeholder="N/A">
                                    @endif
                                </div>

                                {{-- Monto --}}
                                <div class="w-full md:w-1/3 flex items-center gap-2">
                                    <span class="text-gray-500">$</span>
                                    <input type="number" step="0.01"
                                        wire:model.live="itemsPago.{{ $index }}.monto" placeholder="0.00"
                                        class="w-full border-gray-300 rounded shadow-sm text-sm font-bold text-right">

                                    @if (count($itemsPago) > 1)
                                        <button wire:click="quitarMetodoPago({{ $index }})"
                                            class="text-red-500 hover:text-red-700 font-bold">X</button>
                                    @endif
                                </div>
                            </div>
                            @error("itemsPago.{$index}.monto")
                                <span class="text-red-500 text-xs ml-2">{{ $message }}</span>
                            @enderror
                        @endforeach
                    </div>
                </div>

                {{-- 3. TOTAL Y CIERRE --}}
                <div class="flex justify-end items-center mb-6">
                    <div class="text-right">
                        <span class="text-gray-600 text-lg mr-2">Total a Pagar:</span>
                        <span class="text-3xl font-bold text-green-600">${{ number_format($total_a_pagar, 2) }}</span>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Observaciones:</label>
                    <textarea wire:model="observaciones" class="w-full border-gray-300 rounded shadow-sm" rows="2"></textarea>
                </div>

                <div class="flex justify-end gap-4">
                    <a href="{{ route('caja.index') }}"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancelar</a>
                    <button wire:click="guardarCobro"
                        class="px-6 py-2 bg-indigo-600 text-white font-bold rounded shadow hover:bg-indigo-700">
                        CONFIRMAR COBRO
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>
