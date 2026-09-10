<?php

namespace App\Livewire\Facturas;

use App\Enums\EstadoFactura;
use App\Models\Factura;
use App\Services\FacturaService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class FacturaShow extends Component
{
    public Factura $factura;

    public array $desglose_impuestos = [];

    public function mount(Factura $factura)
    {
        $this->factura = $factura->load(['cliente', 'vendedor', 'items.producto']);

        $desglose = [];
        $totalImpuestoItems = $this->factura->items->sum('impuesto_monto');
        // El ratio ajusta el desglose por el descuento global ya aplicado en factura->impuesto
        $ratio = $totalImpuestoItems > 0 ? ($this->factura->impuesto / $totalImpuestoItems) : 1;

        foreach ($this->factura->items as $item) {
            if ($item->impuesto_monto > 0) {
                $nombre = $item->impuesto_nombre ?? 'Impuesto';
                $porc = number_format($item->impuesto_porcentaje, 2).'%';
                $llave = "$nombre ($porc)";

                if (! isset($desglose[$llave])) {
                    $desglose[$llave] = 0;
                }
                $desglose[$llave] += $item->impuesto_monto * $ratio;
            }
        }

        foreach ($desglose as $llave => $monto) {
            $desglose[$llave] = round($monto, 2);
        }

        $this->desglose_impuestos = $desglose;
    }

    public function cambiarEstado(string $nuevoEstado, FacturaService $facturaService)
    {
        if (! Gate::allows('facturas.estado.cambiar')) {
            abort(403, 'No tiene permiso para cambiar el estado de la factura.');
        }

        try {
            $estadoEnum = EstadoFactura::from($nuevoEstado);
            $this->factura = $facturaService->cambiarEstado($this->factura, $estadoEnum);
            session()->flash('success', 'Estado de la factura actualizado correctamente.');
        } catch (\DomainException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.facturas.factura-show');
    }
}
