<?php

namespace App\Http\Controllers;

use App\Mail\AdminNewOrderMail;
use App\Mail\InvoiceMail;
use App\Mail\OrderConfirmationMail;
use App\Models\B2bUser;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\StripeService;
use App\Services\SuperFaktura\SuperFakturaService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Throwable;

class CheckoutController extends Controller
{
    public const SHIPPING_COST = 4.90;
    public const FREE_SHIPPING_FROM = 60;

    public function show(): View
    {
        $b2b = Auth::guard('b2b')->user();
        return view('pages.checkout', [
            'shippingCost'    => self::SHIPPING_COST,
            'freeShippingFrom' => self::FREE_SHIPPING_FROM,
            'b2b' => $b2b,
            // Pay-by-invoice is offered only to approved salons that have no
            // outstanding (unpaid) invoice order.
            'canPayByInvoice' => $b2b && !$this->hasOutstandingInvoiceOrder($b2b),
            // Pickup-point option shows only when the Packeta widget key is set.
            'packetaKey' => config('services.packeta.api_key'),
        ]);
    }

    /**
     * True if the salon has an unpaid pay-by-invoice order still open — while one
     * exists, further invoice orders are blocked (only card/COD allowed).
     */
    private function hasOutstandingInvoiceOrder(B2bUser $b2b): bool
    {
        return Order::where('b2b_user_id', $b2b->id)
            ->where('payment_method', 'transfer')
            ->where('payment_status', 'unpaid')
            ->where('status', '!=', 'cancelled')
            ->exists();
    }

    public function store(Request $request): RedirectResponse
    {
        if (is_string($request->input('items'))) {
            $decoded = json_decode($request->input('items'), true);
            $request->merge(['items' => is_array($decoded) ? $decoded : []]);
        }

        $b2b = Auth::guard('b2b')->user();

        $isCompany = $b2b ? true : $request->boolean('is_company_purchase');

        // Delivery: choose a carrier. Zásielkovňa = pickup point (only if the
        // Packeta widget key is configured); GLS / DPD = courier to address.
        $allowedCarriers = ['gls', 'dpd'];
        if (filled(config('services.packeta.api_key'))) {
            $allowedCarriers[] = 'zasielkovna';
        }
        $choice = $request->input('delivery_choice');
        $isPickup = $choice === 'zasielkovna';
        $shipSame = !$isPickup && $request->boolean('shipping_same_as_billing');

        $rules = [
            'customer_name'    => 'required|string|max:150',
            'customer_email'   => 'required|email|max:150',
            'customer_phone'   => 'required|string|max:40',
            'payment_method'   => 'required|in:cod,card,transfer',
            'delivery_choice'  => 'required|in:' . implode(',', $allowedCarriers),
            'notes'            => 'nullable|string|max:1000',
            'items'            => 'required|array|min:1',
            'items.*.id'       => 'required|string|max:80',
            'items.*.qty'      => 'required|integer|min:1|max:99',
        ];

        // Fakturačná / korešpondenčná adresa — povinná pre každého (B2C aj firmu).
        $rules['billing_address'] = 'required|string|max:200';
        $rules['billing_city']    = 'required|string|max:100';
        $rules['billing_zip']     = 'required|string|max:16';
        $rules['billing_country'] = 'required|string|max:2';

        if ($isCompany) {
            $rules['company_name'] = 'required|string|max:200';
            $rules['ico']          = 'required|string|max:16';
            $rules['vat_id']       = ['nullable', 'string', 'max:24', 'regex:/^[A-Za-z]{2}[0-9A-Za-z]{8,12}$/'];
        }

        if ($isPickup) {
            // Pickup point selected via the widget — its details ride in hidden fields.
            $rules['pickup_point_id']   = 'required|string|max:64';
            $rules['pickup_point_name'] = 'required|string|max:200';
            $rules['shipping_city']     = 'nullable|string|max:100';
            $rules['shipping_zip']      = 'nullable|string|max:16';
            $rules['shipping_country']  = 'nullable|string|max:2';
        } elseif (!$shipSame) {
            $rules['shipping_address'] = 'required|string|max:200';
            $rules['shipping_city']    = 'required|string|max:100';
            $rules['shipping_zip']     = 'required|string|max:16';
            $rules['shipping_country'] = 'required|string|max:2';
        }

        $data = $request->validate($rules);

        // Pay-by-invoice is B2B-only and blocked while an unpaid invoice order is open.
        if ($data['payment_method'] === 'transfer') {
            if (!$b2b) {
                return back()->withErrors(['payment_method' => 'Platba na faktúru je dostupná len pre prihlásené salóny.'])->withInput();
            }
            if ($this->hasOutstandingInvoiceOrder($b2b)) {
                return back()->withErrors(['payment_method' => 'Máte neuhradenú objednávku na faktúru. Ďalšiu objednávku na faktúru je možné vytvoriť až po jej úhrade — teraz použite dobierku alebo platbu kartou.'])->withInput();
            }
        }

        $shippingMethod  = $isPickup ? 'pickup' : 'courier';
        $shippingCarrier = $choice; // zasielkovna | gls | dpd

        if ($isPickup) {
            // Delivery goes to the chosen pickup point — store its label/address.
            $data['shipping_address'] = $data['pickup_point_name'];
            $data['shipping_city']    = $data['shipping_city'] ?? '';
            $data['shipping_zip']     = $data['shipping_zip'] ?? '';
            $data['shipping_country'] = $data['shipping_country'] ?: 'SK';
        } elseif ($shipSame) {
            $data['shipping_address'] = $data['billing_address'];
            $data['shipping_city']    = $data['billing_city'];
            $data['shipping_zip']     = $data['billing_zip'];
            $data['shipping_country'] = $data['billing_country'];
        }

        $parsed = collect($data['items'])->map(function ($it) {
            $rawId = (string) $it['id'];
            $shadeCode = null;
            $pid = $rawId;
            if (str_contains($rawId, '-')) {
                [$pid, $shadeCode] = explode('-', $rawId, 2);
            }
            return [
                'product_id' => (int) $pid,
                'shade_code' => $shadeCode ?: null,
                'qty'        => (int) $it['qty'],
            ];
        });

        $productIds = $parsed->pluck('product_id')->unique()->all();
        $products = Product::where('published', true)->whereIn('id', $productIds)->get()->keyBy('id');

        if ($products->isEmpty()) {
            return back()->withErrors(['items' => 'Košík je prázdny alebo obsahuje neplatné produkty.'])->withInput();
        }

        $discountPct = $b2b ? (int) $b2b->discount_pct : 0;

        $lines = [];
        $subtotal = 0;
        $subtotalBeforeDiscount = 0;
        $dropped = 0;
        foreach ($parsed as $item) {
            $product = $products->get($item['product_id']);
            if (!$product) { $dropped++; continue; }

            $shade = null;
            if ($item['shade_code']) {
                $shade = $product->findShade($item['shade_code']);
                if (!$shade) { $dropped++; continue; }
            }

            // Product sale discount applies to everyone (shade price or base price),
            // then the B2B salon discount stacks on top.
            $rawPrice = isset($shade['price']) ? (float) $shade['price'] : (float) $product->price;
            $basePrice = $product->discountedPrice($rawPrice);
            $qty = $item['qty'];
            $unit = round($basePrice * (1 - $discountPct / 100), 2);
            $lineTotal = round($unit * $qty, 2);
            $subtotal += $lineTotal;
            $subtotalBeforeDiscount += round($basePrice * $qty, 2);

            $lines[] = [
                'product'    => $product,
                'shade'      => $shade,
                'qty'        => $qty,
                'unit_price' => $unit,
                'line_total' => $lineTotal,
            ];
        }

        if (empty($lines)) {
            return back()->withErrors(['items' => 'Košík je prázdny.'])->withInput();
        }

        // Some cart items no longer exist / aren't available (e.g. unpublished, or a
        // professional-only product left in a guest's cart). Don't silently charge a
        // different order than the customer saw — ask them to refresh the cart.
        if ($dropped > 0) {
            return back()->withErrors(['items' => 'Niektoré položky v košíku už nie sú dostupné. Skontrolujte prosím košík a skúste znova.'])->withInput();
        }

        $subtotalBeforeDiscount = round($subtotalBeforeDiscount, 2);
        $discountValue = round($subtotalBeforeDiscount - $subtotal, 2);
        $shipping = $subtotal >= self::FREE_SHIPPING_FROM ? 0 : self::SHIPPING_COST;
        $total = round($subtotal + $shipping, 2);

        $order = DB::transaction(function () use ($data, $lines, $subtotalBeforeDiscount, $discountValue, $shipping, $total, $discountPct, $b2b, $isCompany, $shippingMethod, $shippingCarrier) {
            $order = Order::create([
                'order_number'     => Order::generateOrderNumber('PH'),
                'b2b_user_id'      => $b2b?->id,
                'order_type'       => $b2b ? 'b2b' : 'b2c',
                'customer_name'    => $data['customer_name'],
                'customer_email'   => $data['customer_email'],
                'customer_phone'   => $data['customer_phone'],
                'company_name'     => $isCompany ? $data['company_name'] : null,
                'ico'              => $isCompany ? $data['ico'] : null,
                'vat_id'           => $isCompany ? ($data['vat_id'] ?? null) : null,
                'shipping_address' => $data['shipping_address'],
                'shipping_city'    => $data['shipping_city'],
                'shipping_zip'     => $data['shipping_zip'],
                'shipping_country' => strtoupper($data['shipping_country']),
                'billing_address'  => $data['billing_address'],
                'billing_city'     => $data['billing_city'],
                'billing_zip'      => $data['billing_zip'],
                'billing_country'  => strtoupper($data['billing_country']),
                'subtotal'         => $subtotalBeforeDiscount,
                'discount'         => $discountValue,
                'discount_pct'     => $discountPct,
                'shipping'         => $shipping,
                'total'            => $total,
                'payment_method'   => $data['payment_method'],
                'payment_status'   => 'unpaid',
                'shipping_method'  => $shippingMethod,
                'shipping_carrier' => $shippingCarrier,
                'pickup_point_id'  => $data['pickup_point_id'] ?? null,
                'pickup_point_name' => $data['pickup_point_name'] ?? null,
                'status'           => 'new',
                'notes'            => $data['notes'] ?? null,
            ]);

            foreach ($lines as $line) {
                $product = $line['product'];
                $shade = $line['shade'] ?? null;
                $code = $shade['code'] ?? $product->code;
                $name = $shade
                    ? trim($product->name . ' · ' . $shade['code'] . (!empty($shade['name']) ? ' ' . $shade['name'] : ''))
                    : $product->name;
                OrderItem::create([
                    'order_id'           => $order->id,
                    'product_id'         => $product->id,
                    'product_code'       => $code,
                    'product_name'       => $name,
                    'product_subtitle'   => $product->subtitle,
                    'product_volume'     => $product->volume,
                    'product_line_label' => $product->line_label,
                    'unit_price'         => $line['unit_price'],
                    'qty'                => $line['qty'],
                    'line_total'         => $line['line_total'],
                ]);
            }

            return $order;
        });

        if ($order->payment_method === 'card') {
            try {
                $session = app(StripeService::class)->createCheckoutSession($order);
                return redirect()->away($session->url);
            } catch (Throwable $e) {
                Log::error('Stripe session create failed', [
                    'order' => $order->order_number,
                    'error' => $e->getMessage(),
                ]);
                return redirect()->route('checkout.show')
                    ->withErrors(['payment' => 'Nepodarilo sa pripojiť k platobnej bráne. Skús znova alebo vyber dobierku.']);
            }
        }

        // Card orders are handled above (redirect to Stripe, invoiced after payment).
        // Here we handle COD and pay-by-invoice.
        $this->afterOrderPlaced($order);

        return redirect($this->confirmationUrl($order));
    }

    /**
     * Post-checkout side effects for COD / pay-by-invoice orders. All mailables
     * are queued, so failures don't block the checkout response.
     * - COD: thank-you confirmation; the invoice is issued later, when marked paid.
     * - Transfer (na faktúru): thank-you confirmation + a regular invoice with a
     *   due date (SuperFaktúra renders IBAN + Pay-by-Square QR on the PDF). The
     *   order ships normally; the salon pays by the due date and we record the
     *   payment when it arrives.
     */
    private function afterOrderPlaced(Order $order): void
    {
        try {
            Mail::to(config('mail.admin_address'))->send(new AdminNewOrderMail($order));
        } catch (Throwable $e) {
            Log::error('Admin order notify failed', ['order' => $order->order_number, 'error' => $e->getMessage()]);
        }

        try {
            Mail::to($order->customer_email)->send(new OrderConfirmationMail($order));
        } catch (Throwable $e) {
            Log::error('Order confirmation email failed', ['order' => $order->order_number, 'error' => $e->getMessage()]);
        }

        if ($order->payment_method === 'transfer') {
            $this->issueTransferInvoice($order);
        }

        // COD and pay-by-invoice ship immediately → push to the fulfilment warehouse.
        app(\App\Services\Foxlog\FoxlogService::class)->sendOrder($order);
    }

    /**
     * Issue the regular invoice (with due date) for a pay-by-invoice order and
     * email it. SuperFaktúra renders the bank details + Pay-by-Square QR on the
     * PDF. The order stays unpaid until the transfer is recorded in the admin.
     */
    private function issueTransferInvoice(Order $order): void
    {
        $sf = app(SuperFakturaService::class);
        try {
            $sf->issueForOrder($order);
        } catch (Throwable $e) {
            Log::error('SuperFaktura transfer invoice issue failed', ['order' => $order->order_number, 'error' => $e->getMessage()]);
            $sf->recordError($order, $e);
            return;
        }

        if (!$order->activeDocumentPdfPath()) {
            return;
        }

        try {
            Mail::to($order->customer_email)->send(new InvoiceMail($order));
            $order->forceFill(['invoice_sent_at' => now()])->save();
        } catch (Throwable $e) {
            Log::error('Transfer invoice email failed', ['order' => $order->order_number, 'error' => $e->getMessage()]);
        }
    }

    private function confirmationUrl(Order $order): string
    {
        return URL::temporarySignedRoute(
            'order.confirmation',
            now()->addDays(30),
            ['orderNumber' => $order->order_number],
        );
    }

    public function confirmation(string $orderNumber): View
    {
        $order = Order::with('items')->where('order_number', $orderNumber)->firstOrFail();
        return view('pages.order-confirmation', compact('order'));
    }
}
