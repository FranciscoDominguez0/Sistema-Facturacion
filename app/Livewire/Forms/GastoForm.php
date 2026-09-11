<?php

namespace App\Livewire\Forms;

use App\Models\Gasto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Form;

class GastoForm extends Form
{
    public string $concepto = '';

    public string $categoria = '';

    public string $monto = '';

    public string $fecha = '';

    public string $comprobante = '';

    public function rules(): array
    {
        return [
            'concepto' => ['required', 'string', 'max:255'],
            'categoria' => ['required', Rule::in(config('gastos.categorias'))],
            'monto' => ['required', 'numeric', 'min:0.01', 'max:'.config('gastos.monto_maximo')],
            'fecha' => ['required', 'date'],
            'comprobante' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'concepto.required' => 'El concepto es obligatorio.',
            'categoria.required' => 'Selecciona una categoría.',
            'categoria.in' => 'La categoría seleccionada no es válida.',
            'monto.required' => 'El monto es obligatorio.',
            'monto.numeric' => 'El monto debe ser un número.',
            'monto.min' => 'El monto debe ser mayor a 0.',
            'monto.max' => 'El monto excede el máximo permitido.',
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date' => 'La fecha no es válida.',
        ];
    }

    /**
     * Llena el formulario con los datos de un gasto existente para editarlo.
     */
    public function cargarGasto(Gasto $gasto): void
    {
        $this->concepto = $gasto->concepto;
        $this->categoria = $gasto->categoria;
        $this->monto = $gasto->monto;
        $this->fecha = $gasto->fecha->format('Y-m-d');
        $this->comprobante = $gasto->comprobante ?? '';
    }

    public function guardar(): void
    {
        $this->validate();

        DB::transaction(function () {
            Gasto::create([
                'concepto' => $this->concepto,
                'categoria' => $this->categoria,
                'monto' => $this->monto,
                'fecha' => $this->fecha,
                'comprobante' => $this->comprobante ?: null,
                'registrado_por' => Auth::id(),
            ]);
        });
    }

    /**
     * Actualiza el gasto con los datos del formulario.
     */
    public function actualizar(Gasto $gasto): void
    {
        $this->validate();

        DB::transaction(function () use ($gasto) {
            $gasto->update([
                'concepto' => $this->concepto,
                'categoria' => $this->categoria,
                'monto' => $this->monto,
                'fecha' => $this->fecha,
                'comprobante' => $this->comprobante ?: null,
            ]);
        });
    }
}
