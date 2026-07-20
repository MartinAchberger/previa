<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SuperFakturaCompanies extends Command
{
    protected $signature = 'sf:companies';
    protected $description = 'List SuperFaktura companies for the configured email/api_key (no company_id needed).';

    public function handle(): int
    {
        $cfg = config('services.superfaktura');

        if (empty($cfg['email']) || empty($cfg['api_key'])) {
            $this->error('Doplň najprv SUPERFAKTURA_EMAIL a SUPERFAKTURA_API_KEY do .env.');
            return self::FAILURE;
        }

        $base = ($cfg['sandbox'] ?? true) ? 'https://sandbox.superfaktura.sk' : 'https://moja.superfaktura.sk';

        $auth = 'SFAPI ' . http_build_query([
            'email'      => $cfg['email'],
            'apikey'     => $cfg['api_key'],
            'company_id' => 0,
            'module'     => 'API [' . ($cfg['company_name'] ?? 'app') . ']',
        ]);

        $endpoints = ['/users/listInfo', '/users/index.json', '/users'];

        foreach ($endpoints as $path) {
            $this->line("Skúšam {$base}{$path} ...");
            $res = Http::withHeaders(['Authorization' => $auth])
                ->acceptJson()
                ->get($base . $path);

            if (!$res->successful()) {
                $this->line("  HTTP {$res->status()}");
                continue;
            }

            $json = $res->json();
            $companies = $this->extractCompanies($json);
            if (!$companies) {
                $this->line('  (response neobsahuje zoznam firiem)');
                continue;
            }

            $this->newLine();
            $this->info('Firmy dostupné s týmito kľúčmi:');
            $this->table(['ID', 'Názov', 'IČO', 'Krajina'], $companies);
            $this->newLine();
            $this->line('Daj zvolené ID do .env ako SUPERFAKTURA_COMPANY_ID a spusti `php artisan sf:ping`.');
            return self::SUCCESS;
        }

        $this->error('Nepodarilo sa získať zoznam firiem. Pozri sa do SF webu - po login je company_id v URL: companies/edit/{ID}.');
        return self::FAILURE;
    }

    private function extractCompanies(mixed $json): array
    {
        if (!is_array($json)) return [];

        $candidates = [];
        if (isset($json['UserCompany']) && is_array($json['UserCompany'])) {
            $candidates = $json['UserCompany'];
        } elseif (isset($json['Company']) && is_array($json['Company'])) {
            $candidates = is_array($json['Company'][0] ?? null) ? $json['Company'] : [$json['Company']];
        } elseif (isset($json['data']) && is_array($json['data'])) {
            $candidates = $json['data'];
        } else {
            return [];
        }

        $rows = [];
        foreach ($candidates as $c) {
            $company = $c['Company'] ?? $c;
            if (!is_array($company)) continue;
            $rows[] = [
                $company['id'] ?? $company['company_id'] ?? '?',
                $company['name'] ?? '',
                $company['ico'] ?? '',
                $company['country_iso_id'] ?? $company['country'] ?? '',
            ];
        }
        return $rows;
    }
}
