<?php

namespace App\Support;

use App\Mail\AdminAlertMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Operational alerts for the shop owner (MAIL_ADMIN_ADDRESS): things that need
 * a human to step in — warehouse push failed, invoice not issued, stock low.
 * Sent synchronously (not queued) so they arrive even when the queue worker
 * is down. Never throws: a failed alert is logged and swallowed.
 */
class AdminNotifier
{
    /**
     * @param array<string,string> $rows  Label => value pairs shown as a table.
     */
    public static function alert(string $subject, string $intro, array $rows = [], ?string $url = null, ?string $urlLabel = null): void
    {
        $to = config('mail.admin_address');
        if (!filled($to)) {
            Log::warning('Admin alert skipped — no MAIL_ADMIN_ADDRESS', ['subject' => $subject]);
            return;
        }

        try {
            Mail::to($to)->send(new AdminAlertMail($subject, $intro, $rows, $url, $urlLabel));
        } catch (Throwable $e) {
            Log::error('Admin alert failed', ['subject' => $subject, 'error' => $e->getMessage()]);
        }
    }
}
