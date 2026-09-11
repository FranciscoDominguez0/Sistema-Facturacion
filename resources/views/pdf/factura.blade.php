<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Factura {{ $factura->numero_factura }}</title>
    @php
        $colorPrimario = $empresa->color_primario ?: '#1A2B44';

        $logoBase64 = null;
        if ($empresa->logo_path) {
            $path = storage_path('app/public/' . $empresa->logo_path);
            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }

        $estadoSlug = strtolower($factura->estado->value);
        $colorEstadoBg = match ($estadoSlug) {
            'borrador' => '#f1f5f9',
            'emitida' => '#e0f2fe',
            'pagada' => '#dcfce7',
            'anulada' => '#fee2e2',
            'vencida' => '#fef3c7',
            default => '#f1f5f9',
        };
        $colorEstadoText = match ($estadoSlug) {
            'borrador' => '#475569',
            'emitida' => '#0284c7',
            'pagada' => '#16a34a',
            'anulada' => '#dc2626',
            'vencida' => '#d97706',
            default => '#475569',
        };
    @endphp
    <style>
        @page {
            margin: 35px 40px 40px 40px;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #334155;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
        }

        /* Utilidades de texto */
        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .font-bold {
            font-weight: bold;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        /* HEADER */
        .header-logo {
            max-height: 70px;
            max-width: 220px;
            margin-bottom: 10px;
        }

        .company-name {
            font-size: 16px;
            font-weight: bold;
            color:
                {{ $colorPrimario }}
            ;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .company-details {
            font-size: 10px;
            color: #64748b;
            line-height: 1.4;
        }

        /* Invoice Badge */
        .invoice-badge-table {
            width: auto;
            float: right;
            background-color:
                {{ $colorPrimario }}
            ;
            color: #ffffff;
            border-radius: 8px;
            padding: 15px 25px;
            text-align: center;
        }

        .invoice-badge-title {
            font-size: 26px;
            font-weight: bold;
            letter-spacing: 2px;
            margin: 0;
            line-height: 1;
        }

        .invoice-badge-num {
            font-size: 12px;
            margin-top: 5px;
            font-weight: normal;
        }

        /* Fechas y Estado */
        .meta-info-table {
            width: auto;
            float: right;
            margin-top: 15px;
            margin-bottom: 10px;
            font-size: 10px;
        }

        .meta-info-table td {
            padding: 0 10px;
            text-align: center;
            border-right: 1px solid #e2e8f0;
        }

        .meta-info-table td:last-child {
            border-right: none;
            padding-right: 0;
        }

        .meta-label {
            color: #94a3b8;
            font-weight: bold;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .meta-val {
            color: #0f172a;
            font-weight: bold;
            font-size: 11px;
        }

        /* Total a Pagar */
        .total-due-wrapper {
            float: right;
            background-color: #f1f5f9;
            border-radius: 6px;
            padding: 10px 20px;
            text-align: center;
            clear: both;
        }

        .total-due-label {
            font-size: 9px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .total-due-amount {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
        }

        /* CLIENTE */
        .client-section {
            margin-top: 20px;
            margin-bottom: 25px;
        }

        .client-label {
            font-size: 10px;
            font-weight: bold;
            color: #94a3b8;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .client-name {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .client-details-table {
            width: 100%;
            font-size: 10px;
            color: #475569;
        }

        .client-details-table td {
            padding-bottom: 3px;
        }

        .client-prefix {
            font-weight: bold;
            color:
                {{ $colorPrimario }}
            ;
            width: 25px;
            /* Evita que EML pise el correo */
        }

        /* TABLA DE ITEMS */
        .items-table {
            margin-bottom: 25px;
        }

        .items-table thead tr {
            background-color:
                {{ $colorPrimario }}
            ;
            color: #ffffff;
        }

        .items-table th {
            padding: 10px 12px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #ffffff;
        }

        .items-table td {
            padding: 12px 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        .row-even {
            background-color: #f8fafc;
        }

        .item-desc {
            font-weight: bold;
            color: #0f172a;
            font-size: 11px;
        }

        .item-discount {
            font-size: 9px;
            color: #d97706;
            margin-top: 3px;
        }

        /* TOTALES INFERIORES */
        .totals-table {
            width: 100%;
        }

        .totals-table td {
            padding: 5px 8px;
        }

        .totals-label {
            text-align: right;
            color: #64748b;
            font-weight: bold;
        }

        .totals-value {
            text-align: right;
            color: #0f172a;
            font-weight: bold;
            width: 120px;
        }

        .grand-total-bg {
            background-color:
                {{ $colorPrimario }}
            ;
            border-radius: 6px;
        }

        .grand-total-bg td {
            color: #ffffff !important;
            font-size: 14px;
            padding: 10px 12px;
        }

        /* NOTAS */
        .notes-box {
            background-color: #f8fafc;
            border-left: 3px solid
                {{ $colorPrimario }}
            ;
            padding: 12px 15px;
            border-radius: 0 4px 4px 0;
            margin-right: 30px;
        }

        .notes-title {
            font-size: 10px;
            font-weight: bold;
            color:
                {{ $colorPrimario }}
            ;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .notes-text {
            font-size: 10px;
            color: #475569;
        }

        /* FOOTER */
        .footer {
            position: fixed;
            bottom: -10px;
            left: 0;
            right: 0;
            text-align: center;
        }

        .footer-thanks {
            font-size: 11px;
            font-weight: bold;
            color:
                {{ $colorPrimario }}
            ;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .footer-line {
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            font-size: 9px;
            color: #64748b;
        }

        .footer-contact {
            margin-bottom: 5px;
            font-weight: bold;
        }

        .footer-contact span {
            margin: 0 10px;
        }
    </style>
</head>

<body>

    <!-- 1. HEADER (Logo y Datos de Empresa a la Izquierda / Factura a la Derecha) -->
    <table>
        <tr>
            <td style="width: 50%;">
                <!-- Logo -->
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" class="header-logo" alt="Logo">
                @endif

                <!-- Nombre de Empresa -->
                <div class="company-name">{{ $empresa->nombre }}</div>

                <!-- Datos de Empresa -->
                <div class="company-details">
                    @if($empresa->ruc)
                        <strong>RUC:</strong> {{ $empresa->ruc }}{{ $empresa->dv ? '-' . $empresa->dv : '' }}<br>
                    @elseif($empresa->identificacion_fiscal)
                        <strong>ID Fiscal:</strong> {{ $empresa->identificacion_fiscal }}<br>
                    @endif
                    @if($empresa->direccion)
                        {{ $empresa->direccion }}<br>
                    @endif
                    @if($empresa->telefono)
                        <strong>Tel:</strong> {{ $empresa->telefono }}<br>
                    @endif
                    @if($empresa->email)
                        <strong>Email:</strong> {{ $empresa->email }}
                    @endif
                </div>
            </td>

            <td style="width: 50%; text-align: right;">
                <!-- Bloque FACTURA -->
                <table class="invoice-badge-table">
                    <tr>
                        <td>
                            <div class="invoice-badge-title">FACTURA</div>
                            <div class="invoice-badge-num">No. {{ $factura->numero_factura }}</div>
                        </td>
                    </tr>
                </table>
                <div style="clear: both;"></div>

                <!-- Bloque Fechas y Estado -->
                <table class="meta-info-table">
                    <tr>
                        <td>
                            <div class="meta-label">Fecha Emisión</div>
                            <div class="meta-val">{{ $factura->fecha_emision->format('d/m/Y') }}</div>
                        </td>
                        @if($factura->fecha_vencimiento)
                            <td>
                                <div class="meta-label">Vencimiento</div>
                                <div class="meta-val">{{ $factura->fecha_vencimiento->format('d/m/Y') }}</div>
                            </td>
                        @endif
                        <td style="border-right: none;">
                            <div class="meta-label">Estado</div>
                            <div class="meta-val" style="color: {{ $colorEstadoText }};">
                                {{ strtoupper($factura->estado->value) }}</div>
                        </td>
                    </tr>
                </table>
                <div style="clear: both;"></div>

                <!-- Bloque Total a Pagar -->
                <div class="total-due-wrapper">
                    <div class="total-due-label">Total a Pagar</div>
                    <div class="total-due-amount">{{ $empresa->simbolo_moneda }} {{ number_format($factura->total, 2) }}
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- 2. SECCIÓN DEL CLIENTE -->
    <div class="client-section">
        <div class="client-label">Facturar a:</div>
        <div class="client-name">{{ $factura->cliente->nombre }}</div>

        <!-- Tabla anidada para alinear los prefijos (evita superposiciones) -->
        <table style="width: 60%;">
            @if($factura->cliente->identificacion)
                <tr>
                    <td class="client-prefix">ID</td>
                    <td style="font-size: 11px; color: #475569;">{{ $factura->cliente->identificacion }}</td>
                </tr>
            @endif
            @if($factura->cliente->direccion)
                <tr>
                    <td class="client-prefix">DIR</td>
                    <td style="font-size: 11px; color: #475569;">{{ $factura->cliente->direccion }}</td>
                </tr>
            @endif
            @if($factura->cliente->telefono)
                <tr>
                    <td class="client-prefix">TEL</td>
                    <td style="font-size: 11px; color: #475569;">{{ $factura->cliente->telefono }}</td>
                </tr>
            @endif
            @if($factura->cliente->email)
                <tr>
                    <td class="client-prefix">EML</td>
                    <td style="font-size: 11px; color: #475569;">{{ $factura->cliente->email }}</td>
                </tr>
            @endif
        </table>
    </div>

    <!-- 3. TABLA DE ÍTEMS -->
    <table class="items-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 7%;">SL.</th>
                <th class="text-left" style="width: 48%;">DESCRIPCIÓN</th>
                <th class="text-right" style="width: 17%;">PRECIO</th>
                <th class="text-center" style="width: 11%;">CANT.</th>
                <th class="text-right" style="width: 17%;">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($factura->items as $index => $item)
                <tr class="{{ $index % 2 === 1 ? 'row-even' : '' }}">
                    <td class="text-center" style="color: #94a3b8; font-weight: bold;">{{ $index + 1 }}.</td>
                    <td>
                        <div class="item-desc">{{ $item->descripcion }}</div>
                        @if($item->descuento_porcentaje > 0)
                            <div class="item-discount">Desc. aplicado:
                                {{ rtrim(rtrim(number_format($item->descuento_porcentaje, 2), '0'), '.') }}%</div>
                        @endif
                    </td>
                    <td class="text-right">{{ $empresa->simbolo_moneda }} {{ number_format($item->precio_unitario, 2) }}
                    </td>
                    <td class="text-center" style="font-weight: bold;">
                        {{ rtrim(rtrim(number_format($item->cantidad, 2), '0'), '.') }}</td>
                    <td class="text-right font-bold" style="color: #0f172a;">{{ $empresa->simbolo_moneda }}
                        {{ number_format($item->subtotal_linea, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- 4. TOTALES Y NOTAS -->
    <table>
        <tr>
            <!-- Columna Izquierda: Notas -->
            <td style="width: 55%;">
                @if($factura->notas)
                    <div class="notes-box">
                        <div class="notes-title">Notas / Observaciones</div>
                        <div class="notes-text">{!! nl2br(e($factura->notas)) !!}</div>
                    </div>
                @endif
            </td>

            <!-- Columna Derecha: Totales -->
            <td style="width: 45%;">
                <table class="totals-table">
                    <tr>
                        <td class="totals-label">Sub Total</td>
                        <td class="totals-value">{{ $empresa->simbolo_moneda }}
                            {{ number_format($factura->subtotal, 2) }}</td>
                    </tr>
                    @if($factura->descuento_total > 0)
                        <tr>
                            <td class="totals-label">Descuento
                                ({{ rtrim(rtrim(number_format($factura->descuento_porcentaje, 2), '0'), '.') }}%)</td>
                            <td class="totals-value" style="color: #dc2626;">-{{ $empresa->simbolo_moneda }}
                                {{ number_format($factura->descuento_total, 2) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="totals-label">{{ $empresa->impuesto_nombre ?: 'Impuesto' }}
                            ({{ rtrim(rtrim(number_format($empresa->impuesto_porcentaje, 2), '0'), '.') }}%)</td>
                        <td class="totals-value">{{ $empresa->simbolo_moneda }}
                            {{ number_format($factura->impuesto, 2) }}</td>
                    </tr>

                    <!-- Fila de espacio antes del Grand Total -->
                    <tr>
                        <td colspan="2" style="height: 5px; padding: 0;"></td>
                    </tr>

                    <!-- Grand Total Block -->
                    <tr class="grand-total-bg">
                        <td class="font-bold text-left"
                            style="color: #ffffff; padding-left: 15px; border-top-left-radius: 6px; border-bottom-left-radius: 6px;">
                            Grand Total :</td>
                        <td class="totals-value"
                            style="color: #ffffff; padding-right: 15px; border-top-right-radius: 6px; border-bottom-right-radius: 6px;">
                            {{ $empresa->simbolo_moneda }} {{ number_format($factura->total, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- 5. FOOTER -->
    <div class="footer">
        <div class="footer-thanks">Gracias por su preferencia</div>
        <div class="footer-line">
            <div class="footer-contact">
                @if($empresa->telefono) <span>TEL: {{ $empresa->telefono }}</span> @endif
                @if($empresa->email) <span>EMAIL: {{ $empresa->email }}</span> @endif
                @if($empresa->ruc || $empresa->identificacion_fiscal) <span>ID:
                {{ $empresa->ruc ?: $empresa->identificacion_fiscal }}</span> @endif
            </div>
            @if($empresa->pie_pagina_pdf)
                <div style="margin-top: 4px;">{{ $empresa->pie_pagina_pdf }}</div>
            @endif
        </div>
    </div>

</body>

</html>