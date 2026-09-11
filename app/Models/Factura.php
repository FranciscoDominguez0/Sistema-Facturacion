<?php

namespace App\Models;

use App\Enums\EstadoFactura;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    use HasFactory;

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
        'fecha_emision' => 'datetime',
        'fecha_vencimiento' => 'datetime',
        'subtotal' => 'decimal:2',
        'descuento_porcentaje' => 'decimal:2',
        'descuento_total' => 'decimal:2',
        'impuesto' => 'decimal:2',
        'total' => 'decimal:2',
        'estado' => EstadoFactura::class,
    ];

    // El cliente al que se le emitió esta factura
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    // El vendedor que realizó la venta
    public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    // Las líneas de detalle de la factura (productos o servicios vendidos)
    public function items()
    {
        return $this->hasMany(FacturaItem::class);
    }

    // Permite buscar facturas por número o por el nombre del cliente
    public function scopeBuscar($query, $search)
    {
        if (strlen($search) > 0) {
            $query->where('numero_factura', 'ilike', '%'.$search.'%')
                ->orWhereHas('cliente', function ($q) use ($search) {
                    $q->where('nombre', 'ilike', '%'.$search.'%');
                });
        }
    }
}
