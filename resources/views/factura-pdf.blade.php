<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Factura {{ $factura->numero_factura }}</title>
    @vite(['resources/css/app.css'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @php
        $colorPrimario = $empresa->color_primario ?: '#2563eb'; // Un azul vibrante por defecto similar a la imagen
        
        $logoBase64 = null;
        if($empresa->logo_path) {
            $path = storage_path('app/public/' . $empresa->logo_path);
            if(file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }
    @endphp
    <style>
        :root {
            --color-primary: {{ $colorPrimario }};
            --color-dark: #374151; /* Gris oscuro para el header de la tabla */
            --color-light: #f3f4f6; /* Gris muy claro para filas de tabla */
        }
        body {
            font-family: 'Montserrat', sans-serif;
            color: #1f2937;
            -webkit-font-smoothing: antialiased;
            background-color: white;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            /* A4 size */
            width: 794px;
            height: 1123px;
            position: relative;
        }

        /* Utilidades de color personalizadas */
        .text-primary { color: var(--color-primary); }
        .bg-primary { background-color: var(--color-primary); }
        
        /* Formas específicas del diseño */
        .top-banner {
            background-color: var(--color-primary);
            border-top-left-radius: 40px;
            border-bottom-left-radius: 40px;
            padding: 24px 40px 24px 60px;
            color: white;
            display: inline-block;
        }

        .bottom-left-shape {
            background-color: var(--color-primary);
            border-top-right-radius: 60px;
            width: 140px;
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Tabla personalizada para lograr el efecto partido */
        table { border-collapse: separate; border-spacing: 0 8px; width: 100%; }
        
        thead tr th {
            padding: 14px 16px;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.05em;
            color: white;
        }
        
        .th-dark {
            background-color: var(--color-dark);
        }
        
        .th-primary {
            background-color: var(--color-primary);
        }

        /* Efecto de corte diagonal en el header de la tabla */
        .th-cut-left {
            position: relative;
            background-color: var(--color-primary);
        }
        .th-cut-left::before {
            content: '';
            position: absolute;
            left: -15px;
            top: 0;
            bottom: 0;
            width: 30px;
            background-color: var(--color-primary);
            transform: skewX(-20deg);
            z-index: -1;
        }

        tbody tr {
            background-color: transparent;
        }
        tbody tr.striped {
            background-color: var(--color-light);
        }
        tbody td {
            padding: 16px;
            font-size: 12px;
            font-weight: 600;
        }
        tbody tr td:first-child {
            border-top-left-radius: 20px;
            border-bottom-left-radius: 20px;
            padding-left: 24px;
        }
        tbody tr td:last-child {
            border-top-right-radius: 20px;
            border-bottom-right-radius: 20px;
            padding-right: 24px;
        }

        /* Ocultar barras de desplazamiento */
        ::-webkit-scrollbar { display: none; }
        
        /* ESTILOS PARA LA VISTA PREVIA EN PANTALLA (HTML) */
        @media screen {
            html {
                background-color: #f1f5f9;
                display: flex;
                justify-content: center;
                padding: 2rem 0;
                overflow-x: hidden;
            }
            body {
                width: 794px !important;
                height: 1123px !important; /* El alto exacto de un A4 */
                background-color: white;
                box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
                transform-origin: top center;
                /* Escala hacia abajo si el contenedor es menor a 850px para evitar scroll horizontal */
                zoom: calc(min(1, 100vw / 850));
            }
        }

        /* ESTILOS DE IMPRESIÓN / PDF */
        @media print {
            @page { margin: 0; size: A4; }
            html, body {
                width: 794px !important;
                height: 1123px !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: hidden !important;
                background-color: white !important;
            }
            .bottom-left-shape {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            .top-banner {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>
<body class="flex flex-col justify-between box-border">
    
    <div>
        <!-- HEADER ROW -->
        <header class="flex justify-between items-start pt-8">
            <!-- Logo Izquierda -->
            <div class="pl-12 pt-4 flex flex-col max-w-[50%]">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" class="max-h-16 object-contain object-left mb-3" alt="Logo">
                @else
                    <div class="flex items-center gap-3 text-primary mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg>
                        <h1 class="text-3xl font-black tracking-tight uppercase leading-none">{{ $empresa->nombre }}<br><span class="text-xs tracking-[0.2em] font-bold text-gray-500">Facturación</span></h1>
                    </div>
                @endif
                
                <div class="text-[11px] text-gray-600 mt-2 leading-relaxed">
                    @if($logoBase64) <p class="font-extrabold text-gray-900 text-sm tracking-tight mb-1">{{ $empresa->nombre }}</p> @endif
                    
                    @if($empresa->ubicacion)
                        <p class="max-w-[250px]">{{ $empresa->ubicacion }}</p>
                    @endif
                    
                    <p class="mt-1">
                        @if($empresa->telefono) <span class="font-semibold text-gray-900">{{ $empresa->telefono }}</span> @endif
                        @if($empresa->telefono && $empresa->email) <span class="mx-1.5 text-gray-300">|</span> @endif
                        @if($empresa->email) <span class="font-semibold text-gray-900">{{ $empresa->email }}</span> @endif
                    </p>
                    
                    @if($empresa->ruc || $empresa->identificacion_fiscal)
                        <p class="mt-0.5">RUC: <span class="font-medium">{{ $empresa->ruc ?: $empresa->identificacion_fiscal }}{{ $empresa->dv ? '-' . $empresa->dv : '' }}</span></p>
                    @endif
                </div>
            </div>

            <!-- Banner INVOICE Derecha -->
            <div class="top-banner text-right shadow-md">
                <h2 class="text-4xl font-black tracking-widest uppercase mb-1">FACTURA</h2>
                <p class="text-base font-semibold">N° {{ $factura->numero_factura }}</p>
            </div>
        </header>

        <!-- INFO ROW -->
        <section class="flex justify-between items-start px-12 mt-8 mb-6">
            <!-- Facturar A -->
            <div class="w-1/2">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">FACTURAR A</h3>
                <h4 class="text-xl font-black text-gray-800 mb-1 leading-none">{{ $factura->cliente->nombre }}</h4>
                <p class="text-sm font-semibold text-gray-500 mb-4">{{ $factura->cliente->identificacion ?? 'Cliente' }}</p>
                
                <div class="grid grid-cols-[20px_1fr] gap-y-1 text-xs font-semibold text-gray-700">
                    @if($factura->cliente->direccion)
                        <span class="font-bold text-gray-900">A</span> <span>{{ $factura->cliente->direccion }}</span>
                    @endif
                    @if($factura->cliente->email)
                        <span class="font-bold text-gray-900">W</span> <span>{{ $factura->cliente->email }}</span>
                    @endif
                    @if($factura->cliente->telefono)
                        <span class="font-bold text-gray-900">P</span> <span>{{ $factura->cliente->telefono }}</span>
                    @endif
                </div>
            </div>

            <!-- Fechas y Total Due -->
            <div class="w-1/2 flex flex-col items-end">
                <div class="flex gap-8 mb-4">
                    <div class="flex flex-col">
                        <span class="text-xs font-bold text-gray-700 mb-1">Fecha Emisión</span>
                        <span class="text-sm font-bold text-gray-500">{{ $factura->fecha_emision->format('d-m-Y') }}</span>
                    </div>
                    <div class="w-px bg-gray-300 h-8 self-center"></div>
                    <div class="flex flex-col">
                        <span class="text-xs font-bold text-gray-700 mb-1">Vencimiento</span>
                        <span class="text-sm font-bold text-gray-500">{{ $factura->fecha_vencimiento ? $factura->fecha_vencimiento->format('d-m-Y') : $factura->fecha_emision->format('d-m-Y') }}</span>
                    </div>
                </div>

                <!-- Total Due Box -->
                <div class="bg-gray-100 rounded-xl px-8 py-4 flex flex-col items-center min-w-[200px]">
                    <span class="text-xs font-bold text-gray-500 mb-1">Total a Pagar:</span>
                    <span class="text-2xl font-black text-gray-800">{{ number_format($factura->total, 2) }} {{ $empresa->simbolo_moneda ?: 'EUR' }}</span>
                </div>
            </div>
        </section>

        <!-- ITEMS TABLE -->
        <section class="px-12 mb-6">
            <table>
                <thead>
                    <tr>
                        <th class="th-dark text-left w-[10%] rounded-l-full pl-6">N°</th>
                        <th class="th-dark text-left w-[40%]">DESCRIPCIÓN</th>
                        <!-- La celda azul con el corte a la izquierda -->
                        <th class="th-cut-left text-center w-[15%]">PRECIO</th>
                        <th class="th-primary text-center w-[15%]">CANT.</th>
                        <th class="th-primary text-right w-[20%] rounded-r-full pr-6">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($factura->items as $index => $item)
                    <tr class="{{ $index % 2 !== 0 ? 'striped' : '' }}">
                        <td class="text-center">{{ $index + 1 }}.</td>
                        <td>
                            <p class="font-bold text-gray-800 text-[13px]">{{ $item->descripcion }}</p>
                            @if($item->descuento_porcentaje > 0)
                                <p class="text-[10px] text-gray-500 font-medium leading-tight mt-0.5">Descuento aplicado: {{ rtrim(rtrim(number_format($item->descuento_porcentaje, 2), '0'), '.') }}%</p>
                            @endif
                        </td>
                        <td class="text-center text-gray-600">{{ $empresa->simbolo_moneda }} {{ number_format($item->precio_unitario, 2) }}</td>
                        <td class="text-center text-gray-800">{{ rtrim(rtrim(number_format($item->cantidad, 2), '0'), '.') }}</td>
                        <td class="text-right text-gray-800">{{ $empresa->simbolo_moneda }} {{ number_format($item->subtotal_linea, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        <!-- BOTTOM SECTION -->
        <section class="flex justify-between items-end px-12 pb-6 relative z-10">
            <!-- Izquierda: Terms -->
            <div class="w-[50%] pr-8 flex flex-col gap-6">
                @if($factura->notas)
                <div>
                    <h5 class="text-[13px] font-bold text-gray-800 mb-2">Términos y Notas</h5>
                    <p class="text-[10px] text-gray-500 font-medium leading-snug pr-10">
                        {!! nl2br(e($factura->notas)) !!}
                    </p>
                </div>
                @endif
            </div>

            <!-- Derecha: Totals -->
            <div class="w-[45%] flex flex-col gap-3">
                <div class="flex justify-between text-xs px-2 border-b border-gray-200 pb-2">
                    <span class="font-bold text-gray-800">Subtotal</span>
                    <span class="font-bold text-gray-600">{{ $empresa->simbolo_moneda }} {{ number_format($factura->subtotal, 2) }}</span>
                </div>
                
                <div class="flex justify-between text-xs px-2 border-b border-gray-200 pb-2">
                    <span class="font-bold text-gray-800">{{ $empresa->impuesto_nombre ?: 'Impuesto' }} {{ rtrim(rtrim(number_format($empresa->impuesto_porcentaje, 2), '0'), '.') }}%</span>
                    <span class="font-bold text-gray-600">{{ $empresa->simbolo_moneda }} {{ number_format($factura->impuesto, 2) }}</span>
                </div>

                @if($factura->descuento_total > 0)
                <div class="flex justify-between text-xs px-2 border-b border-gray-200 pb-2">
                    <span class="font-bold text-gray-800">Descuento</span>
                    <span class="font-bold text-gray-600">-{{ $empresa->simbolo_moneda }} {{ number_format($factura->descuento_total, 2) }}</span>
                </div>
                @endif

                <!-- Grand Total Banner -->
                <div class="bg-primary text-white rounded-lg flex justify-between items-center px-6 py-4 mt-1">
                    <span class="font-bold text-sm">Total a Pagar :</span>
                    <span class="font-black text-lg">{{ $empresa->simbolo_moneda }} {{ number_format($factura->total, 2) }}</span>
                </div>
            </div>
        </section>
    </div>

    <!-- FOOTER -->
    <footer class="mt-auto w-full flex items-end">
        <!-- QR / Decoration Box -->
        <div class="bottom-left-shape shrink-0 h-[100px]">
            <!-- Espacio vacío, se removió el QR -->
        </div>

        <!-- Contact Info -->
        <div class="flex-grow pb-6 px-8 pl-12 flex flex-col justify-end">
            <h6 class="text-center text-[11px] font-black text-gray-800 uppercase tracking-widest mb-6">GRACIAS POR SU COMPRA</h6>
            
            <div class="flex justify-between items-center text-[10px] font-bold text-gray-700">
                <!-- Phone -->
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full border border-gray-300 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" /></svg>
                    </div>
                    <span>{{ $empresa->telefono ?? '123 4567 890' }}</span>
                </div>
                
                <!-- Email/Web -->
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full border border-gray-300 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" /><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" /></svg>
                    </div>
                    <span>{{ $empresa->email ?? 'Company@website.com' }}</span>
                </div>

                <!-- Address -->
                <div class="flex items-center gap-2 max-w-[140px]">
                    <div class="w-7 h-7 rounded-full border border-gray-300 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /></svg>
                    </div>
                    <span class="leading-tight">{{ $empresa->ubicacion ?? '123, Main Street, New York' }}</span>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
