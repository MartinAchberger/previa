<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use SuperFaktura\ApiClient\ApiClient;
use SuperFaktura\ApiClient\Contract\Invoice\CannotCreateInvoiceException;
use SuperFaktura\ApiClient\Contract\Invoice\InvoiceType;
use Throwable;

class SuperFakturaPing extends Command
{
    protected $signature = 'sf:ping';
    protected $description = 'Test connection to SuperFaktura API by issuing and immediately deleting a tiny proforma.';

    public function handle(ApiClient $api): int
    {
        $cfg = config('services.superfaktura');
        $this->line('email:        ' . ($cfg['email'] ?: '<empty>'));
        $this->line('api_key:      ' . ($cfg['api_key'] ? str_repeat('•', 8) . substr($cfg['api_key'], -4) : '<empty>'));
        $this->line('company_id:   ' . ($cfg['company_id'] ?: '<empty>'));
        $this->line('company_name: ' . $cfg['company_name']);
        $this->line('sandbox:      ' . ($cfg['sandbox'] ? 'true' : 'false'));
        $this->newLine();

        if (empty($cfg['email']) || empty($cfg['api_key']) || empty($cfg['company_id'])) {
            $this->error('Chýba email / api_key / company_id v .env. Doplň ich a skús znova.');
            return self::FAILURE;
        }

        try {
            $response = $api->invoices->create(
                invoice: [
                    'name'     => 'SF ping ' . now()->format('Y-m-d H:i:s'),
                    'type'     => InvoiceType::PROFORMA->value,
                    'variable' => '999000',
                ],
                items: [[
                    'name'       => 'Test',
                    'quantity'   => 1,
                    'unit'       => 'ks',
                    'unit_price' => 1.00,
                    'tax'        => 0,
                ]],
                client: [
                    'name'           => 'Test client',
                    'email'          => 'test@example.com',
                    'country_iso_id' => 'SK',
                ],
                settings: ['language' => 'slo', 'signature' => false],
            );

            $invoice = $response->data['data']['Invoice']
                ?? $response->data['Invoice']
                ?? [];
            $sfId   = (int) ($invoice['id'] ?? 0);
            $number = (string) ($invoice['invoice_no_formatted'] ?? '?');

            if ($sfId <= 0) {
                $this->error('SuperFaktúra nevrátila ID. Raw data:');
                $this->line(json_encode($response->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                return self::FAILURE;
            }

            $this->info("OK - vytvorená proforma #{$number} (SF id {$sfId}). Mažem...");
            $api->invoices->delete($sfId);
            $this->info('Pripojenie funguje.');
            return self::SUCCESS;
        } catch (CannotCreateInvoiceException $e) {
            $this->error('SF odmietla vytvorenie. Errors:');
            foreach ($e->getErrors() as $err) {
                $this->line('  - ' . $err);
            }
            $this->line('Message: ' . $e->getMessage());
            return self::FAILURE;
        } catch (Throwable $e) {
            $this->error(get_class($e) . ': ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
