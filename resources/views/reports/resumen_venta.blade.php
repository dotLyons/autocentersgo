<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen de Venta - {{ $venta->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { background-color: white !important; -webkit-print-color-adjust: exact; }
            .no-print { display: none !important; }
            .print-border { border: 1px solid #ccc; padding: 20px; box-shadow: none !important; }
        }
    </style>
</head>
<body class="bg-gray-200 p-8 font-sans antialiased text-gray-900">
    <div class="max-w-3xl mx-auto bg-white print-border shadow-xl rounded-lg print:rounded-none p-10 relative overflow-hidden text-sm">
        
        <!-- Decoracion -->
        <div class="absolute top-0 left-0 w-full h-2 bg-indigo-600 no-print"></div>

        <!-- Header -->
        <div class="flex justify-between items-start border-b border-gray-300 pb-6 mb-6">
            <div>
                <h1 class="text-3xl font-black text-indigo-900 tracking-tight uppercase">Resumen de Operación</h1>
                <p class="text-gray-500 font-medium mt-1 uppercase tracking-widest text-[10px]">Venta de Vehículo Finalizada</p>
                <div class="mt-4">
                    <p class="font-bold text-gray-800">AutoCenters GO</p>
                    <p class="text-gray-500">Documento de constancia interna/cliente.</p>
                </div>
            </div>
            <div class="text-right">
                <div class="inline-block p-3 border-2 border-gray-200 rounded-lg bg-gray-50">
                    <p class="text-gray-500 font-bold uppercase text-[10px] mb-1">Operación Nro</p>
                    <p class="text-xl font-mono text-gray-800 font-bold">{{ str_pad($venta->id, 8, '0', STR_PAD_LEFT) }}</p>
                </div>
                <p class="mt-4 text-gray-600"><span class="font-bold text-gray-800">Fecha:</span> {{ \Carbon\Carbon::parse($venta->created_at)->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        @php 
            $cliente = $venta->legajo->cliente;
            $vehiculo = $venta->vehiculo; 
            $vehiculoEntregado = $venta->vehiculo_entregado_id ? App\Src\Vehicles\Models\Vehiculo::find($venta->vehiculo_entregado_id) : null;
        @endphp

        <!-- Datos Claves -->
        <div class="grid grid-cols-2 gap-8 mb-8">
            <div class="bg-gray-50 p-5 border border-gray-200 rounded-lg">
                <h3 class="font-bold text-indigo-900 uppercase tracking-widest border-b border-indigo-200 pb-2 mb-3 text-xs">Datos del Comprador</h3>
                <p class="mb-1"><span class="font-bold text-gray-700">Nombre:</span> {{ $cliente->apellido }}, {{ $cliente->nombre }}</p>
                <p class="mb-1"><span class="font-bold text-gray-700">DNI/CUIT:</span> {{ $cliente->dni }}</p>
                <p class="mb-0"><span class="font-bold text-gray-700">Teléfono:</span> {{ $cliente->celular ?? 'N/A' }}</p>
            </div>
            <div class="bg-indigo-50 p-5 border border-indigo-200 rounded-lg">
                <h3 class="font-bold text-indigo-900 uppercase tracking-widest border-b border-indigo-200 pb-2 mb-3 text-xs">Vehículo Adquirido</h3>
                <p class="mb-1"><span class="font-bold text-gray-700">Vehículo:</span> {{ $vehiculo->marca }} {{ $vehiculo->modelo }}</p>
                <p class="mb-1"><span class="font-bold text-gray-700">Patente:</span> <span class="font-mono bg-white border border-indigo-300 px-2 py-0.5 rounded text-xs ml-1 uppercase shadow-sm">{{ $vehiculo->patente }}</span></p>
            </div>
        </div>

        <!-- Estructura Financiera -->
        <div class="mb-8 border border-gray-200 rounded-lg overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th colspan="2" class="py-3 px-5 font-bold uppercase tracking-wider text-[11px] text-center">Estructura Financiera (Cierre)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr class="bg-gray-50">
                        <td class="py-3 px-5 text-gray-800 font-bold uppercase text-xs w-2/3">Valor de Venta (Total Operación)</td>
                        <td class="py-3 px-5 text-right text-gray-900 font-bold text-lg">$ {{ number_format($venta->precio_compra, 2) }}</td>
                    </tr>
                    
                    {{-- Aportes / Dinero Entrante --}}
                    <tr>
                        <td class="py-2 px-5 text-gray-600 pl-8 border-l-4 border-green-500">Entrega en Efectivo</td>
                        <td class="py-2 px-5 text-right text-gray-800">$ {{ number_format($venta->monto_efectivo, 2) }}</td>
                    </tr>
                    @if($venta->monto_transferencia > 0)
                    <tr>
                        <td class="py-2 px-5 text-gray-600 pl-8 border-l-4 border-blue-500">Monto Transferencia / Banco</td>
                        <td class="py-2 px-5 text-right text-gray-800">$ {{ number_format($venta->monto_transferencia, 2) }}</td>
                    </tr>
                    @endif
                    @if($venta->monto_entrega > 0)
                    <tr>
                        <td class="py-2 px-5 text-gray-600 pl-8 border-l-4 border-yellow-500">Cheques / Otros Medios</td>
                        <td class="py-2 px-5 text-right text-gray-800">$ {{ number_format($venta->monto_entrega, 2) }}</td>
                    </tr>
                    @endif
                    @if($vehiculoEntregado)
                    <tr>
                        <td class="py-2 px-5 text-gray-600 pl-8 border-l-4 border-purple-500">
                            Vehículo en Parte de Pago <span class="text-xs ml-2 text-gray-400">({{ $vehiculoEntregado->marca }} {{ $vehiculoEntregado->modelo }} - {{ $vehiculoEntregado->patente }})</span>
                        </td>
                        <td class="py-2 px-5 text-right text-gray-800">$ {{ number_format($venta->valor_vehiculo_entregado, 2) }}</td>
                    </tr>
                    @endif
                    
                    {{-- Financiacion --}}
                    @if($venta->financiacion_banco > 0)
                    <tr class="bg-blue-50">
                        <td class="py-2 px-5 text-blue-800 font-bold border-l-4 border-blue-600">Financiación Externa (Prendario)</td>
                        <td class="py-2 px-5 text-right text-blue-800 font-bold">$ {{ number_format($venta->financiacion_banco, 2) }}</td>
                    </tr>
                    @endif

                    @if($venta->financiacion_casa > 0)
                    <tr class="bg-indigo-50 border-t-2 border-indigo-200">
                        <td class="py-4 px-5 text-indigo-900 border-l-4 border-indigo-600">
                            <span class="font-bold">Crédito DE LA CASA (Saldo Financiado)</span>
                            <div class="mt-2 text-xs text-indigo-700 bg-white inline-block px-3 py-1 border border-indigo-200 rounded">
                                Plan de Pago Acordado: <strong>{{ $venta->cant_cuotas_casa }} Cuotas Fijas</strong> de <strong>$ {{ number_format($venta->monto_cuota_casa, 2) }}</strong>
                            </div>
                        </td>
                        <td class="py-4 px-5 text-right text-indigo-900 font-bold text-lg align-top">$ {{ number_format($venta->financiacion_casa, 2) }}</td>
                    </tr>
                    @endif

                    @if($venta->saldo_entrega_pendiente > 0)
                    <tr class="bg-red-50">
                        <td class="py-3 px-5 text-red-800 font-bold border-l-4 border-red-600">Saldo Pendiente de Entrega</td>
                        <td class="py-3 px-5 text-right text-red-800 font-bold">$ {{ number_format($venta->saldo_entrega_pendiente, 2) }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Condiciones -->
        <div class="bg-gray-100 p-4 rounded-lg text-sm text-gray-700 grid grid-cols-2 gap-4 border border-gray-200">
            <div>
                <p class="font-bold text-gray-800 mb-1">Estado de Entrega:</p>
                @if($venta->retirado_ahora)
                    <span class="inline-block bg-green-100 text-green-800 border-2 border-green-500 font-bold px-3 py-1 rounded text-xs uppercase tracking-wider shadow-sm">Vehículo Entregado</span>
                @else
                    <span class="inline-block bg-yellow-100 text-yellow-800 border-2 border-yellow-500 font-bold px-3 py-1 rounded text-xs uppercase tracking-wider shadow-sm">Pendiente de Retiro</span>
                @endif
            </div>
            <div>
                <p class="font-bold text-gray-800 mb-1">Transferencia:</p>
                @if($venta->transferencia_a_cargo_comprador)
                    <span class="inline-block bg-white text-gray-800 border border-gray-400 font-bold px-3 py-1 rounded text-xs uppercase tracking-wider shadow-sm">A Cargo del Comprador ($ {{ number_format($venta->costo_transferencia, 2) }})</span>
                @else
                    <span class="inline-block bg-white text-gray-500 border border-gray-300 font-medium px-3 py-1 rounded text-xs shadow-sm">Estándar (Incluida)</span>
                @endif
            </div>
        </div>

        <!-- Firmas -->
        <div class="mt-24 flex justify-between px-10">
            <div class="text-center w-64">
                <div class="border-t border-gray-500 pt-2 text-xs text-gray-600 font-bold uppercase tracking-wider">
                    Firma y Aclaración Comprador
                </div>
            </div>
            <div class="text-center w-64">
                <div class="border-t border-gray-500 pt-2 text-xs text-gray-600 font-bold uppercase tracking-wider">
                    Firma Autorizada AutoCenters GO
                </div>
            </div>
        </div>
        
        <!-- Botones de Acción -->
        <div class="mt-14 text-center no-print">
            <button onclick="window.print()" class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg hover:shadow-xl transition-all mr-4 transform hover:-translate-y-1">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                IMPRIMIR RESUMEN
            </button>
            <a href="{{ route('ventas.index') }}" class="inline-flex items-center bg-white border border-gray-300 hover:bg-gray-50 text-gray-800 font-bold py-3 px-8 rounded-lg shadow transition-all">
                Volver a Ventas
            </a>
        </div>
    </div>
</body>
</html>
