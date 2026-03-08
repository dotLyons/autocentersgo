<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Pago - Cuota #{{ $cuota->numero_cuota }}</title>
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
    <div class="max-w-2xl mx-auto bg-white print-border shadow-xl rounded-lg print:rounded-none p-10 relative overflow-hidden text-sm">
        
        <!-- Decoracion -->
        <div class="absolute top-0 left-0 w-full h-2 bg-indigo-600 no-print"></div>

        <!-- Header -->
        <div class="flex justify-between items-start border-b border-gray-300 pb-6 mb-6">
            <div>
                <h1 class="text-3xl font-black text-indigo-900 tracking-tight uppercase">Recibo de Pago</h1>
                <p class="text-gray-500 font-medium mt-1 uppercase tracking-widest text-[10px]">Mesa de Créditos y Cobranzas</p>
                <div class="mt-4">
                    <p class="font-bold text-gray-800">AutoCenters GO</p>
                    <p class="text-gray-500">Documento no válido como factura.</p>
                </div>
            </div>
            <div class="text-right">
                <div class="inline-block p-3 border-2 border-gray-200 rounded-lg bg-gray-50">
                    <p class="text-gray-500 font-bold uppercase text-[10px] mb-1">Comprobante Nro</p>
                    <p class="text-xl font-mono text-gray-800 font-bold">{{ str_pad($cuota->id, 8, '0', STR_PAD_LEFT) }}</p>
                </div>
                <p class="mt-4 text-gray-600"><span class="font-bold text-gray-800">Fecha:</span> {{ \Carbon\Carbon::parse($cuota->fecha_pago ?? now())->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        @php 
            $cliente = $cuota->legajoVehiculo->legajo->cliente;
            $vehiculo = $cuota->legajoVehiculo->vehiculo; 
        @endphp

        <!-- Info Principal -->
        <div class="grid grid-cols-2 gap-8 mb-8 bg-gray-50 p-6 border border-gray-200 rounded-lg">
            <div>
                <h3 class="font-bold text-indigo-900 uppercase tracking-widest border-b border-indigo-200 pb-2 mb-3 text-xs">Datos del Cliente</h3>
                <p class="mb-1"><span class="font-bold text-gray-700">Nombre:</span> {{ $cliente->apellido }}, {{ $cliente->nombre }}</p>
                <p class="mb-1"><span class="font-bold text-gray-700">DNI/CUIT:</span> {{ $cliente->dni }}</p>
                <p class="mb-0"><span class="font-bold text-gray-700">Teléfono:</span> {{ $cliente->celular ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="font-bold text-indigo-900 uppercase tracking-widest border-b border-indigo-200 pb-2 mb-3 text-xs">Datos del Vehículo</h3>
                <p class="mb-1"><span class="font-bold text-gray-700">Vehículo:</span> {{ $vehiculo->marca }} {{ $vehiculo->modelo }}</p>
                <p class="mb-1"><span class="font-bold text-gray-700">Patente:</span> <span class="font-mono bg-white border border-gray-300 px-2 py-0.5 rounded text-xs ml-1 uppercase shadow-sm">{{ $vehiculo->patente }}</span></p>
                <p class="mb-0 text-indigo-700 font-bold"><span class="text-gray-700">Plan de Cuota:</span> #{{ $cuota->numero_cuota }} de {{ $cuota->legajoVehiculo->cant_cuotas_casa }}</p>
            </div>
        </div>

        <!-- Detalle de Conceptos -->
        <div class="mb-8 border border-gray-200 rounded-lg overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-indigo-900 text-white">
                    <tr>
                        <th class="py-3 px-5 font-bold uppercase tracking-wider text-[11px]">Concepto Abolado</th>
                        <th class="py-3 px-5 text-right font-bold uppercase tracking-wider text-[11px]">Importe</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr class="bg-white">
                        <td class="py-4 px-5 text-gray-800">
                            Valor Original de la Cuota Nro {{ $cuota->numero_cuota }}
                            <div class="text-[10px] text-gray-500 mt-1 uppercase">Vencimiento: {{ \Carbon\Carbon::parse($cuota->fecha_vencimiento)->format('d/m/Y') }}</div>
                        </td>
                        <td class="py-4 px-5 text-right text-gray-800 font-medium">$ {{ number_format($cuota->monto, 2) }}</td>
                    </tr>
                    @if($cuota->interes_mora > 0)
                    <tr class="bg-red-50/50">
                        <td class="py-4 px-5 text-red-800 font-bold">
                            <i class="fas fa-exclamation-circle mr-1"></i> Intereses por Mora aplicados
                        </td>
                        <td class="py-4 px-5 text-right text-red-800 font-bold">$ {{ number_format($cuota->interes_mora, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="bg-green-50 font-bold border-t-2 border-indigo-100">
                        <td class="py-4 px-5 text-green-800 text-lg">TOTAL COBRADO (ESTE RECIBO)</td>
                        <td class="py-4 px-5 text-right text-green-800 text-lg">$ {{ number_format($cuota->monto_pagado, 2) }}</td>
                    </tr>
                    @if(!$cuota->pagada)
                    <tr class="bg-orange-50 font-bold">
                        <td class="py-3 px-5 text-orange-800">
                            SALDO PENDIENTE PARA COMPLETAR LA CUOTA
                        </td>
                        <td class="py-3 px-5 text-right text-orange-800">$ {{ number_format($cuota->monto + ($cuota->interes_mora ?? 0) - $cuota->monto_pagado, 2) }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Estado Final y Metodo -->
        <div class="bg-gray-100 p-4 rounded-lg text-sm text-gray-700 flex justify-between items-center border border-gray-200">
            <div>
                <p><span class="font-bold text-gray-800">Forma de Pago:</span> <span class="uppercase font-medium bg-white px-2 py-1 border border-gray-300 rounded text-xs ml-1 shadow-sm">{{ str_replace('_', ' ', $cuota->metodo_pago) }}</span></p>
                <p class="mt-2"><span class="font-bold text-gray-800">Cobrado por:</span> <span class="uppercase text-xs ml-1">{{ App\Models\User::find($cuota->cobrado_por_id)->name ?? 'Sistema' }}</span></p>
            </div>
            <div class="text-right">
                <p class="font-bold text-gray-500 uppercase tracking-widest text-[10px] mb-1">Estado Posterior de la Cuota</p>
                @if($cuota->pagada)
                    <span class="inline-block bg-green-100 text-green-800 border-2 border-green-500 font-bold px-4 py-2 rounded-full uppercase tracking-wider shadow-sm">
                        <svg class="w-4 h-4 inline-block -mt-1 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        CANCELADA TOTAL
                    </span>
                @else
                    <span class="inline-block bg-orange-100 text-orange-800 border-2 border-orange-500 font-bold px-4 py-2 rounded-full uppercase tracking-wider shadow-sm">
                        PAGO PARCIAL Y/O A CUENTA
                    </span>
                @endif
            </div>
        </div>

        <!-- Firmas -->
        <div class="mt-20 pt-10 flex justify-between px-10">
            <div class="text-center w-56">
                <div class="border-t-2 border-gray-400 pt-3 text-xs text-gray-600 font-bold uppercase tracking-wider">
                    Firma Conformidad Cliente
                </div>
            </div>
            <div class="text-center w-56">
                <div class="border-t-2 border-gray-400 pt-3 text-xs text-gray-600 font-bold uppercase tracking-wider">
                    Firma y Sello Empresa Responsable
                </div>
            </div>
        </div>
        
        <!-- Botones de Acción -->
        <div class="mt-14 text-center no-print">
            <button onclick="window.print()" class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg hover:shadow-xl transition-all mr-4 transform hover:-translate-y-1">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                IMPRIMIR RECIBO AHORA
            </button>
            <button onclick="window.close()" class="inline-flex items-center bg-white border border-gray-300 hover:bg-gray-50 text-gray-800 font-bold py-3 px-8 rounded-lg shadow transition-all">
                Cerrar Ventana
            </button>
        </div>
    </div>
</body>
</html>
