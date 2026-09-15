<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Generic owner alert. Intentionally NOT queued — see App\Support\AdminNotifier. */
class AdminAlertMail extends Mailable
{
    public function __construct(
        public string $subjectLine,
        public string $intro,
        public array $rows = [],
        public ?string $url = null,
        public ?string $urlLabel = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '[Upozornenie] ' . $this->subjectLine);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.admin-alert', with: [
            'subjectLine' => $this->subjectLine,
            'intro'       => $this->intro,
            'rows'        => $this->rows,
            'url'         => $this->url,
            'urlLabel'    => $this->urlLabel,
        ]);
    }
}
