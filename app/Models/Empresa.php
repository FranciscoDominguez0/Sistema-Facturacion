<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Devuelve el único registro de empresa del sistema.
     * Si no existe, lo crea con valores por defecto.
     */
    public static function actual(): self
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
                'nombre' => 'Mi Empresa',
                'moneda' => 'USD - Dólar',
                'simbolo_moneda' => '$',
                'impuesto_nombre' => 'ITBMS',
                'impuesto_porcentaje' => 7.00,
                'prefijo_factura' => 'FAC-',
                'siguiente_numero_factura' => 1,
                'color_primario' => '#1A2B44',
            ]
        );
    }
}
