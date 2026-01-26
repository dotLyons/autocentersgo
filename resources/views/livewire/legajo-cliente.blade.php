<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Legajo del Cliente') }}
            </h2>
            <a href="{{ route('clientes.index') }}"
                class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded text-sm">
                Volver al listado
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- SECCIÓN 1: DATOS DEL CLIENTE --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                <div class="flex items-center">
                    <div class="bg-indigo-100 rounded-full p-4 mr-4">
                        <svg class="w-12 h-12 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800">{{ $cliente->nombre }} {{ $cliente->apellido }}
                        </h3>
                        <div class="text-gray-600 flex space-x-4 mt-1">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0c0 .883-.393 1.627-1.08 1.998" />
                                </svg>
                                DNI: {{ $cliente->dni }}
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                {{ $cliente->telefono ?? 'Sin teléfono' }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">{{ $cliente->direccion ?? 'Sin dirección registrada' }}
                        </p>
                    </div>
                    <div class="ml-auto">
                        <span
                            class="{{ $cliente->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} text-sm font-medium px-3 py-1 rounded-full">
                            {{ $cliente->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                </div>
            </div>

            <h3 class="text-lg font-bold text-gray-800 mb-4 px-2">Vehículos Adquiridos / Planes de Pago</h3>

            {{-- SECCIÓN 2: LISTADO DE VEHÍCULOS (VENTAS) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse ($ventas as $venta)
                    @php
                        $pagadas = $this->getCuotasPagadas($venta->id);
                        $totales = $venta->cantidad_cuotas;
                        $porcentaje = $this->getProgresoPorcentaje($pagadas, $totales);
                    @endphp

                    <div
                        class="bg-white overflow-hidden shadow-xl sm:rounded-lg border-l-4 border-indigo-500 p-6 relative">

                        {{-- Icono de coche de fondo --}}
                        <div class="absolute right-4 top-4 opacity-10">
                            <svg class="w-24 h-24 text-gray-800" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                                <path
                                    d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a2.5 2.5 0 014.9 0H16a1 1 0 001-1V11h-5.382a1 1 0 01-.894-.553L9.264 6H4a1 1 0 00-1 1v-3z" />
                            </svg>
                        </div>

                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h4 class="text-xl font-bold text-gray-900">
                                    {{ $venta->vehiculo->brand }} {{ $venta->vehiculo->model }}
                                </h4>
                                <span class="bg-gray-200 text-gray-700 text-xs font-bold px-2 py-1 rounded uppercase">
                                    {{ $venta->vehiculo->patent }}
                                </span>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">Precio Venta</p>
                                <p class="text-lg font-bold text-green-600">
                                    ${{ number_format($venta->precio_venta, 2) }}</p>
                            </div>
                        </div>

                        {{-- Barra de Progreso --}}
                        <div class="mb-4">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-semibold text-gray-700">Progreso del Plan</span>
                                <span class="font-semibold text-indigo-600">{{ $pagadas }} de {{ $totales }}
                                    cuotas</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ $porcentaje }}%">
                                </div>
                            </div>
                        </div>

                        {{-- Detalles Financieros --}}
                        <div class="grid grid-cols-2 gap-4 text-sm border-t pt-4 mt-2">
                            <div>
                                <p class="text-gray-500">Valor Cuota</p>
                                <p class="font-bold text-gray-800">${{ number_format($venta->monto_cuota, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Inicio Cobro</p>
                                <p class="font-bold text-gray-800">
                                    {{ $venta->fecha_cobro ? \Carbon\Carbon::parse($venta->fecha_cobro)->format('d/m/Y') : '-' }}
                                </p>
                            </div>
                        </div>

                        {{-- Observaciones si existen --}}
                        @if ($venta->observaciones)
                            <div class="mt-4 bg-yellow-50 p-2 rounded text-xs text-yellow-800 border border-yellow-200">
                                <strong>Obs:</strong> {{ $venta->observaciones }}
                            </div>
                        @endif

                    </div>
                @empty
                    <div class="col-span-1 md:col-span-2 bg-white rounded-lg shadow p-8 text-center">
                        <div class="text-gray-400 mb-4">
                            <svg class="w-16 h-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Este cliente no tiene vehículos adquiridos</h3>
                        <p class="text-gray-500 mt-2">Registra una venta para ver la información aquí.</p>
                        <a href="{{ route('ventas.index') }}"
                            class="inline-block mt-4 text-indigo-600 hover:text-indigo-800 font-medium">
                            Ir a registrar venta &rarr;
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
