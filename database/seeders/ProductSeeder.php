<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductLine;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    private const LOREM = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.';

    public function run(): void
    {
        // Wipe products before reseeding (keeps orders intact via nullOnDelete FK).
        Product::query()->delete();

        $bySlug = ProductLine::all()->keyBy('slug');

        // Placeholder tóny SVG fliaš pre jednotlivé línie.
        $tones = [
            'reconstruct'                => '#16140f',
            'keeping-after-color'        => '#5a3540',
            'energising'                 => '#3f4c3a',
            'regrowth'                   => '#2c4a3a',
            'purifying'                  => '#4a5a6a',
            'dry-dandruff'               => '#7a5a32',
            'oily-dandruff'              => '#6b6b3a',
            'calming'                    => '#8a7a5a',
            'rebalancing'                => '#5a6a5a',
            'hair-and-scalp'             => '#3a3530',
            'taming'                     => '#4a3a50',
            'curl-friends'               => '#7a4a3a',
            'bodifying'                  => '#3a4a6a',
            'blonde'                     => '#d8c5b0',
            'styling-and-basics'         => '#2a2a26',
            'man'                        => '#1a1a18',
            'earth-professional-color'   => '#6b4a2a',
            'virtuos-professional-color' => '#4a2a3a',
            'waving-system'              => '#3a5a4a',
            'doplnky'                    => '#a89e88',
        ];

        // PREVIA CENNÍK 2026: [name, volume, price € s DPH, b2b_only, kind, variant_group]
        $catalog = [
            'reconstruct' => [
                ['Reconstruct Regenerating Shampoo', '100 ml', 10, false, 'tall', 'reconstruct-shampoo'],
                ['Reconstruct Regenerating Shampoo', '340 ml', 31, false, 'tall', 'reconstruct-shampoo'],
                ['Reconstruct Regenerating Shampoo', '1000 ml', 61, true, 'tall', 'reconstruct-shampoo'],
                ['Reconstruct Regenerating Conditioner', '100 ml', 14, false, 'tall', 'reconstruct-conditioner'],
                ['Reconstruct Regenerating Conditioner', '200 ml', 25, false, 'tall', 'reconstruct-conditioner'],
                ['Reconstruct Regenerating Conditioner', '1000 ml', 82, true, 'tall', 'reconstruct-conditioner'],
                ['Reconstruct Regenerating Treatment', '150 ml', 22, false, 'tall', 'reconstruct-treatment'],
                ['Reconstruct Regenerating Treatment', '1000 ml', 103, true, 'tall', 'reconstruct-treatment'],
                ['Reconstruct Biphasic Leave-in Filler Conditioner', '100 ml', 14, false, 'tall', 'reconstruct-biphasic'],
                ['Reconstruct Biphasic Leave-in Filler Conditioner', '200 ml', 24, false, 'tall', 'reconstruct-biphasic'],
                ['Reconstruct Serum', '50 ml', 29, false, 'tall', null],
                ['Reconstruct Brand Kit Premium', '340 ml + 150 ml + 200 ml', 91, false, 'sachet', null],
            ],
            'keeping-after-color' => [
                ['Keeping After Color Shampoo', '340 ml', 31, false, 'tall', 'kac-shampoo'],
                ['Keeping After Color Shampoo', '1000 ml', 61, true, 'tall', 'kac-shampoo'],
                ['Keeping After Color Conditioner', '200 ml', 25, false, 'tall', 'kac-conditioner'],
                ['Keeping After Color Conditioner', '1000 ml', 82, true, 'tall', 'kac-conditioner'],
                ['Keeping After Color Treatment', '150 ml', 22, false, 'tall', 'kac-treatment'],
                ['Keeping After Color Treatment', '1000 ml', 103, true, 'tall', 'kac-treatment'],
                ['Keeping After Color Brand Kit Premium', '340 ml + 150 ml + 200 ml', 91, false, 'sachet', null],
            ],
            'energising' => [
                ['Energising Shampoo', '340 ml', 31, false, 'tall', 'energising-shampoo'],
                ['Energising Shampoo', '1000 ml', 61, true, 'tall', 'energising-shampoo'],
                ['Energising Leave-in Lotion', '100 ml', 17, false, 'tall', null],
            ],
            'regrowth' => [
                ['Regrowth Shampoo', '100 ml', 10, false, 'tall', 'regrowth-shampoo'],
                ['Regrowth Shampoo', '350 ml', 31, false, 'tall', 'regrowth-shampoo'],
                ['Regrowth Shampoo', '950 ml', 72, true, 'tall', 'regrowth-shampoo'],
                ['Regrowth Treatment', '100 ml', 101, false, 'tall', 'regrowth-treatment'],
                ['Regrowth Treatment', '10 × 3 ml', 58, false, 'sachet', 'regrowth-treatment'],
                ['Regrowth Duo Kit', '350 ml + 100 ml', 132, false, 'sachet', null],
                ['Regrowth Brand Kit Premium', '350 ml + 100 ml', 132, false, 'sachet', null],
            ],
            'purifying' => [
                ['Purifying Shampoo', '340 ml', 31, false, 'tall', 'purifying-shampoo'],
                ['Purifying Shampoo', '1000 ml', 61, true, 'tall', 'purifying-shampoo'],
                ['Purifying Treatment', '150 ml', 22, false, 'tall', 'purifying-treatment'],
                ['Purifying Treatment', '1000 ml', 103, true, 'tall', 'purifying-treatment'],
                ['Purifying Leave-in Lotion', '100 ml', 17, false, 'tall', null],
            ],
            'dry-dandruff' => [
                ['Dry Dandruff Cleansing Shampoo', '100 ml', 10, false, 'tall', 'dry-dandruff-shampoo'],
                ['Dry Dandruff Cleansing Shampoo', '350 ml', 31, false, 'tall', 'dry-dandruff-shampoo'],
                ['Dry Dandruff Cleansing Shampoo', '950 ml', 72, true, 'tall', 'dry-dandruff-shampoo'],
                ['Dry Dandruff Kit', '350 ml + 150 ml', 62, false, 'sachet', null],
            ],
            'oily-dandruff' => [
                ['Oily Dandruff Cleansing Shampoo', '100 ml', 10, false, 'tall', 'oily-dandruff-shampoo'],
                ['Oily Dandruff Cleansing Shampoo', '350 ml', 31, false, 'tall', 'oily-dandruff-shampoo'],
                ['Oily Dandruff Cleansing Shampoo', '950 ml', 72, true, 'tall', 'oily-dandruff-shampoo'],
                ['Oily Dandruff Kit', '350 ml + 100 ml', 74, false, 'sachet', null],
            ],
            'calming' => [
                ['Calming Serum', '50 ml', 60, false, 'tall', null],
                ['Calming Shampoo', '100 ml', 10, false, 'tall', 'calming-shampoo'],
                ['Calming Shampoo', '350 ml', 31, false, 'tall', 'calming-shampoo'],
                ['Calming Shampoo', '950 ml', 72, true, 'tall', 'calming-shampoo'],
                ['Calming Duo Kit', '350 ml + 50 ml', 91, false, 'sachet', null],
                ['Calming Brand Kit Premium', '350 ml + 50 ml', 91, false, 'sachet', null],
            ],
            'rebalancing' => [
                ['Rebalancing Treatment', '100 ml', 43, false, 'tall', null],
                ['Rebalancing Shampoo', '100 ml', 10, false, 'tall', 'rebalancing-shampoo'],
                ['Rebalancing Shampoo', '350 ml', 31, false, 'tall', 'rebalancing-shampoo'],
                ['Rebalancing Shampoo', '950 ml', 72, true, 'tall', 'rebalancing-shampoo'],
                ['Rebalancing Duo Kit', '350 ml + 100 ml', 74, false, 'sachet', null],
            ],
            'hair-and-scalp' => [
                ['Dry Scalp Massage Oil', '100 ml', 62, false, 'tall', null],
                ['Hair and Scalp Tonic Conditioner', '150 ml', 31, false, 'tall', 'has-tonic-conditioner'],
                ['Hair and Scalp Tonic Conditioner', '950 ml', 82, true, 'tall', 'has-tonic-conditioner'],
                ['Scalp Peeling', '150 ml', 31, false, 'jar', null],
                ['Scalp Cleanser', '100 ml', 17, false, 'tall', null],
                ['Dry Shampoo', '200 ml', 22, false, 'tall', null],
            ],
            'taming' => [
                ['Smoothing Taming Shampoo', '340 ml', 31, false, 'tall', 'taming-shampoo'],
                ['Smoothing Taming Shampoo', '1000 ml', 62, true, 'tall', 'taming-shampoo'],
                ['Smoothing Taming Conditioner', '200 ml', 25, false, 'tall', 'taming-conditioner'],
                ['Smoothing Taming Conditioner', '1000 ml', 82, true, 'tall', 'taming-conditioner'],
            ],
            'curl-friends' => [
                ['Luscious Curls Shampoo', '340 ml', 31, false, 'tall', 'curls-shampoo'],
                ['Luscious Curls Shampoo', '1000 ml', 62, true, 'tall', 'curls-shampoo'],
                ['Luscious Curls Conditioner', '200 ml', 25, false, 'tall', 'curls-conditioner'],
                ['Luscious Curls Conditioner', '1000 ml', 82, true, 'tall', 'curls-conditioner'],
                ['Luscious Curls Spray', '200 ml', 33, false, 'tall', null],
                ['Luscious Curls Foam', '150 ml', 29, false, 'tall', null],
            ],
            'bodifying' => [
                ['Volumising Bodifying Shampoo', '340 ml', 31, false, 'tall', 'bodifying-shampoo'],
                ['Volumising Bodifying Shampoo', '1000 ml', 61, true, 'tall', 'bodifying-shampoo'],
                ['Volumising Bodifying Conditioner', '200 ml', 25, false, 'tall', 'bodifying-conditioner'],
                ['Volumising Bodifying Conditioner', '1000 ml', 82, true, 'tall', 'bodifying-conditioner'],
            ],
            'blonde' => [
                ['Blonde Silver Shampoo', '340 ml', 31, false, 'tall', 'blonde-shampoo'],
                ['Blonde Silver Shampoo', '1000 ml', 61, true, 'tall', 'blonde-shampoo'],
                ['Blonde Silver Conditioner', '200 ml', 25, false, 'tall', 'blonde-conditioner'],
                ['Blonde Silver Conditioner', '1000 ml', 82, true, 'tall', 'blonde-conditioner'],
                ['Blonde Biphasic Leave-in Conditioner', '200 ml', 28, false, 'tall', null],
            ],
            'styling-and-basics' => [
                ['Styling Creme', '200 ml', 31, false, 'tall', null],
                ['Curl Definer', '100 ml', 14, false, 'tall', 'curl-definer'],
                ['Curl Definer', '200 ml', 22, false, 'tall', 'curl-definer'],
                ['Taming Gloss', '100 ml', 22, false, 'tall', 'taming-gloss'],
                ['Taming Gloss', '200 ml', 39, false, 'tall', 'taming-gloss'],
                ['Plumping Serum', '200 ml', 23, false, 'tall', null],
                ['Sea Salt Spray', '200 ml', 27, false, 'tall', null],
                ['Shine Glaze', '200 ml', 20, false, 'tall', null],
                ['Acid Water', '200 ml', 28, false, 'tall', null],
                ['Style and Finish Defining Paste', '100 ml', 30, false, 'jar', null],
                ['Extra Firm Hairspray', '400 ml', 23, false, 'tall', null],
                ['No Gas Hairspray', '350 ml', 33, false, 'tall', null],
                ['Style and Finish Extra Firm Mousse', '300 ml', 33, false, 'tall', null],
                ['Basic Shampoo', '1000 ml', 61, true, 'tall', null],
                ['Scalp Protective Oil', '200 ml', 25, false, 'tall', null],
                ['Instant Detangler', '200 ml', 25, false, 'tall', null],
            ],
            'man' => [
                ['Man Wash', '250 ml', 20, false, 'tall', 'man-wash'],
                ['Man Wash', '1000 ml', 57, true, 'tall', 'man-wash'],
                ['Man Tonic', '150 ml', 19, false, 'tall', null],
                ['Man Wax Gel', '200 ml', 19, false, 'jar', null],
                ['Man Paste', '100 ml', 21, false, 'jar', null],
                ['Man Wax', '100 ml', 25, false, 'jar', null],
                ['Man Pomade', '100 ml', 24, false, 'jar', null],
            ],
            'earth-professional-color' => [
                ['Earth Permanent Color', '100 ml', 15, true, 'sachet', null],
                ['Earth Toning Solution 5 vol. – 1,5 %', '1000 ml', 16, true, 'tall', null],
                ['Earth Creme Activator 10 vol – 3 %', '1000 ml', 16, true, 'tall', null],
                ['Earth Creme Activator 20 vol – 6 %', '1000 ml', 16, true, 'tall', null],
                ['Earth Creme Activator 30 vol – 9 %', '1000 ml', 16, true, 'tall', null],
                ['Earth Creme Activator 40 vol – 12 %', '1000 ml', 16, true, 'tall', null],
                ['Earth Free Hand Bleaching Powder', '450 g', 48, true, 'jar', null],
                ['Earth Gentle Bleaching Paste', '450 g', 51, true, 'jar', null],
                ['Earth Powder Infusion', '200 g', 46, true, 'jar', null],
            ],
            'virtuos-professional-color' => [
                ['Virtuos Color', '100 ml', 13, true, 'sachet', null],
                ['Peroxide 10 vol – 3 %', '150 ml', 4, true, 'tall', 'peroxide-10'],
                ['Peroxide 10 vol – 3 %', '1000 ml', 12, true, 'tall', 'peroxide-10'],
                ['Peroxide 20 vol – 6 %', '150 ml', 4, true, 'tall', 'peroxide-20'],
                ['Peroxide 20 vol – 6 %', '1000 ml', 12, true, 'tall', 'peroxide-20'],
                ['Peroxide 20 vol – 6 %', '5000 ml', 55, true, 'tall', 'peroxide-20'],
                ['Peroxide 30 vol – 9 %', '150 ml', 4, true, 'tall', 'peroxide-30'],
                ['Peroxide 30 vol – 9 %', '1000 ml', 12, true, 'tall', 'peroxide-30'],
                ['Peroxide 40 vol – 12 %', '1000 ml', 12, true, 'tall', null],
                ['White Bleach', '500 g', 31, true, 'jar', null],
                ['Blue Bleach', '500 g', 31, true, 'jar', null],
                ['Violet Bleach', '500 g', 31, true, 'jar', null],
            ],
            'waving-system' => [
                ['Organic Aloe Vera Neutralizer', '1000 ml', 22, true, 'tall', null],
                ['Organic Aloe Waving Lotion 1', '200 ml', 27, true, 'tall', null],
                ['Organic Aloe Waving Lotion 2', '200 ml', 27, true, 'tall', null],
            ],
            'doplnky' => [
                ['Vzorkovník Earth Powder Infusion', '—', 34, true, 'sachet', null],
                ['Vzorkovník Earth Permanent Color', '—', 26, true, 'sachet', null],
                ['Vzorkovník Virtuos Color', '—', 26, true, 'sachet', null],
                ['Plátená taška S 19 × 25 cm – béžová', '—', 7, false, 'sachet', null],
                ['Plátená taška L 38 × 42 cm – béžová', '—', 9, false, 'sachet', null],
                ['Darčeková taška Premium L 36 × 12 × 31', '—', 9, false, 'sachet', null],
                ['Darčeková taška ECO M 28 × 11 × 22', '—', 3, false, 'sachet', null],
            ],
        ];

        $i = 0;
        foreach ($catalog as $lineSlug => $rows) {
            $line = $bySlug->get($lineSlug);
            foreach ($rows as [$name, $volume, $price, $b2bOnly, $kind, $variantGroup]) {
                $i++;
                $slug = Str::slug($name . ' ' . $volume);
                $imagePath = file_exists(public_path('products/' . $slug . '.png'))
                    ? '/products/' . $slug . '.png'
                    : null;

                Product::updateOrCreate(['slug' => $slug], [
                    'line_id'       => $line?->id,
                    'code'          => str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                    'variant_group' => $variantGroup,
                    'name'          => $name,
                    'subtitle'      => 'Lorem ipsum dolor sit amet',
                    'line_label'    => $line?->name ?? '',
                    'complex'       => $line?->complex ?? '—',
                    'volume'        => $volume,
                    'price'         => $price,
                    'kind'          => $kind,
                    'tone'          => $tones[$lineSlug] ?? '#16140f',
                    'cap'           => $lineSlug === 'blonde' ? '#16140f' : null,
                    'image_path'    => $imagePath,
                    'description'   => self::LOREM,
                    'sort_order'    => $i,
                    'published'     => true,
                    'b2b_only'      => $b2bOnly,
                ]);
            }
        }
    }
}
