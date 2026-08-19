<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturaItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'factura_id',
        'producto_id',
        'descripcion',
        'cantidad',
        'precio_unitario',
        'descuento_porcentaje',
        'descuento_monto',
        'subtotal_linea',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'descuento_porcentaje' => 'decimal:2',
        'descuento_monto' => 'decimal:2',
        'subtotal_linea' => 'decimal:2',
    ];

    // La factura a la que pertenece esta línea
    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }

    // El producto o servicio que se está vendiendo en esta línea
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
