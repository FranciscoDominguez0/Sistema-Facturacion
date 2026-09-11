<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    /**
     * Atributos asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'identificacion_fiscal',
        'email',
        'telefono',
        'ruc',
        'dv',
        'logo_path',
        'moneda',
        'simbolo_moneda',
        'impuesto_nombre',
        'impuesto_porcentaje',
        'prefijo_factura',
        'siguiente_numero_factura',
        'color_primario',
        'pie_pagina_pdf',
    ];

    /**
     * Devuelve el único registro de empresa del sistema.
     * Si no existe, lo crea con valores por defecto.
     */
    public static function actual(): self
    {
        return self::first() ?? self::create([
            'nombre' => 'Mi Empresa',
            'moneda' => 'USD - Dólar',
            'simbolo_moneda' => '$',
            'impuesto_nombre' => 'ITBMS',
            'impuesto_porcentaje' => 7.00,
            'prefijo_factura' => 'FAC-',
            'siguiente_numero_factura' => 1,
            'color_primario' => '#1A2B44',
        ]);
    }
}
