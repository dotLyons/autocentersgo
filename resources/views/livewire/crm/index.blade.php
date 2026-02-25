<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Directorio de Clientes (CRM)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                @if (session()->has('message'))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
                        class="bg-green-100 border-t-4 border-green-500 rounded-b text-green-900 px-4 py-3 shadow-md mb-4">
                        <p class="text-sm font-bold">{{ session('message') }}</p>
                    </div>
                @endif

                <div class="flex flex-col md:flex-row justify-between items-center mb-6 space-y-4 md:space-y-0 md:space-x-4">
                    <div class="w-full md:w-1/3">
                        <input type="text" wire:model.live="search" placeholder="Buscar por DNI, Nombre o Apellido..."
                            class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    </div>
                    
                    <div class="w-full md:w-1/4">
                        <select wire:model.live="filteroTipo" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Todos los Tipos</option>
                            <option value="vendedor">Vendedores</option>
                            <option value="comprador">Compradores</option>
                            <option value="ambos">Ambos</option>
                        </select>
                    </div>

                    <a href="{{ route('crm.create') }}" wire:navigate
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded shadow transition duration-150 w-full md:w-auto text-center">
                        + Nuevo Cliente
                    </a>
                </div>

                <div class="overflow-x-auto rounded-lg shadow ring-1 ring-black ring-opacity-5">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">DNI</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Cliente</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Contacto</th>
                                <th scope="col" class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900">Tipo</th>
                                <th scope="col" class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($clientes as $cliente)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                        {{ $cliente->dni }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        <div class="font-bold text-gray-900">{{ $cliente->apellido }}, {{ $cliente->nombre }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        <div><i class="fas fa-phone text-xs"></i> {{ $cliente->celular }}</div>
                                        @if($cliente->email)
                                            <div class="text-xs mt-1 text-gray-400">{{ $cliente->email }}</div>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-center">
                                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset
                                            @if($cliente->tipo_cliente->value === 'vendedor') bg-blue-50 text-blue-700 ring-blue-700/10 
                                            @elseif($cliente->tipo_cliente->value === 'comprador') bg-green-50 text-green-700 ring-green-600/20
                                            @else bg-purple-50 text-purple-700 ring-purple-700/10 @endif">
                                            {{ strtoupper($cliente->tipo_cliente->value) }}
                                        </span>
                                    </td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-center text-sm font-medium sm:pr-6">
                                        <a href="{{ route('crm.show', $cliente->id) }}" wire:navigate class="text-indigo-600 hover:text-indigo-900 mx-2" title="Ver Perfil y Legajos">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>
                                        <a href="{{ route('crm.edit', $cliente->id) }}" wire:navigate class="text-amber-600 hover:text-amber-900 mx-2" title="Editar Info">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-10 text-center text-gray-500 font-medium">
                                        No se encontraron clientes registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $clientes->links() }}
                </div>

            </div>
        </div>
    </div>
</div>
