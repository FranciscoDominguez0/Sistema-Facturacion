<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Factura {{ $factura->numero_factura }}</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; color: #1e293b; line-height: 1.6;">
    <h2 style="margin-bottom: 4px;">Factura {{ $factura->numero_factura }}</h2>
    <p style="margin-top: 0; color: #64748b;">{{ $factura->fecha_emision->format('d/m/Y') }}</p>

    <p>Hola {{ $factura->cliente->nombre }},</p>

    <p>
        Adjuntamos la factura <strong>{{ $factura->numero_factura }}</strong> por un total de
        <strong>${{ number_format($factura->total, 2) }}</strong>.
    </p>

    <p>Gracias por su preferencia.</p>

    <p style="color: #64748b; font-size: 13px; margin-top: 32px;">
        Este correo fue enviado desde el sistema de facturación.
    </p>
</body>
</html>