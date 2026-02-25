<div>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('vehicles.index') }}" wire:navigate class="text-gray-500 hover:text-indigo-600 transition">
                <svg class="w-6 h-6 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $vehiculoId ? 'Editar Vehículo: ' . $marca . ' ' . $modelo : 'Cargar Vehículo al Catálogo' }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                
                <form wire:submit.prevent="save">
                    
                    {{-- 1. IDENTIFICACIÓN Y CATEGORÍA --}}
                    <h3 class="text-lg font-bold text-gray-900 border-b pb-2 mb-4">1. Identificación y Categoría</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8 bg-gray-50 p-4 rounded border">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tipo de Vehículo *</label>
                            <select wire:model.live="tipo_vehiculo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="auto">Automóvil</option>
                                <option value="camioneta">Camioneta</option>
                                <option value="furgon">Furgón</option>
                                <option value="moto">Moto</option>
                            </select>
                            @error('tipo_vehiculo') <span class="text-sm text-red-600 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Propiedad (Origen) *</label>
                            <select wire:model.live="categoria_propiedad" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-blue-50 font-semibold text-blue-800">
                                <option value="propio">Propio de Agencia</option>
                                <option value="consignacion">Consignación (De un Vendedor)</option>
                            </select>
                            @error('categoria_propiedad') <span class="text-sm text-red-600 mt-1">{{ $message }}</span> @enderror
                        </div>

                        @if($categoria_propiedad === 'consignacion')
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 text-purple-700">Seleccionar Propietario Existente (Vendedor) *</label>
                            <select wire:model="vendedor_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                                <option value="">--- Seleccione ---</option>
                                @foreach($vendedores as $v)
                                    <option value="{{ $v->id }}">{{ $v->dni }} - {{ $v->apellido }} {{ $v->nombre }}</option>
                                @endforeach
                            </select>
                            @error('vendedor_id') <span class="text-sm text-red-600 mt-1">{{ $message }}</span> @enderror
                        </div>
                        @else
                        <div class="md:col-span-2 flex items-center p-2 text-sm text-gray-500">
                            * Al ingresar en parte de pago o comprarlo directo, este vehículo pertenece netamente a la Agencia.
                        </div>
                        @endif
                    </div>

                    {{-- 2. DATOS PRINCIPALES --}}
                    <h3 class="text-lg font-bold text-gray-900 border-b pb-2 mb-4">2. Datos Principales</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Marca *</label>
                            <input type="text" wire:model="marca" placeholder="Ej: Toyota" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('marca') <span class="text-sm text-red-600 mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Modelo *</label>
                            <input type="text" wire:model="modelo" placeholder="Ej: Hilux SRX" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('modelo') <span class="text-sm text-red-600 mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Año *</label>
                            <input type="number" wire:model="anio" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('anio') <span class="text-sm text-red-600 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Precio Venta Público ($)</label>
                            <input type="number" wire:model="precio_venta_publico" class="mt-1 flex-1 block w-full rounded-md sm:text-sm border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            @error('precio_venta_publico') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Monto de Entrega Requerido / Inicial ($)</label>
                            <input type="number" wire:model="monto_entrega_requerido" class="mt-1 flex-1 block w-full rounded-md sm:text-sm border-gray-300 focus:ring-green-500 focus:border-green-500 bg-green-50">
                            <p class="text-xs text-gray-500 mt-1">Este monto será la exigencia mínima al comprador para llevarse la unidad.</p>
                            @error('monto_entrega_requerido') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Patente</label>
                            <input type="text" wire:model="patente" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm uppercase font-mono">
                            @error('patente') <span class="text-sm text-red-600 mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Color</label>
                            <input type="text" wire:model="color" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('color') <span class="text-sm text-red-600 mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Versión General</label>
                            <input type="text" wire:model="version" placeholder="Ej: 2.8 TDI 4x4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('version') <span class="text-sm text-red-600 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- 3. DETALLE TÉCNICO --}}
                    <h3 class="text-lg font-bold text-gray-900 border-b pb-2 mb-4">3. Detalle Técnico y Registral</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Cód. Motor</label>
                            <input type="text" wire:model="codigo_motor" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('codigo_motor') <span class="text-sm text-red-600 mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Cód. Chasis / Marco (VIN)</label>
                            <input type="text" wire:model="codigo_chasis_o_marco" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm uppercase">
                            @error('codigo_chasis_o_marco') <span class="text-sm text-red-600 mt-1">{{ $message }}</span> @enderror
                        </div>

                        @if($tipo_vehiculo !== 'moto')
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Transmisión</label>
                            <select wire:model="tipo_caja" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="manual">Manual</option>
                                <option value="automatica">Automática</option>
                            </select>
                            @error('tipo_caja') <span class="text-sm text-red-600 mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Cant. Puertas</label>
                            <input type="number" wire:model="puertas" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        @else
                        <div class="md:col-span-2 text-sm text-gray-400 mt-6">- Opciones de Puertas y Transmisión no aplican para motovehículos.</div>
                        @endif

                        <div class="md:col-span-4 mt-2 bg-yellow-50 p-4 border border-yellow-200 rounded flex space-x-4">
                            <div class="flex items-center">
                                <input id="tiene_gnc" type="checkbox" wire:model.live="tiene_gnc" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="tiene_gnc" class="ml-2 block text-sm font-bold text-gray-900">
                                    ¿Tiene Instalación de GNC?
                                </label>
                            </div>
                            @if($tiene_gnc)
                            <div class="flex-1 flex items-center space-x-2 border-l border-yellow-300 pl-4">
                                <label class="text-sm font-medium text-gray-700">Indicar Generación:</label>
                                <select wire:model="generacion_gnc" class="block rounded-md border-gray-300 shadow-sm sm:text-sm w-32 py-1">
                                    <option value="">Seleccione</option>
                                    <option value="1">1ra Gen</option><option value="2">2da Gen</option><option value="3">3ra Gen</option><option value="4">4ta Gen</option><option value="5">5ta Gen</option>
                                </select>
                                @error('generacion_gnc') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                            </div>
                            @endif
                        </div>

                    </div>

                    {{-- 4. PRECIOS Y ALINEACIÓN COMERCIAL --}}
                    <h3 class="text-lg font-bold text-gray-900 border-b pb-2 mb-4">4. Precios (Opcionales para el Tasador)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 bg-green-50 p-4 rounded border border-green-200">
                        @if($categoria_propiedad === 'consignacion')
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Lo que pide el Vendedor ($)</label>
                                <input type="number" step="0.01" wire:model.live="precio_venta_consignacion" wire:change="autoCalcularGanancia" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm text-right font-bold">
                                @error('precio_venta_consignacion') <span class="text-sm text-red-600 mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Precio de Venta Público ($)</label>
                                <input type="number" step="0.01" wire:model.live="precio_venta_publico" wire:change="autoCalcularGanancia" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm text-right font-bold text-green-700">
                                @error('precio_venta_publico') <span class="text-sm text-red-600 mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Ganancia Concesionaria ($)</label>
                                <input type="number" step="0.01" wire:model="ganancia_concesionaria" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-200 cursor-not-allowed shadow-inner sm:text-sm text-right font-bold" readonly placeholder="Calculado Autom.">
                            </div>
                        @else
                            <div class="md:col-span-3">
                                <label class="block text-sm font-medium text-gray-700">Precio de Venta Público Definido ($) - Si no lo sabe déjelo vacío y defina al tasar o descontar.</label>
                                <div class="mt-2 w-1/3">
                                    <input type="number" step="0.01" wire:model="precio_venta_publico" placeholder="0.00" class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500 text-right text-lg font-bold text-green-700">
                                </div>
                                @error('precio_venta_publico') <span class="text-sm text-red-600 mt-1">{{ $message }}</span> @enderror
                            </div>
                        @endif
                    </div>

                    <div class="mt-8 border-t border-gray-200 pt-5 flex justify-end space-x-3">
                        <a href="{{ route('vehicles.index') }}" wire:navigate class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancelar
                        </a>
                        <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-bold rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ $vehiculoId ? 'Actualizar Vehículo' : 'Ingresar a Catálogo' }}
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
