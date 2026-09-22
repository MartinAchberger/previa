<?php

namespace Tests\Feature;

use App\Mail\OrderShippedMail;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Inbound Foxlog webhooks (warehouse → us). Runs against the local DB inside a
 * transaction that is rolled back, so no fixtures survive.
 */
class FoxlogWebhookTest extends TestCase
{
    use DatabaseTransactions;

    private const SECRET = 'test-secret';

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.foxlog.webhook_secret' => self::SECRET]);
        Mail::fake();
    }

    private function headers(?string $token = self::SECRET): array
    {
        return $token === null ? [] : ['X-Foxlog-Token' => $token];
    }

    private function makeProduct(string $sku): int
    {
        return DB::table('products')->insertGetId([
            'name' => 'Test ' . $sku, 'slug' => 'test-' . strtolower($sku), 'code' => '9' . random_int(100, 999),
            'line_label' => 'test', 'price' => 10, 'sku' => $sku, 'published' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function makeOrder(string $number): int
    {
        return DB::table('orders')->insertGetId([
            'order_number' => $number, 'customer_name' => 'Test', 'customer_email' => 'test@example.com',
            'customer_phone' => '0900000000', 'shipping_address' => 'x', 'shipping_city' => 'x', 'shipping_zip' => '00000',
            'subtotal' => 10, 'total' => 10, 'status' => 'confirmed', 'payment_method' => 'card',
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    public function test_rejects_missing_or_wrong_token(): void
    {
        $this->postJson('/api/foxlog/stock', ['X' => 1], $this->headers(null))->assertStatus(401);
        $this->postJson('/api/foxlog/stock', ['X' => 1], $this->headers('wrong'))->assertStatus(401);
        $this->postJson('/api/foxlog/order-status', [], $this->headers('wrong'))->assertStatus(401);
    }

    public function test_query_token_is_accepted_as_alternative(): void
    {
        $this->postJson('/api/foxlog/stock?token=' . self::SECRET, [])->assertOk();
    }

    public function test_stock_updates_products_by_sku_and_reports_unknown(): void
    {
        $id = $this->makeProduct('TST-SKU-1');

        $res = $this->postJson('/api/foxlog/stock', ['TST-SKU-1' => 7, 'NOPE-999' => 3], $this->headers());

        $res->assertOk()->assertJson(['updated' => 1, 'unknown_skus' => ['NOPE-999']]);
        $this->assertSame(7, (int) DB::table('products')->where('id', $id)->value('stock'));
    }

    public function test_order_status_maps_status_stores_tracking_and_emails_customer_once(): void
    {
        $id = $this->makeOrder('TS-26-9001');

        $payload = ['TS-26-9001' => ['status' => 'shipped', 'tracking_number' => 'ZZ123', 'tracking_link' => 'https://t.example/ZZ123'], 'TS-26-0000' => ['status' => 'shipped']];
        $res = $this->postJson('/api/foxlog/order-status', $payload, $this->headers());

        $res->assertOk()->assertJson(['updated' => 1, 'unknown_references' => ['TS-26-0000']]);
        $order = Order::find($id);
        $this->assertSame('shipped', $order->status);
        $this->assertSame('shipped', $order->foxlog_status);
        $this->assertSame('ZZ123', $order->tracking_number);
        Mail::assertQueued(OrderShippedMail::class, 1);

        // Second identical call must not e-mail again.
        $this->postJson('/api/foxlog/order-status', $payload, $this->headers())->assertOk();
        Mail::assertQueued(OrderShippedMail::class, 1);
    }

    public function test_unknown_status_keeps_our_status_but_records_raw_value(): void
    {
        $id = $this->makeOrder('TS-26-9002');

        $this->postJson('/api/foxlog/order-status', ['TS-26-9002' => ['status' => 'packing']], $this->headers())->assertOk();

        $order = Order::find($id);
        $this->assertSame('confirmed', $order->status);
        $this->assertSame('packing', $order->foxlog_status);
    }
}
