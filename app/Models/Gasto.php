<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gasto extends Model
{
    use HasFactory;

    protected $fillable = [
        'concepto',
        'categoria',
        'monto',
        'fecha',
        'registrado_por',
        'comprobante',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha' => 'date',
    ];

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function montoFormateado(): Attribute
    {
        return Attribute::get(fn () => '$'.number_format((float) $this->monto, 2));
    }

    public function scopeRecientes(Builder $query): void
    {
        $query->orderBy('fecha', 'desc')->orderBy('id', 'desc');
    }
}
