<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CheckoutGiftBagTest extends TestCase
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
            'name' => 'Test product ' . $n, 'slug' => 'test-product-' . $n . '-' . uniqid(), 'code' => '98' . $n,
            'line_label' => 'test', 'price' => 20, 'published' => true, 'created_at' => now(), 'updated_at' => now(),
        ], $extra));
    }

    public function test_checkout_shows_gift_bag_dropdown_only_when_a_bag_is_flagged(): void
    {
        $this->get('/pokladna')->assertOk()->assertDontSee('Darčekové balenie (voliteľné)');

        $this->product(['name' => 'Darčeková taška TEST', 'code' => 'GBTEST', 'price' => 3, 'is_gift_bag' => true, 'b2b_only' => true]);

        $this->get('/pokladna')->assertOk()->assertSee('Darčekové balenie (voliteľné)')->assertSee('Darčeková taška TEST');
    }

    public function test_selected_gift_bag_is_added_as_an_order_line_and_priced(): void
    {
        $pid = $this->product(['name' => 'Shampoo TEST']);
        $this->product(['name' => 'Darčeková taška TEST', 'code' => 'GBTEST', 'price' => 3, 'is_gift_bag' => true, 'b2b_only' => true]);

        $res = $this->post('/objednat', [
            'customer_name' => 'Test Kupec', 'customer_email' => 'kupec@example.com', 'customer_phone' => '0900000000',
            'payment_method' => 'cod', 'delivery_choice' => 'gls', 'shipping_same_as_billing' => '1',
            'billing_address' => 'Ulica 1', 'billing_city' => 'Bratislava', 'billing_zip' => '81101', 'billing_country' => 'SK',
            'terms' => '1', 'gift_bag' => 'GBTEST',
            'items' => [['id' => (string) $pid, 'qty' => 2]],
        ]);

        $res->assertSessionHasNoErrors();
        $order = Order::latest('id')->first();
        $this->assertSame(['Shampoo TEST', 'Darčeková taška TEST'], $order->items->pluck('product_name')->all());
        $this->assertSame(43.0, (float) $order->subtotal);   // 2 × 20 + 3
        $this->assertSame(47.5, (float) $order->total);      // + GLS 4,50
    }

    public function test_unknown_gift_bag_code_is_rejected(): void
    {
        $pid = $this->product();
        $res = $this->from('/pokladna')->post('/objednat', [
            'customer_name' => 'Test', 'customer_email' => 'k@example.com', 'customer_phone' => '0900000000',
            'payment_method' => 'cod', 'delivery_choice' => 'gls', 'shipping_same_as_billing' => '1',
            'billing_address' => 'Ulica 1', 'billing_city' => 'Bratislava', 'billing_zip' => '81101', 'billing_country' => 'SK',
            'terms' => '1', 'gift_bag' => 'NOPE', 'items' => [['id' => (string) $pid, 'qty' => 1]],
        ]);
        $res->assertSessionHasErrors('gift_bag');
    }
}
