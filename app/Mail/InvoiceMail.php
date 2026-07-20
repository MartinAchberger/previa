<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class InvoiceMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        $isProforma = !$this->order->hasInvoice() && $this->order->hasProforma();
        $label = $isProforma ? 'Proforma faktúra' : 'Faktúra';
        $number = $this->order->activeDocumentNumber() ?: $this->order->order_number;

        return new Envelope(
            subject: $label . ' ' . $number . ' - PREVIA',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice',
            with: [
                'order'       => $this->order,
                'isProforma'  => !$this->order->hasInvoice() && $this->order->hasProforma(),
                'docNumber'   => $this->order->activeDocumentNumber() ?: $this->order->order_number,
            ],
        );
    }

    public function attachments(): array
    {
        $path = $this->order->activeDocumentPdfPath();
        if (!$path) return [];

        $absolute = Storage::disk('local')->path($path);
        $name = ($this->order->activeDocumentNumber() ?: $this->order->order_number) . '.pdf';

        return [
            Attachment::fromPath($absolute)->as($name)->withMime('application/pdf'),
        ];
    }
}
