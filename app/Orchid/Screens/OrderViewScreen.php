<?php

namespace App\Orchid\Screens;

use App\Mail\InvoiceMail;
use App\Mail\OrderCancelledMail;
use App\Models\Order;
use App\Services\StripeService;
use App\Services\SuperFaktura\SuperFakturaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Throwable;

class OrderViewScreen extends Screen
{
    public ?Order $order = null;

    public function permission(): ?iterable { return ['platform.eshop.orders']; }

    public function query(Order $order): array
    {
        $this->order = $order;
        $order->load('items', 'b2bUser');
        return ['order' => $order];
    }

    public function name(): string
    {
        return 'Objednávka ' . ($this->order?->order_number ?? '?');
    }

    public function description(): string
    {
        if (!$this->order) return '';
        return strtoupper($this->order->order_type) . ' · ' . $this->order->customer_email . ' · ' . $this->order->totalFormatted();
    }

    public function commandBar(): array
    {
        $order = $this->order;

        $bar = [
            Button::make('Uložiť zmeny')->method('save')->icon('bs.check'),
        ];

        if ($order?->activeDocumentPdfPath()) {
            $kind = $order->hasInvoice() ? 'invoice' : 'proforma';
            $bar[] = Link::make('Stiahnuť faktúru')
                ->icon('bs.file-earmark-pdf')
                ->href(route('platform.orders.invoice.download', ['order' => $order->id, 'kind' => $kind]));
        }

        if ($order?->hasProforma() && !$order->hasInvoice()) {
            $bar[] = Button::make('Vystaviť ostrú faktúru')
                ->method('issueRegular')
                ->icon('bs.receipt')
                ->confirm('Vystaví ostrú faktúru z proformy v SuperFaktúre.');
        }

        if ($order?->invoice_status === Order::INVOICE_STATUS_ERROR || $order?->invoice_status === Order::INVOICE_STATUS_NONE) {
            if ($order && !$order->hasInvoice() && !$order->hasProforma()) {
                $bar[] = Button::make('Vystaviť faktúru')
                    ->method('issueDocument')
                    ->icon('bs.arrow-clockwise');
            }
        }

        if ($order?->hasProforma() || $order?->hasInvoice()) {
            $bar[] = Button::make('Stornovať faktúru')
                ->method('cancelInvoice')
                ->icon('bs.x-circle')
                ->confirm('Faktúra bude vymazaná v SuperFaktúre. Naozaj pokračovať?');

            if ($order?->activeDocumentPdfPath()) {
                $bar[] = Button::make('Poslať email')
                    ->method('emailInvoice')
                    ->icon('bs.envelope')
                    ->novalidate();
            }
        }

        if ($order && $order->status !== 'cancelled') {
            $bar[] = Button::make('Poslať do skladu (Foxlog)')
                ->method('resendFoxlog')
                ->icon('bs.box-seam')
                ->novalidate();
        }

        return $bar;
    }

    public function layout(): array
    {
        return [
            Layout::columns([
                Layout::rows([
                    Select::make('order.status')
                        ->title('Stav objednávky')
                        ->options([
                            'new' => 'Nová',
                            'confirmed' => 'Potvrdená',
                            'shipped' => 'Odoslaná',
                            'delivered' => 'Doručená',
                            'cancelled' => 'Zrušená',
                        ])
                        ->required(),
                    Select::make('order.payment_status')
                        ->title('Stav platby')
                        ->options([
                            'unpaid' => 'Nezaplatené',
                            'paid' => 'Zaplatené',
                            'refunded' => 'Vrátené',
                        ])
                        ->required(),
                    Input::make('order.tracking_number')->title('Tracking number')->help('Číslo zásielky (synchronizuje sa zo skladu, prípadne doplň ručne).'),
                    Input::make('foxlog_status_display')->title('Stav v sklade (Foxlog)')->disabled()->value($this->order?->foxlog_status ?: '—'),
                    TextArea::make('order.notes')->title('Poznámka')->rows(3),
                ]),
                Layout::view('orchid.order-summary', ['order' => $this->order]),
                Layout::view('orchid.invoice-panel', ['order' => $this->order]),
            ]),
            Layout::table('order.items', [
                TD::make('product_code', 'n°')->width('60px'),
                TD::make('product_name', 'Produkt'),
                TD::make('product_line_label', 'Línia'),
                TD::make('product_volume', 'Objem'),
                TD::make('qty', 'Ks')->width('60px'),
                TD::make('unit_price', 'Jedn. cena')->render(fn ($i) => $i->unitPriceFormatted()),
                TD::make('line_total', 'Spolu')->render(fn ($i) => $i->lineTotalFormatted()),
            ]),
        ];
    }

    public function save(Order $order, Request $request, SuperFakturaService $sf)
    {
        $data = $request->input('order');
        $newStatus        = $data['status'] ?? $order->status;
        $newPaymentStatus = $data['payment_status'] ?? $order->payment_status;
        $oldStatus        = $order->status;
        $oldPaymentStatus = $order->payment_status;

        $fill = [
            'status' => $newStatus,
            'payment_status' => $newPaymentStatus,
            'notes' => $data['notes'] ?? $order->notes,
            'tracking_number' => $data['tracking_number'] ?? $order->tracking_number,
        ];
        if ($newPaymentStatus === 'paid' && $oldPaymentStatus !== 'paid') {
            $fill['paid_at'] = now();
        } elseif ($newPaymentStatus === 'unpaid' && $oldPaymentStatus !== 'unpaid') {
            // Keep paid_at on refunds — the payment did happen; only a reset
            // back to "unpaid" clears it.
            $fill['paid_at'] = null;
            $fill['refunded_at'] = null;
        }

        $order->fill($fill)->save();

        // Auto: refunded → refund the card payment via Stripe (COD/transfer refunds
        // happen outside the system, we only record the state for those).
        if ($newPaymentStatus === 'refunded' && $oldPaymentStatus !== 'refunded') {
            if ($order->payment_method === 'card') {
                try {
                    app(StripeService::class)->refundOrder($order);
                    $order->forceFill(['refunded_at' => now()])->save();
                    Toast::info('Platba vrátená cez Stripe.');
                } catch (Throwable $e) {
                    Log::error('Stripe refund failed', ['order' => $order->order_number, 'error' => $e->getMessage()]);
                    Toast::error('Stripe refund zlyhal: ' . $e->getMessage());
                }
            } else {
                $order->forceFill(['refunded_at' => now()])->save();
                Toast::info('Označené ako vrátené (mimo Stripe - vrátenie rieš manuálne prevodom).');
            }
        }

        // Auto: cancellation → cancel invoice + notify the customer
        if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
            if ($order->hasProforma() || $order->hasInvoice()) {
                try {
                    $sf->cancel($order);
                    Toast::info('Faktúra stornovaná v SuperFaktúre.');
                } catch (Throwable $e) {
                    Log::error('SF cancel failed', ['order' => $order->order_number, 'error' => $e->getMessage()]);
                    Toast::error('Storno faktúry zlyhalo: ' . $e->getMessage());
                }
            }

            try {
                Mail::to($order->customer_email)->send(new OrderCancelledMail($order->refresh()));
                Toast::info('Zákazníkovi bol odoslaný e-mail o zrušení objednávky.');
            } catch (Throwable $e) {
                Log::error('Cancellation email failed', ['order' => $order->order_number, 'error' => $e->getMessage()]);
                Toast::error('E-mail o zrušení sa nepodarilo odoslať.');
            }

            // Cancel in the fulfilment warehouse too (no-op if Foxlog is disabled).
            app(\App\Services\Foxlog\FoxlogService::class)->cancelOrder($order);
        }

        // Auto: payment marked paid → ensure a paid regular invoice exists + email it.
        // This is where COD orders get invoiced (money collected on delivery); card
        // orders are already invoiced from Stripe, so we only record the payment.
        if ($newPaymentStatus === 'paid' && $oldPaymentStatus !== 'paid') {
            try {
                if ($order->hasProforma() && !$order->hasInvoice()) {
                    $sf->convertProformaToInvoice($order);
                    $sf->markInvoicePaid($order->refresh());
                    $this->dispatchInvoiceEmail($order->refresh());
                    Toast::info('Vystavená ostrá faktúra, zaznamenaná platba a odoslaný e-mail zákazníkovi.');
                } elseif ($order->hasInvoice()) {
                    $sf->markInvoicePaid($order->refresh());
                    Toast::info('Platba zaznamenaná v SuperFaktúre.');
                } else {
                    // No document yet (typicky dobierka) → vystaviť faktúru teraz.
                    $sf->issueForOrder($order);
                    $sf->markInvoicePaid($order->refresh());
                    $this->dispatchInvoiceEmail($order->refresh());
                    Toast::info('Vystavená faktúra, zaznamenaná platba a odoslaný e-mail zákazníkovi.');
                }
            } catch (Throwable $e) {
                Log::error('SF on-paid failed', ['order' => $order->order_number, 'error' => $e->getMessage()]);
                $sf->recordError($order, $e);
                Toast::error('Spracovanie platby v SuperFaktúre zlyhalo: ' . $e->getMessage());
            }
        }

        Toast::info('Uložené');
        return redirect()->route('platform.orders.view', $order->id);
    }

    public function issueDocument(Order $order, SuperFakturaService $sf)
    {
        try {
            $sf->issueForOrder($order);
            $this->dispatchInvoiceEmail($order->fresh());
            Toast::info('Faktúra vystavená.');
        } catch (Throwable $e) {
            Log::error('SF issue failed', ['order' => $order->order_number, 'error' => $e->getMessage()]);
            $sf->recordError($order, $e);
            Toast::error('Vystavenie zlyhalo: ' . $e->getMessage());
        }
        return redirect()->route('platform.orders.view', $order->id);
    }

    public function issueRegular(Order $order, SuperFakturaService $sf)
    {
        try {
            $sf->convertProformaToInvoice($order);
            $this->dispatchInvoiceEmail($order->fresh());
            Toast::info('Vystavená ostrá faktúra.');
        } catch (Throwable $e) {
            Log::error('SF convert failed', ['order' => $order->order_number, 'error' => $e->getMessage()]);
            $sf->recordError($order, $e);
            Toast::error('Konverzia zlyhala: ' . $e->getMessage());
        }
        return redirect()->route('platform.orders.view', $order->id);
    }

    public function cancelInvoice(Order $order, SuperFakturaService $sf)
    {
        try {
            $sf->cancel($order);
            Toast::info('Faktúra stornovaná.');
        } catch (Throwable $e) {
            Log::error('SF cancel failed', ['order' => $order->order_number, 'error' => $e->getMessage()]);
            Toast::error('Storno zlyhalo: ' . $e->getMessage());
        }
        return redirect()->route('platform.orders.view', $order->id);
    }

    public function emailInvoice(Order $order)
    {
        $this->dispatchInvoiceEmail($order);
        return redirect()->route('platform.orders.view', $order->id);
    }

    public function resendFoxlog(Order $order, \App\Services\Foxlog\FoxlogService $foxlog)
    {
        if (!$foxlog->enabled()) {
            Toast::warning('Foxlog integrácia nie je zapnutá (FOXLOG_ENABLED / API token).');
            return redirect()->route('platform.orders.view', $order->id);
        }

        $foxlog->sendOrder($order);
        $order->refresh();

        if ($order->foxlog_status === 'error') {
            Toast::error('Odoslanie do skladu zlyhalo — pozri logy.');
        } else {
            Toast::info('Objednávka odoslaná do skladu (Foxlog).');
        }

        return redirect()->route('platform.orders.view', $order->id);
    }

    private function dispatchInvoiceEmail(Order $order): void
    {
        if (!$order->activeDocumentPdfPath()) {
            Toast::error('Pre objednávku neexistuje PDF.');
            return;
        }

        try {
            Mail::to($order->customer_email)->send(new InvoiceMail($order));
            $order->forceFill(['invoice_sent_at' => now()])->save();
            Toast::info('Email odoslaný na ' . $order->customer_email);
        } catch (Throwable $e) {
            Log::error('Invoice email failed', ['order' => $order->order_number, 'error' => $e->getMessage()]);
            Toast::error('Odoslanie emailu zlyhalo: ' . $e->getMessage());
        }
    }
}
