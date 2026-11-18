<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // When the warehouse last reported `stock`. Orders pushed to Foxlog
            // after this moment are not in that number yet → still reserved.
            $table->timestamp('stock_synced_at')->nullable()->after('stock');
        });

        Schema::table('order_items', function (Blueprint $table) {
            // Warehouse SKU of the line (product SKU or per-shade SKU).
            $table->string('sku', 80)->nullable()->after('product_code');
            $table->index('sku');
        });

        // Backfill SKUs on existing lines, same derivation as the Foxlog push.
        $products = Product::withoutGlobalScopes()->get()->keyBy('id');
        DB::table('order_items')->whereNull('sku')->whereNotNull('product_id')->orderBy('id')
            ->each(function ($item) use ($products) {
                $product = $products->get($item->product_id);
                if (!$product) {
                    return;
                }
                $sku = $item->product_code !== $product->code && $product->hasShades()
                    ? $product->shadeSku($product->findShade($item->product_code) ?? ['code' => $item->product_code])
                    : ($product->sku ?: $product->code);
                DB::table('order_items')->where('id', $item->id)->update(['sku' => $sku]);
            });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['sku']);
            $table->dropColumn('sku');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('stock_synced_at');
        });
    }
};
