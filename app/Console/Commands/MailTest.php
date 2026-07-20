<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

class MailTest extends Command
{
    protected $signature = 'mail:test {to? : Recipient (default: MAIL_ADMIN_ADDRESS)}';

    protected $description = 'Send a test e-mail to verify the SMTP connection';

    public function handle(): int
    {
        $to = $this->argument('to') ?: config('mail.admin_address');

        $this->line('Mailer:  ' . config('mail.default'));
        $this->line('From:    ' . config('mail.from.address') . ' (' . config('mail.from.name') . ')');
        $this->line('To:      ' . $to);

        try {
            // Sent synchronously (no queue) so the result is visible immediately.
            Mail::raw(
                "Toto je testovací e-mail z webu PREVIA.\n\nAk ho vidíte, SMTP je správne napojené.",
                function ($m) use ($to) {
                    $m->to($to)->subject('Test SMTP - PREVIA');
                }
            );
        } catch (Throwable $e) {
            $this->error('Odoslanie zlyhalo: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->info('Test e-mail odoslaný na ' . $to . '. Skontrolujte schránku (aj spam).');
        return self::SUCCESS;
    }
}
