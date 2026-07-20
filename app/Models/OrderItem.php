<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Orchid\Screen\AsSource;

class OrderItem extends Model
{
    use AsSource;

    protected $fillable = [
        'order_id', 'product_id',
        'product_code', 'product_name', 'product_subtitle',
        'product_volume', 'product_line_label',
        'unit_price', 'qty', 'line_total',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
        'qty'        => 'integer',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function unitPriceFormatted(): string
    {
        return '€' . number_format($this->unit_price, 2, ',', ' ');
    }

    public function lineTotalFormatted(): string
    {
        return '€' . number_format($this->line_total, 2, ',', ' ');
    }
}
