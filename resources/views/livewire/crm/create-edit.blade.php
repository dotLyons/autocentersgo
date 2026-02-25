<div>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('crm.index') }}" wire:navigate class="text-gray-500 hover:text-indigo-600 transition">
                <svg class="w-6 h-6 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $clienteId ? 'Editar Cliente: ' . $nombre . ' ' . $apellido : 'Registrar Nuevo Cliente' }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                
                <form wire:submit.prevent="save">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- DNI -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">DNI / Documento *</label>
                            <input type="text" wire:model="dni" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('dni') <span class="text-sm text-red-600 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Tipo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Categoría del Cliente *</label>
                            <select wire:model="tipo_cliente" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="comprador">Exclusivo Comprador</option>
                                <option value="vendedor">Exclusivo Vendedor</option>
                                <option value="ambos">Ambos (Operaciones de compra y venta)</option>
                            </select>
                            @error('tipo_cliente') <span class="text-sm text-red-600 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Nombre -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nombre *</label>
                            <input type="text" wire:model="nombre" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('nombre') <span class="text-sm text-red-600 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Apellido -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Apellido *</label>
                            <input type="text" wire:model="apellido" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('apellido') <span class="text-sm text-red-600 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Celular -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Celular *</label>
                            <input type="text" wire:model="celular" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('celular') <span class="text-sm text-red-600 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Celular Alternativo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Celular de Referencia (Opcional)</label>
                            <input type="text" wire:model="celular_referencia" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('celular_referencia') <span class="text-sm text-red-600 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Email -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Correo Electrónico (Opcional)</label>
                            <input type="email" wire:model="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('email') <span class="text-sm text-red-600 mt-1">{{ $message }}</span> @enderror
                        </div>

                    </div>

                    <div class="mt-8 border-t border-gray-200 pt-5 flex justify-end space-x-3">
                        <a href="{{ route('crm.index') }}" wire:navigate class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancelar
                        </a>
                        <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-bold rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ $clienteId ? 'Guardar Cambios' : 'Registrar Cliente' }}
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
