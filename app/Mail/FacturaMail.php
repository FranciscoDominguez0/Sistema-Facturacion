<?php

namespace App\Mail;

use App\Models\Empresa;
use App\Models\Factura;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FacturaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Factura $factura)
    {
        $this->factura->loadMissing(['cliente', 'vendedor', 'items']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Factura '.$this->factura->numero_factura,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.factura',
        );
    }

    /**
     * Adjunta el PDF de la factura generado en memoria.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $pdfService = app(\App\Services\FacturaPdfService::class);
        $pdfBinary = $pdfService->generarPdfBinario($this->factura);

        return [
            Attachment::fromData(fn () => $pdfBinary, 'factura-'.$this->factura->numero_factura.'.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
