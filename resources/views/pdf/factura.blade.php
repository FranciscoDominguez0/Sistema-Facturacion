<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Factura {{ $factura->numero_factura }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 13px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            width: 100%;
            margin-bottom: 30px;
        }
        .header td {
            vertical-align: top;
        }
        .logo {
            max-width: 120px;
            margin-bottom: 15px;
            object-fit: contain;
        }
        .company-info {
            color: #555;
            line-height: 1.6;
        }
        .invoice-title-container {
            text-align: right;
        }
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: {{ $empresa->color_primario ?? '#002349' }};
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .invoice-meta {
            font-size: 13px;
            color: #555;
            line-height: 1.6;
        }
        .billing-section {
            width: 100%;
            margin-bottom: 30px;
        }
        .billing-section td {
            vertical-align: top;
            width: 50%;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: {{ $empresa->color_primario ?? '#002349' }};
            border-bottom: 2px solid {{ $empresa->color_primario ?? '#002349' }};
            padding-bottom: 5px;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .customer-info {
            line-height: 1.6;
            color: #444;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            background-color: #f4f6f8;
            color: {{ $empresa->color_primario ?? '#002349' }};
            font-weight: bold;
            text-align: left;
            padding: 12px 10px;
            border-bottom: 2px solid {{ $empresa->color_primario ?? '#002349' }};
            font-size: 12px;
            text-transform: uppercase;
        }
        .items-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #eee;
            color: #333;
            vertical-align: top;
        }
        .items-table .text-right {
            text-align: right;
        }
        .items-table .text-center {
            text-align: center;
        }
        .items-table tr:nth-child(even) td {
            background-color: #fcfcfc;
        }
        .totals-container {
            width: 100%;
        }
        .totals-table {
            width: 45%;
            float: right;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 8px 12px;
            color: #333;
        }
        .totals-table .label {
            font-weight: bold;
            color: #555;
        }
        .totals-table .amount {
            text-align: right;
        }
        .totals-table .total-row td {
            font-size: 18px;
            font-weight: bold;
            color: {{ $empresa->color_primario ?? '#002349' }};
            border-top: 2px solid {{ $empresa->color_primario ?? '#002349' }};
            padding-top: 15px;
            margin-top: 5px;
        }
        .footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 11px;
            color: #888;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        .badge {
            font-size: 12px;
            font-weight: bold;
        }
        .notes {
            margin-top: 40px;
            padding: 15px;
            background-color: #f9f9f9;
            border-left: 4px solid {{ $empresa->color_primario ?? '#002349' }};
            font-size: 12px;
            color: #555;
            width: 50%;
            float: left;
        }
    </style>
</head>
<body>

    <table class="header">
        <tr>
            <td style="width: 50%;">
                @php
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
                
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" class="logo" alt="{{ $empresa->nombre }}">
                @else
                    <h2 style="color: {{ $empresa->color_primario ?? '#002349' }}; margin-top:0; font-size:24px;">{{ $empresa->nombre }}</h2>
                @endif
                
                <div class="company-info">
                    <strong>{{ $empresa->nombre }}</strong><br>
                    @if($empresa->identificacion_fiscal)
                        RUC/ID: {{ $empresa->identificacion_fiscal }}<br>
                    @endif
                    Moneda: {{ $empresa->moneda }} ({{ $empresa->simbolo_moneda }})
                </div>
            </td>
            <td style="width: 50%;" class="invoice-title-container">
                <div class="invoice-title">Factura</div>
                <div class="invoice-meta">
                    @php
                        $estadoSlug = strtolower($factura->estado->value);
                        $colorEstado = match($estadoSlug) {
                            'borrador'  => '#6b7280',
                            'emitida'   => '#059669', // Verde
                            'pagada'    => '#16a34a',
                            'anulada'   => '#dc2626', // Rojo
                            'vencida'   => '#d97706', // Naranja
                            default     => '#4b5563',
                        };
                    @endphp
                    <strong>Nº de Factura:</strong> {{ $factura->numero_factura }}<br>
                    <strong>Fecha de Emisión:</strong> {{ $factura->fecha_emision->format('d M, Y') }}<br>
                    @if($factura->fecha_vencimiento)
                        <strong>Vencimiento:</strong> {{ $factura->fecha_vencimiento->format('d M, Y') }}<br>
                    @endif
                    <strong>Estado:</strong> <span class="badge" style="color: {{ $colorEstado }};">{{ strtoupper($factura->estado->value) }}</span>
                </div>
            </td>
        </tr>
    </table>

    <table class="billing-section">
        <tr>
            <td style="padding-right: 30px;">
                <div class="section-title">Facturar a</div>
                <div class="customer-info">
                    <strong>{{ $factura->cliente->nombre }}</strong><br>
                    @if($factura->cliente->identificacion_fiscal)
                        RUC/ID: {{ $factura->cliente->identificacion_fiscal }}<br>
                    @endif
                    @if($factura->cliente->email)
                        {{ $factura->cliente->email }}<br>
                    @endif
                    @if($factura->cliente->telefono)
                        Tel: {{ $factura->cliente->telefono }}
                    @endif
                </div>
            </td>
            <td>
                <div class="section-title">Información Adicional</div>
                <div class="customer-info">
                    <strong>Vendedor:</strong> {{ $factura->vendedor->name }}<br>
                </div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 10%; text-align: center;">CANT</th>
                <th style="width: 45%;">DESCRIPCIÓN</th>
                <th style="width: 20%; text-align: right;">PRECIO UNIT.</th>
                <th style="width: 25%; text-align: right;">SUBTOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($factura->items as $item)
            <tr>
                <td class="text-center">{{ rtrim(rtrim(number_format($item->cantidad, 2), '0'), '.') }}</td>
                <td>
                    <strong>{{ $item->descripcion }}</strong>
                    @if($item->descuento_porcentaje > 0)
                        <div style="font-size: 11px; color: #777; margin-top: 3px;">
                            Desc. aplicado: {{ $item->descuento_porcentaje }}%
                        </div>
                    @endif
                </td>
                <td class="text-right">{{ $empresa->simbolo_moneda }}{{ number_format($item->precio_unitario, 2) }}</td>
                <td class="text-right">{{ $empresa->simbolo_moneda }}{{ number_format($item->subtotal_linea, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-container">
        @if($factura->notas)
        <div class="notes">
            <strong>Notas Adicionales:</strong><br>
            {!! nl2br(e($factura->notas)) !!}
        </div>
        @endif
        
        <table class="totals-table">
            <tr>
                <td class="label">Subtotal</td>
                <td class="amount">{{ $empresa->simbolo_moneda }}{{ number_format($factura->subtotal, 2) }}</td>
            </tr>
            @if($factura->descuento_total > 0)
            <tr>
                <td class="label">Descuento ({{ $factura->descuento_porcentaje }}%)</td>
                <td class="amount" style="color: #059669;">-{{ $empresa->simbolo_moneda }}{{ number_format($factura->descuento_total, 2) }}</td>
            </tr>
            @endif
            <tr>
                <td class="label">{{ $empresa->impuesto_nombre ?: 'Impuesto' }} ({{ $empresa->impuesto_porcentaje }}%)</td>
                <td class="amount">{{ $empresa->simbolo_moneda }}{{ number_format($factura->impuesto, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td class="label">TOTAL</td>
                <td class="amount">{{ $empresa->simbolo_moneda }}{{ number_format($factura->total, 2) }}</td>
            </tr>
        </table>
        <div style="clear: both;"></div>
    </div>

    <div class="footer">
        @if($empresa->pie_pagina_pdf)
            {{ $empresa->pie_pagina_pdf }}<br>
        @endif
        <strong>{{ $empresa->nombre }}</strong>
    </div>

</body>
</html>
