<?php

namespace Tests\Feature;

use App\Models\B2bUser;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminSalonOrdersTest extends TestCase
{
    use DatabaseTransactions;

    public function test_salon_edit_screen_lists_the_salons_orders(): void
    {
        $admin = User::first() ?? User::factory()->create();
        $salon = B2bUser::forceCreate(['email' => 'salon-orders@example.com', 'password' => bcrypt('x'), 'contact_name' => 'T', 'salon_name' => 'Salón Orders', 'status' => 'active']);
        DB::table('orders')->insert([
            'order_number' => 'TS-26-7001', 'b2b_user_id' => $salon->id, 'customer_name' => 'T', 'customer_email' => 'x@example.com',
            'customer_phone' => '0', 'shipping_address' => 'x', 'shipping_city' => 'x', 'shipping_zip' => '0', 'subtotal' => 10, 'total' => 10,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('platform.b2b-users.edit', $salon->id))
            ->assertOk()
            ->assertSee('Objednávky salónu')
            ->assertSee('TS-26-7001');
    }
}
