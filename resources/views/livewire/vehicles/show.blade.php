<div>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('vehicles.index') }}" wire:navigate class="text-gray-500 hover:text-indigo-600 transition">
                <svg class="w-6 h-6 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Expediente del Vehículo: ') }} {{ $vehiculo->marca }} {{ $vehiculo->modelo }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if (session()->has('message'))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
                    class="bg-green-100 border-t-4 border-green-500 rounded-b text-green-900 px-4 py-3 shadow-md mb-4">
                    <p class="text-sm font-bold">{{ session('message') }}</p>
                </div>
            @endif

            {{-- Panel Superior de Información General --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg flex flex-col md:flex-row relative">

                <div class="w-full md:w-2 {{ $vehiculo->categoria_propiedad->value === 'propio' ? 'bg-green-500' : 'bg-purple-500' }}"></div>

                <div class="p-6 md:p-8 flex-1 flex flex-col md:flex-row justify-between items-start md:items-center">
                    <div>
                        <div class="flex space-x-2 mb-2">
                            <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-bold ring-1 ring-inset {{ $vehiculo->categoria_propiedad->value === 'propio' ? 'bg-green-50 text-green-700 ring-green-600/20' : 'bg-purple-50 text-purple-700 ring-purple-600/20' }}">
                                {{ strtoupper($vehiculo->categoria_propiedad->value) }}
                            </span>
                            <span class="px-2 py-1 text-xs font-bold rounded-md bg-gray-800 text-white uppercase shadow">
                                {{ $vehiculo->tipo_vehiculo->value }}
                            </span>
                        </div>
                        <h3 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</h3>
                        <p class="text-gray-500 mt-1 font-medium">{{ $vehiculo->version ?? 'Versión Standard' }} • Año {{ $vehiculo->anio }}</p>
                    </div>

                    <div class="mt-6 md:mt-0 text-left md:text-right">
                        <p class="text-sm text-gray-500 uppercase tracking-widest font-bold">Patente</p>
                        <p class="text-3xl font-mono text-gray-800 border-2 border-gray-300 rounded px-4 py-1 mt-1 bg-gray-50 uppercase shadow-sm">
                            {{ $vehiculo->patente ?? 'S/P' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- Columna Izquierda: Datos Técnicos y Financieros --}}
                <div class="space-y-8 lg:col-span-1">

                    {{-- Ficha Técnica --}}
                    <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-200">
                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                            <h4 class="font-bold text-gray-700"><i class="fas fa-clipboard-list mr-2"></i> Detalles Técnicos</h4>
                        </div>
                        <div class="p-4 space-y-3">
                            <div class="flex justify-between items-center text-sm border-b border-dashed pb-2">
                                <span class="text-gray-500 font-medium">Color Exterior</span>
                                <span class="text-gray-900 font-bold">{{ $vehiculo->color ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm border-b border-dashed pb-2">
                                <span class="text-gray-500 font-medium">Motorización</span>
                                <span class="text-gray-900 font-bold">{{ $vehiculo->version_motor ?? 'Nafta/Diesel' }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm border-b border-dashed pb-2">
                                <span class="text-gray-500 font-medium">Cód. Motor</span>
                                <span class="text-gray-900 font-bold uppercase">{{ $vehiculo->codigo_motor ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm border-b border-dashed pb-2">
                                <span class="text-gray-500 font-medium">Cód. Chasis (VIN)</span>
                                <span class="text-gray-900 font-bold uppercase font-mono">{{ $vehiculo->codigo_chasis_o_marco ?? '-' }}</span>
                            </div>
                            @if($vehiculo->tipo_vehiculo->value !== 'moto')
                                <div class="flex justify-between items-center text-sm border-b border-dashed pb-2">
                                    <span class="text-gray-500 font-medium">Transmisión</span>
                                    <span class="text-gray-900 font-bold uppercase">{{ $vehiculo->tipo_caja?->value ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between items-center text-sm border-b border-dashed pb-2">
                                    <span class="text-gray-500 font-medium">Puertas</span>
                                    <span class="text-gray-900 font-bold">{{ $vehiculo->puertas ?? '-' }}</span>
                                </div>
                            @endif
                            @if($vehiculo->tiene_gnc)
                                <div class="flex justify-between items-center text-sm bg-yellow-50 px-2 py-1 rounded">
                                    <span class="text-yellow-700 font-bold"><i class="fas fa-gas-pump mr-1"></i> Instalación GNC</span>
                                    <span class="text-yellow-800 font-bold">Generación {{ $vehiculo->generacion_gnc }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Datos Financieros / Precios --}}
                    <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-200">
                        <div class="px-4 py-3 bg-green-50 border-b border-green-200 text-green-800">
                            <h4 class="font-bold"><i class="fas fa-dollar-sign mr-2"></i> Perfil Comercial y Tasación</h4>
                        </div>
                        <div class="p-4 space-y-4">
                            @if($vehiculo->precio_venta_publico > 0)
                                <div class="text-center p-3 bg-gray-50 rounded border border-gray-200">
                                    <p class="text-xs text-gray-500 uppercase tracking-widest font-bold">Precio Público de Venta</p>
                                    <p class="text-3xl font-bold text-green-600 mt-1">$ {{ number_format($vehiculo->precio_venta_publico, 2) }}</p>
                                </div>
                            @else
                                <div class="text-center p-3 bg-yellow-50 rounded border border-yellow-200">
                                    <p class="text-sm font-bold text-yellow-700">Vehículo Pendiente de Tasación de Venta</p>
                                </div>
                            @endif

                            @if($vehiculo->categoria_propiedad->value === 'consignacion')
                                <div class="mt-4 pt-4 border-t border-gray-100 space-y-3">
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-500">Monto del Titular</span>
                                        <span class="text-gray-800 font-bold">$ {{ number_format($vehiculo->precio_venta_consignacion, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-500">Diferencia (Agencia)</span>
                                        <span class="text-indigo-600 font-bold">$ {{ number_format($vehiculo->ganancia_concesionaria, 2) }}</span>
                                    </div>

                                    @if($vendedor)
                                        <div class="mt-4 p-3 bg-purple-50 border border-purple-200 rounded text-sm text-purple-800">
                                            <p class="text-xs uppercase font-bold text-purple-600 mb-1"><i class="fas fa-user-circle mr-1"></i> Vendedor / Titular Asignado</p>
                                            <p class="font-bold">{{ $vendedor->nombre }} {{ $vendedor->apellido }}</p>
                                            <p class="text-xs mt-1">DNI: {{ $vendedor->dni }} | Cel: {{ $vendedor->celular }}</p>
                                            <a href="{{ route('crm.show', $vendedor->id) }}" class="text-xs underline mt-2 block hover:text-purple-900">Ir al Perfil CRM</a>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="bg-gray-50 px-4 py-3 border-t text-center">
                            <a href="{{ route('vehicles.edit', $vehiculo->id) }}" wire:navigate class="text-indigo-600 font-bold text-sm hover:underline"><i class="fas fa-edit mr-1"></i> Actualizar Ficha y Precios</a>
                        </div>
                    </div>
                </div>

                {{-- Columna Derecha: Mantenimientos y Formularios --}}
                <div class="space-y-6 lg:col-span-2">

                    {{-- Panel Mantenimientos/Taller --}}
                    <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-200">
                        <div class="px-5 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                            <h4 class="font-bold text-gray-800"><i class="fas fa-tools mr-2 text-indigo-600"></i> Historial de Taller y Acondicionamiento</h4>
                            <button wire:click="abrirModalMantenimiento" class="text-sm bg-white border border-gray-300 px-3 py-1 rounded text-gray-700 hover:bg-gray-100 font-bold transition shadow-sm">
                                + Loguear Gasto/Arreglo
                            </button>
                        </div>
                        <div class="p-0">
                            @if($vehiculo->mantenimientos->count() > 0)
                                <ul class="divide-y divide-gray-200">
                                    @foreach($vehiculo->mantenimientos->sortByDesc('fecha_llevado') as $man)
                                        <li class="p-5 hover:bg-gray-50 transition">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200 uppercase mb-1">
                                                        {{ $man->tipo_mantenimiento->value }}
                                                    </span>
                                                    <h5 class="font-bold text-gray-900">{{ $man->descripcion_tareas }}</h5>
                                                    <p class="text-sm text-gray-500 mt-1">Realizado en: {{ $man->nombre_lugar }} | Llevado por: {{ $man->responsable_llevado }}</p>
                                                    @if($man->piezas_cambiadas)
                                                        <p class="text-xs text-gray-400 mt-2"><i class="fas fa-wrench mr-1"></i> Repuestos: {{ $man->piezas_cambiadas }}</p>
                                                    @endif
                                                </div>
                                                <div class="text-right ml-4">
                                                    <p class="text-sm text-gray-400 mb-1">{{ \Carbon\Carbon::parse($man->fecha_llevado)->format('d/m/Y') }}</p>
                                                    @if($man->monto)
                                                        <p class="text-lg font-bold text-red-600">-$ {{ number_format($man->monto, 2) }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="p-8 text-center bg-white border-dashed border-2 border-gray-200 m-4 rounded">
                                    <i class="fas fa-car-crash text-gray-300 fa-3x mb-3"></i>
                                    <h3 class="text-sm font-bold text-gray-900">Ningún mantenimiento registrado</h3>
                                    <p class="mt-1 text-sm text-gray-500">Aún no se ha cargado ninguna boleta de lavadero ni entradas a revisión mecánica.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Panel Legajo Papelería y Formularios --}}
                    <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-200">
                        <div class="px-5 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                            <h4 class="font-bold text-gray-800"><i class="fas fa-folder-open mr-2 text-indigo-600"></i> Documentación Física y Formularios</h4>
                            <button wire:click="abrirModalFormulario" class="text-sm bg-white border border-gray-300 px-3 py-1 rounded text-gray-700 hover:bg-gray-100 font-bold transition shadow-sm">
                                + Subir Archivo PDF / Adjuntar Form
                            </button>
                        </div>
                        <div class="p-0">
                            @if($vehiculo->formularios->count() > 0)
                                <table class="min-w-full divide-y divide-gray-200">
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($vehiculo->formularios as $form)
                                            <tr class="hover:bg-gray-50 transition">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <i class="fas fa-file-invoice text-indigo-400 text-xl mr-3"></i>
                                                        <div>
                                                            <div class="text-sm font-bold text-gray-900 uppercase">{{ str_replace('_', ' ', $form->tipo_formulario->value) }}</div>
                                                            <div class="text-xs text-gray-500">
                                                                @if($form->fecha_presentacion)
                                                                    Presentado el: {{ $form->fecha_presentacion->format('d/m/Y') }}
                                                                @else
                                                                    <span class="italic text-gray-400">Fecha pendiente</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($form->presentado)
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                            OK / Vigente
                                                        </span>
                                                    @else
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 uppercase">
                                                            EN TRÁMITE
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    @if($form->archivo_path)
                                                        <a href="{{ Storage::url($form->archivo_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900"><i class="fas fa-download mr-1"></i> Descargar Visor</a>
                                                    @else
                                                        <span class="text-gray-400 italic text-xs">Físico (Sin escanear)</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="p-8 text-center bg-white border-dashed border-2 border-gray-200 m-4 rounded">
                                    <i class="fas fa-file-slash text-gray-300 fa-3x mb-3"></i>
                                    <h3 class="text-sm font-bold text-gray-900">Sin papelería legal todavía</h3>
                                    <p class="mt-1 text-sm text-gray-500">Aún no se ha documentado si posee Formulario 08 firmado, informes de dominio o libres de deuda.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- MODAL LOGUEAR MANTENIMIENTO --}}
    @if($isOpenMantenimiento)
    <div class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full">
                <form wire:submit.prevent="guardarMantenimiento">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 border-b pb-2 mb-4">Loguear Gasto de Acondicionamiento</h3>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tipo:</label>
                                <select wire:model="mant_tipo" class="w-full border-gray-300 shadow-sm rounded focus:ring-indigo-500 sm:text-sm">
                                    <option value="taller">Taller / Mecánico</option>
                                    <option value="lavadero">Lavadero / Estética</option>
                                    <option value="otro">Otro</option>
                                </select>
                                @error('mant_tipo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Monto Costo ($):</label>
                                <input type="number" step="0.01" wire:model="mant_monto" class="w-full border-gray-300 shadow-sm rounded focus:ring-indigo-500 sm:text-sm" placeholder="Ej: 50000.00">
                                @error('mant_monto') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Descripción de tareas:</label>
                            <input type="text" wire:model="mant_descripcion" class="w-full border-gray-300 shadow-sm rounded focus:ring-indigo-500 sm:text-sm" placeholder="Ej: Afinación y cambio de correa">
                            @error('mant_descripcion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Piezas o repuestos (Opcional):</label>
                            <input type="text" wire:model="mant_repuestos" class="w-full border-gray-300 shadow-sm rounded focus:ring-indigo-500 sm:text-sm" placeholder="Ej: Filtro de aceite, Bomba de agua">
                            @error('mant_repuestos') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Lugar / Taller:</label>
                                <input type="text" wire:model="mant_lugar" class="w-full border-gray-300 shadow-sm rounded focus:ring-indigo-500 sm:text-sm" placeholder="Ej: Lubricentro Pepito">
                                @error('mant_lugar') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Fecha (Ingreso):</label>
                                <input type="date" wire:model="mant_fecha" class="w-full border-gray-300 shadow-sm rounded focus:ring-indigo-500 sm:text-sm">
                                @error('mant_fecha') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Persona que lo llevó:</label>
                            <input type="text" wire:model="mant_responsable" class="w-full border-gray-300 shadow-sm rounded focus:ring-indigo-500 sm:text-sm">
                            @error('mant_responsable') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-lg">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-bold text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Guardar Mantenimiento</button>
                        <button type="button" wire:click="$set('isOpenMantenimiento', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL SUBIR FORMULARIO --}}
    @if($isOpenFormulario)
    <div class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <form wire:submit.prevent="guardarFormulario">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 border-b pb-2 mb-4">Adjuntar o Notificar Formulario Legal</h3>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Seleccione el Tipo de Documento:</label>
                            <select wire:model="form_tipo" class="w-full border-gray-300 shadow-sm rounded focus:ring-indigo-500 sm:text-sm">
                                <option value="Formulario 02">Formulario 02 (Dominio Histórico)</option>
                                <option value="Formulario 04">Formulario 04 (Radicación)</option>
                                <option value="Formulario 08">Formulario 08 (Transferencia)</option>
                                <option value="Formulario 12">Formulario 12 (Verificación Policial)</option>
                                <option value="Libre de Deuda">Informe Libre de Deuda</option>
                            </select>
                            @error('form_tipo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Estado del Documento:</label>
                                <select wire:model="form_presentado" class="w-full border-gray-300 shadow-sm rounded focus:ring-indigo-500 sm:text-sm">
                                    <option value="1">Completado y en poder</option>
                                    <option value="0">En trámite / Faltante</option>
                                </select>
                                @error('form_presentado') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Fecha de Certificación:</label>
                                <input type="date" wire:model="form_fecha" class="w-full border-gray-300 shadow-sm rounded focus:ring-indigo-500 sm:text-sm">
                                @error('form_fecha') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Observaciones (Opcional):</label>
                            <input type="text" wire:model="form_obs" class="w-full border-gray-300 shadow-sm rounded focus:ring-indigo-500 sm:text-sm" placeholder="Ej: Firmado ante el escribano Pérez">
                            @error('form_obs') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-2 p-4 bg-gray-50 border border-gray-200 rounded">
                            <label class="block text-gray-700 text-sm font-bold mb-2"><i class="fas fa-file-pdf mr-1"></i> Adjuntar Fichero Escaneado (Opcional):</label>
                            <input type="file" wire:model="form_archivo" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <span wire:loading wire:target="form_archivo" class="text-xs text-indigo-500 mt-2 block">Cargando archivo, por favor espere...</span>
                            @error('form_archivo') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-lg">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-bold text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Aceptar Formulario</button>
                        <button type="button" wire:click="$set('isOpenFormulario', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
