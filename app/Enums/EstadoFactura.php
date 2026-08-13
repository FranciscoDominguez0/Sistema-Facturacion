<?php

namespace App\Enums;

enum EstadoFactura: string
{
    case PENDIENTE = 'Pendiente';
    case PAGADA = 'Pagada';
    case ANULADA = 'Anulada';
}
