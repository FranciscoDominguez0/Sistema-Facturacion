<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'codigo',
        'precio',
        'descuento_porcentaje',
        'tipo',
        'impuesto_id',
        'imagen_path',
        'activo',
    ];

    public function impuesto()
    {
        return $this->belongsTo(Impuesto::class);
    }

    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
            'descuento_porcentaje' => 'decimal:2',
            'activo' => 'boolean',
        ];
    }

    protected function imagenPath(): Attribute
    {
        return Attribute::set(fn ($value) => $value === '' ? null : $value);
    }

    public function scopeActivos(Builder $query): void
    {
        $query->where('activo', true);
    }
}
