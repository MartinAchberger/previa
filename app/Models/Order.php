<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Screen\AsSource;

class Order extends Model
{
    use AsSource, Filterable;

    protected $fillable = [
        'order_number', 'b2b_user_id', 'order_type',
        'customer_name', 'customer_email', 'customer_phone',
        'company_name', 'ico', 'vat_id',
        'shipping_address', 'shipping_city', 'shipping_zip', 'shipping_country',
        'billing_address', 'billing_city', 'billing_zip', 'billing_country',
        'subtotal', 'discount', 'shipping', 'total', 'discount_pct',
        'payment_method', 'payment_status', 'paid_at', 'shipping_method',
        'stripe_payment_intent', 'refunded_at',
        'tracking_number', 'tracking_link', 'shipping_carrier', 'pickup_point_id', 'pickup_point_name', 'foxlog_status', 'foxlog_sent_at',
        'status', 'notes',
        'proforma_sf_id', 'proforma_number', 'proforma_pdf_path',
        'invoice_sf_id', 'invoice_number', 'invoice_pdf_path',
        'invoice_status', 'invoice_error', 'invoiced_at', 'invoice_sent_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'shipping' => 'decimal:2',
        'total'    => 'decimal:2',
        'discount_pct' => 'integer',
        'proforma_sf_id' => 'integer',
        'invoice_sf_id' => 'integer',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
        'foxlog_sent_at' => 'datetime',
        'invoiced_at' => 'datetime',
        'invoice_sent_at' => 'datetime',
    ];

    public const INVOICE_STATUS_NONE      = 'none';
    public const INVOICE_STATUS_PROFORMA  = 'proforma';
    public const INVOICE_STATUS_ISSUED    = 'issued';
    public const INVOICE_STATUS_CANCELLED = 'cancelled';
    public const INVOICE_STATUS_ERROR     = 'error';

    protected $allowedFilters = [
        'order_number' => Like::class,
        'customer_email' => Like::class,
        'company_name' => Like::class,
        'status' => Where::class,
        'order_type' => Where::class,
        'payment_method' => Where::class,
    ];

    protected $allowedSorts = ['id', 'order_number', 'total', 'status', 'created_at'];

    public const STATUSES = [
        'new'       => 'Nová',
        'confirmed' => 'Potvrdená',
        'shipped'   => 'Odoslaná',
        'delivered' => 'Doručená',
        'cancelled' => 'Zrušená',
    ];

    public const PAYMENT_LABELS = [
        'cod'      => 'Dobierka',
        'transfer' => 'Platba na faktúru (prevod)',
        'card'     => 'Online platba',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function b2bUser(): BelongsTo
    {
        return $this->belongsTo(B2bUser::class);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function paymentLabel(): string
    {
        return self::PAYMENT_LABELS[$this->payment_method] ?? $this->payment_method;
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function totalFormatted(): string
    {
        return '€' . number_format($this->total, 2, ',', ' ');
    }

    public function hasProforma(): bool
    {
        return !empty($this->proforma_sf_id);
    }

    public function hasInvoice(): bool
    {
        return !empty($this->invoice_sf_id);
    }

    public function activeDocumentPdfPath(): ?string
    {
        return $this->invoice_pdf_path ?: $this->proforma_pdf_path;
    }

    public function activeDocumentNumber(): ?string
    {
        return $this->invoice_number ?: $this->proforma_number;
    }

    public function variableSymbol(): string
    {
        return preg_replace('/\D+/', '', (string) $this->order_number) ?: (string) $this->id;
    }

    public function billingAddress(): string
    {
        return $this->billing_address ?: $this->shipping_address;
    }

    public function billingCity(): string
    {
        return $this->billing_city ?: $this->shipping_city;
    }

    public function billingZip(): string
    {
        return $this->billing_zip ?: $this->shipping_zip;
    }

    public function billingCountry(): string
    {
        return $this->billing_country ?: $this->shipping_country;
    }

    public function hasSeparateBillingAddress(): bool
    {
        if (!$this->billing_address) {
            return false;
        }
        return $this->billing_address !== $this->shipping_address
            || $this->billing_city !== $this->shipping_city
            || $this->billing_zip !== $this->shipping_zip
            || $this->billing_country !== $this->shipping_country;
    }

    public static function generateOrderNumber(string $prefix = 'PH'): string
    {
        $year = now()->format('y');
        // Lock the highest existing row for this year so two concurrent checkouts
        // can't read the same max and generate a duplicate number (which would hit
        // the unique index and 500). Must run inside the checkout transaction.
        $last = self::where('order_number', 'like', "{$prefix}-{$year}-%")
            ->orderByDesc('id')
            ->lockForUpdate()
            ->first();

        $next = 1;
        if ($last && preg_match('/^[A-Z]+-\d+-(\d+)/', $last->order_number, $m)) {
            $next = ((int) $m[1]) + 1;
        }

        return sprintf('%s-%s-%04d', $prefix, $year, $next);
    }
}
