<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Terminal de Caja (POS)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session()->has('message'))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
                    class="bg-green-100 border-t-4 border-green-500 rounded-b text-green-900 px-4 py-3 shadow-md mb-4">
                    <p class="text-sm font-bold">{{ session('message') }}</p>
                </div>
            @endif

            @if (session()->has('error'))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
                    class="bg-red-100 border-t-4 border-red-500 rounded-b text-red-900 px-4 py-3 shadow-md mb-4">
                    <p class="text-sm font-bold">{{ session('error') }}</p>
                </div>
            @endif

            @if(!$cajaActiva)
                {{-- VISTA SIN CAJA ABIERTA --}}
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-10 text-center">
                    <div class="text-gray-400 mb-4">
                        <i class="fas fa-cash-register fa-4x mb-4"></i>
                        <h3 class="text-2xl font-bold text-gray-700">La caja se encuentra cerrada</h3>
                        <p class="mt-2 text-gray-500">Debe abrir la caja indicando el saldo base en efectivo para comenzar a operar o visualizar movimientos del día.</p>
                    </div>
                    <button wire:click="abrirModalCaja" class="mt-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded shadow-lg transition duration-200">
                        Abrir Nueva Caja
                    </button>
                </div>
            @else
                {{-- VISTA CON CAJA ABIERTA --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Resumen del Efectivo (Lo que debería haber en el cajón) -->
                    <div class="bg-indigo-600 rounded-lg shadow-lg p-6 text-white relative overflow-hidden">
                        <div class="absolute right-0 top-0 opacity-10 mt-4 mr-4 text-6xl"><i class="fas fa-money-bill-wave"></i></div>
                        <h3 class="text-indigo-100 font-semibold mb-1">Caja Efectivo (Esperado)</h3>
                        <p class="text-3xl font-bold">
                            @php
                                $efectivoTotal = $cajaActiva->monto_apertura + ($resumen['efectivo_ingreso'] ?? 0) - ($resumen['efectivo_egreso'] ?? 0);
                            @endphp
                            $ {{ number_format($efectivoTotal, 2) }}
                        </p>
                        <div class="mt-4 text-xs text-indigo-200 flex justify-between">
                            <span>Apertura: $ {{ number_format($cajaActiva->monto_apertura, 2) }}</span>
                            <span>Apertura el: {{ $cajaActiva->fecha_apertura->format('H:i') }}hs</span>
                        </div>
                    </div>

                    <!-- Resumen Digitales (Transferencias, Tarjetas) -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-400">
                        <h3 class="text-gray-500 font-semibold mb-2">Cobros Digitales</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center border-b pb-1">
                                <span class="text-sm font-medium text-gray-600">Transferencias:</span>
                                <span class="text-sm font-bold text-blue-600">$ {{ number_format(($resumen['transferencia_ingreso'] ?? 0) - ($resumen['transferencia_egreso'] ?? 0), 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center pb-1">
                                <span class="text-sm font-medium text-gray-600">Tarjetas:</span>
                                <span class="text-sm font-bold text-blue-600">$ {{ number_format(($resumen['tarjeta_ingreso'] ?? 0) - ($resumen['tarjeta_egreso'] ?? 0), 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción Global -->
                    <div class="bg-white rounded-lg shadow p-6 flex flex-col justify-center space-y-3">
                        <button wire:click="abrirModalMovimiento" class="w-full bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 font-bold py-2 px-4 rounded transition text-center">
                            + Registrar Movimiento
                        </button>
                        <button wire:click="abrirModalCerrar" class="w-full bg-red-50 text-red-700 hover:bg-red-100 border border-red-200 font-bold py-2 px-4 rounded transition text-center">
                            <i class="fas fa-lock mr-2"></i> Cerrar Caja
                        </button>
                    </div>
                </div>

                <!-- Historial de Movimientos -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="font-bold text-gray-700">Movimientos de la sesión actual</h3>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-white">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Método</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detalle</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($movimientos as $mov)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $mov->created_at->format('H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($mov->tipo_movimiento->value == 'ingreso')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">INGRESO</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">EGRESO</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                                            {{ ucfirst($mov->metodo_pago->value) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ $mov->descripcion }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-right @if($mov->tipo_movimiento->value == 'ingreso') text-green-600 @else text-red-600 @endif">
                                            @if($mov->tipo_movimiento->value == 'ingreso') + @else - @endif 
                                            $ {{ number_format($mov->monto, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-10 text-center text-gray-500 font-medium">No se han registrado movimientos aún.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if(method_exists($movimientos,'links'))
                        <div class="px-6 py-3 border-t">
                            {{ $movimientos->links() }}
                        </div>
                    @endif
                </div>

            @endif

        </div>
    </div>

    {{-- MODAL ABRIR CAJA --}}
    @if ($isOpenAbrirCaja)
        <div class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
                    <form wire:submit.prevent="abrirCaja">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4 border-b pb-2">Apertura de Caja</h3>
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Efectivo inicial en cajón:</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-500">$</span>
                                    <input type="number" step="0.01" wire:model="montoApertura" class="pl-8 w-full border-gray-300 rounded shadow-sm focus:ring-indigo-500" required>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Con cuánto dinero comienzas el turno.</p>
                                @error('montoApertura') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-lg">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm">Confirmar Apertura</button>
                            <button type="button" wire:click="$set('isOpenAbrirCaja', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL CERRAR CAJA --}}
    @if ($isOpenCerrarCaja)
        <div class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
                    <form wire:submit.prevent="cerrarCaja">
                        <div class="bg-red-50 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-red-900 mb-4 border-b border-red-200 pb-2">Verificación y Cierre</h3>
                            
                            <div class="mb-4 bg-white p-3 rounded border border-gray-200 text-center">
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Monto esperado calculado por sistema (Efectivo)</p>
                                <p class="text-2xl font-bold text-gray-800">$ {{ number_format($efectivoTotal ?? 0, 2) }}</p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Efectivo real en cajón (Declarado):</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-500">$</span>
                                    <input type="number" step="0.01" wire:model="montoCierreInformado" class="pl-8 w-full border-gray-300 rounded shadow-sm focus:ring-red-500" required>
                                </div>
                                <p class="text-xs text-gray-500 mt-1 block">Si hay diferencias se indicarán en los auditorías posteriores.</p>
                                @error('montoCierreInformado') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="bg-gray-100 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-lg">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">Confirmar Cierre Definitivo</button>
                            <button type="button" wire:click="$set('isOpenCerrarCaja', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL REGISTRAR MOVIMIENTO MANUAL --}}
    @if ($isOpenMovimiento)
        <div class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
                    <form wire:submit.prevent="registrarMovimiento">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4 border-b pb-2">Registrar Movimiento Extra</h3>
                            
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Tipo:</label>
                                    <select wire:model="movTipo" class="w-full border-gray-300 rounded shadow-sm focus:ring-indigo-500 text-sm">
                                        <option value="ingreso">Ingreso (+)</option>
                                        <option value="egreso">Egreso (-)</option>
                                    </select>
                                    @error('movTipo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Método:</label>
                                    <select wire:model="movMetodo" class="w-full border-gray-300 rounded shadow-sm focus:ring-indigo-500 text-sm">
                                        <option value="efectivo">Efectivo</option>
                                        <option value="transferencia">Transferencia</option>
                                        <option value="tarjeta">Tarjeta</option>
                                    </select>
                                    @error('movMetodo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Monto:</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-500">$</span>
                                    <input type="number" step="0.01" wire:model="movMonto" class="pl-8 w-full border-gray-300 rounded shadow-sm focus:ring-indigo-500" required>
                                </div>
                                @error('movMonto') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Descripción del motivo:</label>
                                <textarea wire:model="movDescripcion" rows="2" class="w-full border-gray-300 rounded shadow-sm focus:ring-indigo-500 text-sm" placeholder="Ej: Pago de luz, extracción dueño, etc." required></textarea>
                                @error('movDescripcion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-lg">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm">Guardar Movimiento</button>
                            <button type="button" wire:click="$set('isOpenMovimiento', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
