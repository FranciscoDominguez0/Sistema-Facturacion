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
        'tipo',
        'aplica_impuesto',
        'imagen_path',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
            'aplica_impuesto' => 'boolean',
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
