<?php

namespace Tests\Feature;

use App\Http\Controllers\CheckoutController;
use App\Models\DiscountCode;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CheckoutDiscountCodeTest extends TestCase
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
            'name' => 'Discount product ' . $n, 'slug' => 'discount-product-' . $n . '-' . uniqid(), 'code' => '97' . $n,
            'line_label' => 'test', 'price' => 20, 'published' => true, 'created_at' => now(), 'updated_at' => now(),
        ], $extra));
    }

    private function order(int $pid, array $extra = [])
    {
        return $this->from('/pokladna')->post('/objednat', array_merge([
            'customer_name' => 'Test Kupec', 'customer_email' => 'kupec@example.com', 'customer_phone' => '0900000000',
            'payment_method' => 'cod', 'delivery_choice' => 'gls', 'shipping_same_as_billing' => '1',
            'billing_address' => 'Ulica 1', 'billing_city' => 'Bratislava', 'billing_zip' => '81101', 'billing_country' => 'SK',
            'terms' => '1', 'items' => [['id' => (string) $pid, 'qty' => 2]],
        ], $extra));
    }

    public function test_code_lookup_is_case_insensitive_and_skips_inactive(): void
    {
        DiscountCode::create(['name' => 'Jeseň', 'code' => 'jesen10', 'percent' => 10]);
        DiscountCode::create(['name' => 'Staré', 'code' => 'OLD', 'percent' => 20, 'active' => false]);

        $this->postJson('/pokladna/zlavovy-kod', ['code' => ' Jesen10 '])
            ->assertOk()->assertJson(['code' => 'JESEN10', 'name' => 'Jeseň', 'percent' => 10]);
        $this->postJson('/pokladna/zlavovy-kod', ['code' => 'old'])->assertStatus(422);
        $this->postJson('/pokladna/zlavovy-kod', ['code' => 'NOPE'])->assertStatus(422);
    }

    public function test_code_discounts_order_lines_and_is_stored(): void
    {
        DiscountCode::create(['name' => 'Jeseň', 'code' => 'JESEN10', 'percent' => 10]);
        $pid = $this->product(['name' => 'Shampoo DC']);

        $this->order($pid, ['discount_code' => 'jesen10'])->assertSessionHasNoErrors();

        $order = Order::latest('id')->first();
        $this->assertSame('JESEN10', $order->discount_code);
        $this->assertSame(10, $order->discount_pct);
        $this->assertSame(40.0, (float) $order->subtotal);
        $this->assertSame(4.0, (float) $order->discount);
        $this->assertSame(18.0, (float) $order->items->first()->unit_price);
        $this->assertSame(round(36 + CheckoutController::DELIVERY_OPTIONS['gls']['cost'], 2), (float) $order->total);
    }

    public function test_invalid_code_blocks_the_order(): void
    {
        $pid = $this->product();
        $before = Order::count();

        $this->order($pid, ['discount_code' => 'NOPE'])->assertSessionHasErrors('discount_code');
        $this->assertSame($before, Order::count());
    }

    public function test_admin_can_create_and_list_codes(): void
    {
        $admin = User::first() ?? User::factory()->create();

        $this->actingAs($admin)->get(route('platform.discount-codes.create'))->assertOk()->assertSee('Nový zľavový kód');

        $this->actingAs($admin)->post(route('platform.discount-codes.create', ['method' => 'save']), [
            'discount' => ['name' => 'Vianoce', 'code' => 'vianoce15', 'percent' => 15, 'active' => 1],
        ])->assertRedirect(route('platform.discount-codes'));

        $code = DiscountCode::where('code', 'VIANOCE15')->firstOrFail();
        $this->assertSame(15, $code->percent);

        $this->actingAs($admin)->get(route('platform.discount-codes'))->assertOk()->assertSee('VIANOCE15');
        $this->actingAs($admin)->get(route('platform.discount-codes.edit', $code->id))->assertOk()->assertSee('Vianoce');
    }
}
