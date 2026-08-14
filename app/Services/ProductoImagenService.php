<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductoImagenService
{
    public function guardar(UploadedFile $imagen, ?string $imagenAnterior = null): string
    {
        Validator::make(['imagen' => $imagen], [
            'imagen' => ['image', 'max:2048'],
        ])->validate();

        if ($imagenAnterior) {
            Storage::disk('public')->delete($imagenAnterior);
        }

        return $imagen->store('productos', 'public');
    }
}
