<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'identificacion',
        'email',
        'telefono',
        'direccion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function facturas(): HasMany
    {
        return $this->hasMany(Factura::class);
    }
}
