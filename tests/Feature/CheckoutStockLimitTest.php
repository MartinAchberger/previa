<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CheckoutStockLimitTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        config(['services.foxlog.enabled' => false]);
    }

    private function product(array $extra = []): int
    {
        static $n = 0; $n++;
        return DB::table('products')->insertGetId(array_merge([
            'name' => 'Stock product ' . $n, 'slug' => 'stock-product-' . $n . '-' . uniqid(), 'code' => '97' . $n,
            'sku' => 'STK-' . $n . '-' . uniqid(), 'line_label' => 'test', 'price' => 20, 'published' => true,
            'created_at' => now(), 'updated_at' => now(),
        ], $extra));
    }

    private function order(string $id, int $qty, string $payment = 'cod')
    {
        return $this->from('/pokladna')->post('/objednat', [
            'company_name' => 'Salón s.r.o.', 'ico' => '12345678',
            'customer_name' => 'Test', 'customer_email' => 'k@example.com', 'customer_phone' => '0900000000',
            'payment_method' => $payment, 'delivery_choice' => 'gls', 'shipping_same_as_billing' => '1',
            'billing_address' => 'Ulica 1', 'billing_city' => 'Bratislava', 'billing_zip' => '81101', 'billing_country' => 'SK',
            'terms' => '1', 'items' => [['id' => $id, 'qty' => $qty]],
        ]);
    }

    private function available(int $pid, ?string $shadeCode = null): ?int
    {
        $p = Product::withoutGlobalScopes()->find($pid);
        return StockService::available($p, $shadeCode ? $p->findShade($shadeCode) : null);
    }

    public function test_cannot_order_more_than_on_stock_and_orders_reserve_pieces(): void
    {
        $pid = $this->product(['stock' => 3, 'stock_synced_at' => now()->subMinute()]);
        $before = Order::count();

        $this->order((string) $pid, 4)->assertSessionHasErrors('items');
        $this->assertSame($before, Order::count());

        $this->order((string) $pid, 2)->assertSessionHasNoErrors();
        $this->assertSame(1, $this->available($pid));

        $res = $this->order((string) $pid, 2)->assertSessionHasErrors('items');
        $this->assertStringContainsString('už len 1 ks', session('errors')->first('items'));

        $this->order((string) $pid, 1)->assertSessionHasNoErrors();
        $this->order((string) $pid, 1)->assertSessionHasErrors('items');
        $this->assertSame($before + 2, Order::count());
    }

    public function test_cancelled_order_releases_its_pieces(): void
    {
        $pid = $this->product(['stock' => 2, 'stock_synced_at' => now()->subMinute()]);
        $this->order((string) $pid, 2)->assertSessionHasNoErrors();
        $this->assertSame(0, $this->available($pid));

        Order::latest('id')->first()->forceFill(['status' => 'cancelled'])->save();
        $this->assertSame(2, $this->available($pid));
    }

    public function test_order_counts_until_a_stock_report_arrives_after_it_was_sent_to_foxlog(): void
    {
        $pid = $this->product(['stock' => 5, 'stock_synced_at' => now()->subHour()]);
        $sku = Product::withoutGlobalScopes()->find($pid)->sku;
        $this->order((string) $pid, 2)->assertSessionHasNoErrors();
        $order = Order::latest('id')->first();
        $this->assertSame($sku, $order->items->first()->sku);

        // Pushed to Foxlog, but the last report is older → still reserved.
        $order->forceFill(['foxlog_sent_at' => now()->subMinutes(10)])->save();
        $this->assertSame(3, $this->available($pid));

        // Foxlog reports afterwards (its number already excludes our order).
        config(['services.foxlog.webhook_secret' => 'test-secret']);
        $this->postJson('/api/foxlog/stock', [$sku => 3], ['X-Foxlog-Token' => 'test-secret'])->assertOk();
        $this->assertSame(3, $this->available($pid));
    }

    public function test_abandoned_card_order_stops_reserving(): void
    {
        $pid = $this->product(['stock' => 1, 'stock_synced_at' => now()->subHour()]);
        $order = Order::forceCreate([
            'order_number' => 'ST-26-' . random_int(1000, 9999), 'customer_name' => 'T', 'customer_email' => 'x@example.com',
            'customer_phone' => '0', 'shipping_address' => 'x', 'shipping_city' => 'x', 'shipping_zip' => '0',
            'subtotal' => 20, 'total' => 20, 'payment_method' => 'card', 'payment_status' => 'unpaid', 'status' => 'new',
        ]);
        DB::table('order_items')->insert([
            'order_id' => $order->id, 'product_id' => $pid, 'product_code' => '97', 'sku' => Product::withoutGlobalScopes()->find($pid)->sku,
            'product_name' => 'x', 'unit_price' => 20, 'qty' => 1, 'line_total' => 20, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->assertSame(0, $this->available($pid));

        DB::table('orders')->where('id', $order->id)->update(['created_at' => now()->subHour()]);
        $this->assertSame(1, $this->available($pid));
    }

    public function test_shade_stock_is_enforced_per_shade_and_untracked_is_unlimited(): void
    {
        $pid = $this->product(['shades' => json_encode([
            ['code' => '7.1', 'name' => 'ash', 'stock' => 1, 'stock_at' => now()->subMinute()->toIso8601String()],
            ['code' => '7.2', 'name' => 'pearl'],
        ])]);
        $b2b = \App\Models\B2bUser::forceCreate(['email' => 'stock-salon-' . uniqid() . '@example.com', 'password' => bcrypt('x'), 'contact_name' => 'T', 'salon_name' => 'S', 'status' => 'active']);
        $this->actingAs($b2b, 'b2b');

        $this->order($pid . '-7.1', 2)->assertSessionHasErrors('items');
        $this->order($pid . '-7.1', 1)->assertSessionHasNoErrors();
        $this->order($pid . '-7.1', 1)->assertSessionHasErrors('items');
        $this->order($pid . '-7.2', 50)->assertSessionHasNoErrors();
    }
}
