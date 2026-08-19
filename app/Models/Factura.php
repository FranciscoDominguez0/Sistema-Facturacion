<?php

namespace App\Models;

use App\Enums\EstadoFactura;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    protected $fillable = [
        'numero_factura',
        'cliente_id',
        'vendedor_id',
        'fecha_emision',
        'fecha_vencimiento',
        'subtotal',
        'descuento_porcentaje',
        'descuento_total',
        'impuesto',
        'total',
        'estado',
        'notas',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'fecha_vencimiento' => 'date',
        'subtotal' => 'decimal:2',
        'descuento_porcentaje' => 'decimal:2',
        'descuento_total' => 'decimal:2',
        'impuesto' => 'decimal:2',
        'total' => 'decimal:2',
        'estado' => EstadoFactura::class,
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function vendedor()
    {
        return $this->belongsTo(Vendedor::class);
    }

    public function items()
    {
        return $this->hasMany(FacturaItem::class);
    }
}
