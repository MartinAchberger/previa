<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminStockScreenTest extends TestCase
{
    use DatabaseTransactions;

    private function product(array $attrs): Product
    {
        $p = Product::withoutGlobalScopes()->first()->replicate();
        $p->forceFill(array_merge(['slug' => 'stock-test-' . uniqid(), 'shades' => null, 'stock' => null], $attrs))->save();
        return $p;
    }

    public function test_stock_screen_lists_items_with_states_and_filters(): void
    {
        $admin = User::first() ?? User::factory()->create();
        $this->product(['name' => 'Stock Test Soldout', 'sku' => 'ST-OUT', 'stock' => 0]);
        $this->product(['name' => 'Stock Test Low', 'sku' => 'ST-LOW', 'stock' => 2]);
        $this->product(['name' => 'Stock Test Shades', 'sku' => 'ST-SH', 'shades' => [
            ['code' => '9.1', 'name' => 'ash', 'sku' => 'ST-SH-9.1', 'stock' => 40],
            ['code' => '9.2', 'name' => 'pearl', 'sku' => 'ST-SH-9.2'],
        ]]);

        $this->actingAs($admin)->get(route('platform.stock'))
            ->assertOk()
            ->assertSee('ST-OUT')->assertSee('ST-LOW')
            ->assertSee('ST-SH-9.1')->assertSee('ST-SH-9.2')
            ->assertSee('Vypredané')->assertSee('Nízky stav');

        $this->actingAs($admin)->get(route('platform.stock', ['stav' => 'out']))
            ->assertOk()
            ->assertSee('ST-OUT')
            ->assertDontSee('ST-LOW')
            ->assertDontSee('ST-SH-9.1');

        $this->actingAs($admin)->get(route('platform.products'))->assertOk();
    }
}
