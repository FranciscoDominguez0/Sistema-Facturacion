<?php

namespace App\Mail;

use App\Models\Empresa;
use App\Models\Factura;
use Barryvdh\DomPDF\Facade\Pdf;
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
        $pdf = Pdf::loadView('pdf.factura', [
            'factura' => $this->factura,
            'empresa' => Empresa::actual(),
        ])->output();

        return [
            Attachment::fromData(fn () => $pdf, 'factura-'.$this->factura->numero_factura.'.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
