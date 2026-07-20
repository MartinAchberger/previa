<?php

namespace App\Services\SuperFaktura;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use SuperFaktura\ApiClient\ApiClient;
use SuperFaktura\ApiClient\Contract\Invoice\CannotCreateInvoiceException;
use SuperFaktura\ApiClient\Contract\Invoice\InvoiceType;
use SuperFaktura\ApiClient\Contract\Language;
use SuperFaktura\ApiClient\Contract\PaymentType;
use SuperFaktura\ApiClient\UseCase\Invoice\Payment\Payment;
use SuperFaktura\ApiClient\UseCase\Money\Currency;
use Throwable;

class SuperFakturaService
{
    public const PDF_DISK = 'local';
    public const PDF_DIR  = 'invoices';

    /** Splatnosť pre platbu na faktúru (prevodom) — počet dní od vystavenia. */
    public const NET_TERMS_DAYS = 14;

    public function __construct(private ApiClient $api) {}

    public function issueForOrder(Order $order): void
    {
        // Vždy REGULÁRNA faktúra:
        //  - karta / dobierka → úhrada hneď / pri prevzatí,
        //  - platba na faktúru (prevod) → faktúra so splatnosťou; tovar sa odošle
        //    a salón uhradí do dátumu splatnosti (viď buildInvoice()).
        $this->createDocument($order, InvoiceType::REGULAR);
    }

    public function convertProformaToInvoice(Order $order): void
    {
        if (!$order->hasProforma()) {
            throw new \RuntimeException('Objednávka nemá proforma faktúru.');
        }

        $response = $this->api->invoices->createRegularFromProforma((int) $order->proforma_sf_id);
        $invoice  = $this->extractInvoice($response->data);
        $sfId     = (int) ($invoice['id'] ?? 0);
        $number   = (string) ($invoice['invoice_no_formatted'] ?? '');

        if ($sfId <= 0) {
            throw new \RuntimeException('SuperFaktúra nevrátila ID novej faktúry. Raw: ' . json_encode($response->data));
        }

        $order->forceFill([
            'invoice_sf_id'    => $sfId,
            'invoice_number'   => $number,
            'invoice_status'   => Order::INVOICE_STATUS_ISSUED,
            'invoice_error'    => null,
            'invoiced_at'      => now(),
        ])->save();

        $order->forceFill([
            'invoice_pdf_path' => $this->downloadPdf($sfId, $order->order_number, 'invoice'),
        ])->save();
    }

    public function cancel(Order $order): void
    {
        if ($order->hasInvoice()) {
            $this->api->invoices->delete((int) $order->invoice_sf_id);
        }
        if ($order->hasProforma()) {
            $this->api->invoices->delete((int) $order->proforma_sf_id);
        }

        foreach ([$order->invoice_pdf_path, $order->proforma_pdf_path] as $path) {
            if ($path) {
                Storage::disk(self::PDF_DISK)->delete($path);
            }
        }

        $order->forceFill([
            'proforma_sf_id'    => null,
            'proforma_number'   => null,
            'proforma_pdf_path' => null,
            'invoice_sf_id'     => null,
            'invoice_number'    => null,
            'invoice_pdf_path'  => null,
            'invoice_status'    => Order::INVOICE_STATUS_CANCELLED,
            'invoice_error'     => null,
        ])->save();
    }

    public function markInvoicePaid(Order $order, ?string $documentNumber = null): void
    {
        if (!$order->hasInvoice()) {
            throw new \RuntimeException('Objednávka nemá vystavenú regulárnu faktúru.');
        }

        $payment = new Payment(
            amount: (float) $order->total,
            currency: Currency::EURO,
            payment_type: $this->mapPaymentTypeForRecord($order->payment_method),
            document_number: $documentNumber,
            payment_date: $order->paid_at ? \DateTimeImmutable::createFromInterface($order->paid_at) : new \DateTimeImmutable(),
        );

        $this->api->invoices->payments->create((int) $order->invoice_sf_id, $payment);
    }

    private function mapPaymentTypeForRecord(?string $method): PaymentType
    {
        return match ($method) {
            'card'     => PaymentType::CARD,
            'cod'      => PaymentType::COD,
            'transfer' => PaymentType::TRANSFER,
            default    => PaymentType::OTHER,
        };
    }

    public function recordError(Order $order, Throwable $e): void
    {
        $order->forceFill([
            'invoice_status' => Order::INVOICE_STATUS_ERROR,
            'invoice_error'  => substr($e->getMessage(), 0, 1000),
        ])->save();
    }

    private function createDocument(Order $order, InvoiceType $type): void
    {
        try {
            $response = $this->api->invoices->create(
                invoice:  $this->buildInvoice($order, $type),
                items:    $this->buildItems($order),
                client:   $this->buildClient($order),
                settings: $this->buildSettings(),
            );
        } catch (CannotCreateInvoiceException $e) {
            $detail = implode(' | ', array_filter($e->getErrors())) ?: $e->getMessage();
            throw new \RuntimeException('SuperFaktúra: ' . $detail, 0, $e);
        }

        $invoice = $this->extractInvoice($response->data);
        $sfId    = (int) ($invoice['id'] ?? 0);
        $number  = (string) ($invoice['invoice_no_formatted'] ?? '');

        if ($sfId <= 0) {
            throw new \RuntimeException('SuperFaktúra nevrátila ID dokladu. Raw: ' . json_encode($response->data));
        }

        if ($type === InvoiceType::PROFORMA) {
            // Persist the SF id BEFORE downloading the PDF. If the download throws,
            // the id is already saved so a retry won't create a duplicate document.
            $order->forceFill([
                'proforma_sf_id'    => $sfId,
                'proforma_number'   => $number,
                'invoice_status'    => Order::INVOICE_STATUS_PROFORMA,
                'invoice_error'     => null,
            ])->save();

            $order->forceFill([
                'proforma_pdf_path' => $this->downloadPdf($sfId, $order->order_number, 'proforma'),
            ])->save();
            return;
        }

        $order->forceFill([
            'invoice_sf_id'    => $sfId,
            'invoice_number'   => $number,
            'invoice_status'   => Order::INVOICE_STATUS_ISSUED,
            'invoice_error'    => null,
            'invoiced_at'      => now(),
        ])->save();

        $order->forceFill([
            'invoice_pdf_path' => $this->downloadPdf($sfId, $order->order_number, 'invoice'),
        ])->save();
    }

    private function buildInvoice(Order $order, InvoiceType $type): array
    {
        return array_filter([
            'name'              => 'Objednávka ' . $order->order_number,
            'type'              => $type->value,
            'variable'          => $order->variableSymbol(),
            'constant'          => '0308',
            'specific'          => null,
            'payment_type'      => $this->mapPaymentType($order->payment_method),
            'delivery_type'     => 'courier',
            // Splatnosť: pri platbe na faktúru (prevod) dáme lehotu; inak splatné hneď.
            'due'               => $order->payment_method === 'transfer'
                ? now()->addDays(self::NET_TERMS_DAYS)->format('Y-m-d')
                : now()->format('Y-m-d'),
            'comment'           => $order->notes,
        ], fn ($v) => $v !== null && $v !== '');
    }

    private function buildItems(Order $order): array
    {
        // Stored prices (unit_price, shipping) are gross — VAT-inclusive at the
        // shop's 23 % rate, exactly what the customer was charged. SuperFaktúra
        // expects the NET unit price plus the tax rate, so we strip the VAT here
        // and let SF recompute it. Net is kept at 4 decimals so the recomputed
        // gross rounds back to the charged amount to the cent.
        $rate = Product::VAT_RATE;
        $items = [];
        foreach ($order->items as $item) {
            $items[] = [
                'name'        => trim($item->product_name . ' ' . ($item->product_volume ?? '')),
                'description' => $item->product_line_label ?: ($item->product_subtitle ?: ''),
                'quantity'    => $item->qty,
                'unit'        => 'ks',
                'unit_price'  => round(((float) $item->unit_price) / (1 + $rate), 4),
                'tax'         => $rate * 100,
            ];
        }

        if ((float) $order->shipping > 0) {
            $items[] = [
                'name'       => 'Doprava - ' . ($order->shipping_method ?: 'kuriér'),
                'quantity'   => 1,
                'unit'       => 'ks',
                'unit_price' => round(((float) $order->shipping) / (1 + $rate), 4),
                'tax'        => $rate * 100,
            ];
        }

        return $items;
    }

    private function buildClient(Order $order): array
    {
        $b2b = $order->b2bUser;
        // The collected field is the IČ DPH (country-prefixed, e.g. SK2023...),
        // which belongs in `ic_dph`, not `dic` (the digits-only DIČ). For SK
        // payers the DIČ is the IČ DPH without the country prefix.
        $icDph = $order->vat_id ?: $b2b?->vat_id;
        $dic = $icDph ? preg_replace('/^[A-Za-z]{2}/', '', $icDph) : null;

        return array_filter([
            'name'           => $order->company_name ?: $order->customer_name,
            'email'          => $order->customer_email,
            'phone'          => $order->customer_phone,
            'address'        => $order->billingAddress(),
            'city'           => $order->billingCity(),
            'zip'            => $order->billingZip(),
            'country_iso_id' => $order->billingCountry() ?: 'SK',
            'ico'            => $order->ico ?: $b2b?->ico,
            'dic'            => $dic,
            'ic_dph'         => $icDph,
            'delivery_name'    => $order->customer_name,
            'delivery_address' => $order->shipping_address,
            'delivery_city'    => $order->shipping_city,
            'delivery_zip'     => $order->shipping_zip,
            'delivery_country_iso_id' => $order->shipping_country ?: 'SK',
        ], fn ($v) => $v !== null && $v !== '');
    }

    private function buildSettings(): array
    {
        return [
            'language'  => 'slo',
            'signature' => false,
        ];
    }

    private function mapPaymentType(?string $method): string
    {
        return match ($method) {
            'card'     => 'card',
            'transfer' => 'transfer',
            default    => 'cod',
        };
    }

    private function extractInvoice(array $data): array
    {
        if (isset($data['data']['Invoice']) && is_array($data['data']['Invoice'])) {
            return $data['data']['Invoice'];
        }
        if (isset($data['Invoice']) && is_array($data['Invoice'])) {
            return $data['Invoice'];
        }
        return [];
    }

    private function downloadPdf(int $sfId, string $orderNumber, string $kind): string
    {
        $response = $this->api->invoices->downloadPdf($sfId, Language::SLOVAK);
        $path = sprintf('%s/%s-%s.pdf', self::PDF_DIR, $orderNumber, $kind);
        $contents = is_resource($response->data) ? stream_get_contents($response->data) : (string) $response->data;
        Storage::disk(self::PDF_DISK)->put($path, $contents);
        return $path;
    }
}
