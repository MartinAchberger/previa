<?php

namespace App\Mail;

use App\Models\B2bUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNewRegistrationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public B2bUser $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Nová B2B registrácia: ' . $this->user->salon_name);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.admin-new-registration', with: ['user' => $this->user]);
    }
}
