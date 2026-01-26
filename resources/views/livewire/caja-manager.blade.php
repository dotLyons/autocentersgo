<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Caja y Movimientos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- TARJETAS DE RESUMEN --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-green-100 p-6 rounded-lg shadow border-l-4 border-green-500">
                    <p class="text-green-600 font-bold">Total Ingresos</p>
                    <p class="text-2xl font-bold">${{ number_format($totalIngresos, 2) }}</p>
                </div>
                <div class="bg-red-100 p-6 rounded-lg shadow border-l-4 border-red-500">
                    <p class="text-red-600 font-bold">Total Egresos</p>
                    <p class="text-2xl font-bold">${{ number_format($totalEgresos, 2) }}</p>
                </div>
                <div class="bg-blue-100 p-6 rounded-lg shadow border-l-4 border-blue-500">
                    <p class="text-blue-600 font-bold">Saldo del Periodo</p>
                    <p class="text-2xl font-bold">${{ number_format($saldo, 2) }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                {{-- FILTROS --}}
                <div class="flex flex-col md:flex-row gap-4 mb-6 items-end">
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Desde:</label>
                        <input type="date" wire:model.live="fecha_inicio" class="border-gray-300 rounded shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Hasta:</label>
                        <input type="date" wire:model.live="fecha_fin" class="border-gray-300 rounded shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Tipo:</label>
                        <select wire:model.live="tipo_filtro" class="border-gray-300 rounded shadow-sm">
                            <option value="">Todos</option>
                            <option value="ingreso">Ingresos</option>
                            <option value="egreso">Egresos</option>
                        </select>
                    </div>
                    <div class="ml-auto">
                        <a href="{{ route('cobros.create') }}"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            + Nuevo Cobro
                        </a>
                    </div>
                </div>

                {{-- TABLA --}}
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 uppercase text-sm">
                            <th class="p-3">Fecha</th>
                            <th class="p-3">Concepto</th>
                            <th class="p-3">Usuario</th>
                            <th class="p-3 text-right">Monto</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @forelse($movimientos as $mov)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-3">{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                                <td class="p-3">
                                    <span class="block font-bold">{{ $mov->concepto }}</span>
                                    <span class="text-xs text-gray-500 capitalize">{{ $mov->tipo }}</span>
                                </td>
                                <td class="p-3 text-sm">{{ $mov->user->name }}</td>
                                <td
                                    class="p-3 text-right font-mono font-bold {{ $mov->tipo == 'ingreso' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $mov->tipo == 'ingreso' ? '+' : '-' }} ${{ number_format($mov->monto, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-4 text-center text-gray-500">No hay movimientos en este
                                    rango.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
