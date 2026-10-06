<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

/**
 * CSV of every warehouse item for the fulfilment partner (Foxlog). "interne_id" is
 * the database id (shade rows: "<id>-<shade code>"), "kod" the product number on invoices:
 * one row per product and one row per colour shade (each shade is its own
 * stock item with its own SKU). Runs on the server so it reflects live data.
 *
 *   php artisan products:export-csv            → storage/app/products-export.csv
 *   php artisan products:export-csv --stdout   → prints CSV
 */
class ProductsExportCsv extends Command
{
    protected $signature = 'products:export-csv {--stdout : Print to terminal instead of writing the file}';

    protected $description = 'Export products and shades (ID, SKU, name) as CSV for the warehouse';

    public function handle(): int
    {
        $rows = [['interne_id', 'kod', 'sku', 'nazov', 'objem_varianta', 'odtien', 'linia', 'cena_eur', 'len_pre_salony', 'publikovany']];

        $products = Product::withoutGlobalScopes()->with('line')->orderByRaw('CAST(code AS UNSIGNED), code')->get();

        foreach ($products as $p) {
            $rows[] = [
                $p->id, $p->code, $p->sku ?: $p->code, $p->name, $p->volume, '',
                $p->line?->name ?? $p->line_label, number_format((float) $p->price, 2, '.', ''),
                $p->b2b_only ? 'ano' : 'nie', $p->published ? 'ano' : 'nie',
            ];

            foreach ((array) ($p->shades ?? []) as $shade) {
                $code = trim((string) ($shade['code'] ?? ''));
                if ($code === '') {
                    continue;
                }
                $sku = $p->shadeSku($shade);
                $price = isset($shade['price']) && $shade['price'] !== '' && $shade['price'] !== null ? (float) $shade['price'] : (float) $p->price;
                $rows[] = [
                    $p->id . '-' . $code, $p->code . '-' . $code, $sku,
                    trim($p->name . ' · ' . $code . (!empty($shade['name']) ? ' ' . $shade['name'] : '')),
                    $p->volume, $code . (!empty($shade['name']) ? ' ' . $shade['name'] : ''),
                    $p->line?->name ?? $p->line_label, number_format($price, 2, '.', ''),
                    $p->b2b_only ? 'ano' : 'nie', $p->published ? 'ano' : 'nie',
                ];
            }
        }

        $out = fopen('php://temp', 'r+');
        fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel opens Slovak characters correctly
        foreach ($rows as $r) {
            fputcsv($out, $r, ';');
        }
        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);

        if ($this->option('stdout')) {
            $this->getOutput()->write($csv);
            return self::SUCCESS;
        }

        $path = storage_path('app/products-export.csv');
        file_put_contents($path, $csv);
        $this->info('Zapísané: ' . $path . ' (' . (count($rows) - 1) . ' riadkov)');
        return self::SUCCESS;
    }
}
