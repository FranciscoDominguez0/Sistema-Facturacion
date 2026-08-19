<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura {{ $factura->numero_factura }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            width: 100%;
            margin-bottom: 30px;
        }
        .header table {
            width: 100%;
            border-collapse: collapse;
        }
        .header td {
            vertical-align: top;
        }
        .logo {
            max-width: 150px;
            max-height: 80px;
        }
        .company-info {
            text-align: right;
            font-size: 11px;
            color: #555555;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #1e293b; /* Sovereign Blue */
            margin-bottom: 5px;
        }
        .invoice-title-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 15px;
            margin-bottom: 30px;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            color: #1e293b;
            margin: 0 0 5px 0;
        }
        .invoice-details {
            width: 100%;
            margin-bottom: 30px;
        }
        .invoice-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-details td {
            vertical-align: top;
            width: 50%;
        }
        .section-title {
            font-size: 10px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
            margin-bottom: 5px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 3px;
        }
        .client-name {
            font-size: 16px;
            font-weight: bold;
            margin: 5px 0;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: #f1f5f9;
            color: #475569;
            font-size: 10px;
            text-transform: uppercase;
            padding: 8px;
            text-align: left;
            border-bottom: 2px solid #cbd5e1;
        }
        .items-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e2e8f0;
        }
        .text-right {
            text-align: right !important;
        }
        .text-center {
            text-align: center !important;
        }
        .totals-box {
            width: 40%;
            float: right;
        }
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 5px 8px;
        }
        .total-row {
            font-size: 16px;
            font-weight: bold;
            background-color: #f8fafc;
            border-top: 2px solid #cbd5e1;
        }
        .footer {
            position: absolute;
            bottom: 30px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
        .notes {
            margin-top: 40px;
            font-size: 11px;
            color: #475569;
            width: 50%;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 11px;
            font-weight: bold;
            border-radius: 10px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>
    <div class="container">
        
        <div class="header">
            <table>
                <tr>
                    <td>
                        @if($empresa->logo_path)
                            <img src="{{ public_path('storage/' . $empresa->logo_path) }}" class="logo" alt="Logo">
                        @else
                            <h2 style="color: #1e293b; margin:0;">{{ $empresa->nombre }}</h2>
                        @endif
                    </td>
                    <td class="company-info">
                        <div class="company-name">{{ $empresa->nombre }}</div>
                        @if($empresa->identificacion_fiscal)
                            <div>NIT/ID: {{ $empresa->identificacion_fiscal }}</div>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <div class="invoice-title-box">
            <table>
                <tr>
                    <td style="width: 50%;">
                        <h1 class="invoice-title">FACTURA</h1>
                        <div><strong>Nº:</strong> {{ $factura->numero_factura }}</div>
                        <div style="margin-top: 5px;">
                            <strong>Estado:</strong> 
                            <span class="badge">{{ $factura->estado->value }}</span>
                        </div>
                    </td>
                    <td style="width: 50%; text-align: right; vertical-align: bottom;">
                        <div><strong>Fecha Emisión:</strong> {{ $factura->fecha_emision->format('d/m/Y') }}</div>
                        @if($factura->fecha_vencimiento)
                            <div><strong>Vencimiento:</strong> {{ $factura->fecha_vencimiento->format('d/m/Y') }}</div>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <div class="invoice-details">
            <table>
                <tr>
                    <td style="padding-right: 20px;">
                        <div class="section-title">Facturado A</div>
                        <div class="client-name">{{ $factura->cliente->nombre }}</div>
                        @if($factura->cliente->identificacion_fiscal)
                            <div>NIT/ID: {{ $factura->cliente->identificacion_fiscal }}</div>
                        @endif
                    </td>
                    <td style="padding-left: 20px;">
                        <div class="section-title">Información Adicional</div>
                        <div><strong>Vendedor:</strong> {{ $factura->vendedor->user->name }}</div>
                        <div><strong>Moneda:</strong> {{ $empresa->moneda }} ({{ $empresa->simbolo_moneda }})</div>
                    </td>
                </tr>
            </table>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th class="text-right">Cant.</th>
                    <th class="text-right">Precio Unit.</th>
                    <th class="text-right">Desc %</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($factura->items as $item)
                <tr>
                    <td>{{ $item->descripcion }}</td>
                    <td class="text-right">{{ $item->cantidad }}</td>
                    <td class="text-right">{{ $empresa->simbolo_moneda }} {{ number_format($item->precio_unitario, 2) }}</td>
                    <td class="text-right">{{ $item->descuento_porcentaje > 0 ? $item->descuento_porcentaje . '%' : '-' }}</td>
                    <td class="text-right">{{ $empresa->simbolo_moneda }} {{ number_format($item->subtotal_linea, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals-box">
            <table class="totals-table">
                <tr>
                    <td>Subtotal:</td>
                    <td class="text-right">{{ $empresa->simbolo_moneda }} {{ number_format($factura->subtotal, 2) }}</td>
                </tr>
                @if($factura->descuento_total > 0)
                <tr>
                    <td>Descuento ({{ $factura->descuento_porcentaje }}%):</td>
                    <td class="text-right">-{{ $empresa->simbolo_moneda }} {{ number_format($factura->descuento_total, 2) }}</td>
                </tr>
                @endif
                <tr>
                    <td>{{ $empresa->impuesto_nombre ?: 'Impuesto' }} ({{ $empresa->impuesto_porcentaje }}%):</td>
                    <td class="text-right">{{ $empresa->simbolo_moneda }} {{ number_format($factura->impuesto, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td>TOTAL:</td>
                    <td class="text-right">{{ $empresa->simbolo_moneda }} {{ number_format($factura->total, 2) }}</td>
                </tr>
            </table>
        </div>

        @if($factura->notas)
        <div class="notes">
            <div class="section-title">Notas</div>
            <p>{{ $factura->notas }}</p>
        </div>
        @endif

        @if($empresa->pie_pagina_pdf)
        <div class="footer">
            {{ $empresa->pie_pagina_pdf }}
        </div>
        @endif

    </div>
</body>
</html>
