<?php

namespace Tests\Feature;

use App\Models\B2bUser;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CheckoutInvoiceBlockTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        config(['services.foxlog.enabled' => false]);
        // Never call SuperFaktúra from tests.
        $this->mock(\App\Services\SuperFaktura\SuperFakturaService::class)->shouldIgnoreMissing();
    }

    private function salon(array $extra = []): B2bUser
    {
        return B2bUser::forceCreate(array_merge([
            'email' => 'inv-' . uniqid() . '@example.com', 'password' => bcrypt('x'), 'contact_name' => 'T',
            'salon_name' => 'Salón Faktúra', 'status' => 'active',
        ], $extra));
    }

    private function unpaidInvoiceOrder(B2bUser $b2b): void
    {
        DB::table('orders')->insert([
            'order_number' => 'TS-26-' . random_int(10000, 99999), 'b2b_user_id' => $b2b->id, 'customer_name' => 'T',
            'customer_email' => 'x@example.com', 'customer_phone' => '0', 'shipping_address' => 'x', 'shipping_city' => 'x',
            'shipping_zip' => '0', 'subtotal' => 10, 'total' => 10, 'payment_method' => 'transfer', 'payment_status' => 'unpaid',
            'status' => 'confirmed', 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function order()
    {
        $pid = DB::table('products')->insertGetId([
            'name' => 'Inv product', 'slug' => 'inv-product-' . uniqid(), 'code' => '96', 'line_label' => 'test',
            'price' => 20, 'published' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);
        return $this->from('/pokladna')->post('/objednat', [
            'customer_name' => 'Test', 'customer_email' => 'k@example.com', 'customer_phone' => '0900000000',
            'company_name' => 'Salón s.r.o.', 'ico' => '12345678',
            'payment_method' => 'transfer', 'delivery_choice' => 'gls', 'shipping_same_as_billing' => '1',
            'billing_address' => 'Ulica 1', 'billing_city' => 'Bratislava', 'billing_zip' => '81101', 'billing_country' => 'SK',
            'terms' => '1', 'items' => [['id' => (string) $pid, 'qty' => 1]],
        ]);
    }

    public function test_unpaid_invoice_does_not_block_next_invoice_order_by_default(): void
    {
        $b2b = $this->salon();
        $this->unpaidInvoiceOrder($b2b);
        $this->actingAs($b2b, 'b2b');

        $this->get('/pokladna')->assertOk()->assertSee('Platba na faktúru (prevodom)');
        $this->order()->assertSessionHasNoErrors();
        $this->assertSame('transfer', Order::latest('id')->first()->payment_method);
    }

    public function test_admin_block_hides_and_rejects_invoice_payment(): void
    {
        $b2b = $this->salon(['invoice_blocked' => true]);
        $this->actingAs($b2b, 'b2b');

        $this->get('/pokladna')->assertOk()->assertDontSee('Platba na faktúru (prevodom)');
        $this->order()->assertSessionHasErrors('payment_method');
    }

    public function test_old_unpaid_rule_still_works_when_enabled(): void
    {
        config(['shop.invoice_block_when_unpaid' => true]);
        $b2b = $this->salon();
        $this->unpaidInvoiceOrder($b2b);
        $this->actingAs($b2b, 'b2b');

        $this->order()->assertSessionHasErrors('payment_method');
    }

    public function test_admin_can_toggle_the_block_on_a_salon(): void
    {
        $admin = User::first() ?? User::factory()->create();
        $b2b = $this->salon();
        $this->unpaidInvoiceOrder($b2b);

        $this->actingAs($admin)->get(route('platform.b2b-users.edit', $b2b->id))
            ->assertOk()->assertSee('Zablokovať platbu na faktúru')->assertSee('Neuhradené objednávky na faktúru: 1');

        $this->actingAs($admin)->post(route('platform.b2b-users.edit', [$b2b->id, 'method' => 'save']), ['user' => [
            'salon_name' => $b2b->salon_name, 'contact_name' => 'T', 'email' => $b2b->email, 'status' => 'active',
            'discount_pct' => 0, 'invoice_blocked' => '1',
        ]])->assertRedirect();
        $this->assertTrue($b2b->fresh()->invoice_blocked);
    }
}
