<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'precio' => 'decimal:2',
        'aplica_impuesto' => 'boolean',
        'activo' => 'boolean',
    ];

    /**
     * Scope a query to only include active products.
     */
    public function scopeActivos(\Illuminate\Database\Eloquent\Builder $query): void
    {
        $query->where('activo', true);
    }
}
