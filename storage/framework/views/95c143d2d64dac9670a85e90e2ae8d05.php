<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['p']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['p']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>
<?php
    $b2b = auth('b2b')->user();
    // Product sale first (applies to everyone), then B2B salon discount on top.
    $base = $p->salePrice();
    $effectivePrice = $b2b && $b2b->discount_pct > 0 ? round($base * (1 - $b2b->discount_pct / 100), 2) : $base;
    $effectiveNet = round($effectivePrice / (1 + \App\Models\Product::VAT_RATE), 2);
    $netPrimary = $b2b && !empty($b2b->vat_id);
    $showStrike = $effectivePrice < (float) $p->price;
    $cardHasShades = $p->hasShades();
    $payload = [
        'id'      => (string) $p->id,
        'code'    => $p->code,
        'name'    => $p->name,
        'line'    => $p->line_label,
        'volume'  => $p->volume,
        'price'   => $effectivePrice,
    ];
?>
<div class="card-wrap">
    <a href="<?php echo e(route('product.show', $p->slug)); ?>" class="card">
        <div class="ph">
            <?php if($p->image_url): ?>
                <img src="<?php echo e($p->image_url); ?>" alt="<?php echo e($p->name); ?>" style="width:100%;height:100%;object-fit:contain;display:block;padding:12%;box-sizing:border-box">
            <?php else: ?>
                <div class="b-wrap">
                    <div><?php echo $__env->make('partials.bottle', ['kind' => $p->kind, 'tone' => $p->tone, 'cap' => $p->cap ?: $p->tone, 'sub' => $p->complex, 'n' => $p->code, 'label' => 'PREVIA'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
                </div>
            <?php endif; ?>
            <?php if($p->badge): ?>
                <div class="badge"><?php echo e($p->badge); ?></div>
            <?php endif; ?>
            <div class="no">n° <?php echo e($p->code); ?></div>
        </div>
        <div class="line"><?php echo e($p->line_label); ?></div>
        <div class="name"><?php echo e($p->name); ?></div>
        <div class="pr">
            <?php if($netPrimary): ?>
                <strong>€<?php echo e(number_format($effectiveNet, 2, ',', ' ')); ?></strong>
            <?php elseif($showStrike): ?>
                <strong>€<?php echo e(number_format($effectivePrice, 2, ',', ' ')); ?></strong>
                <s>€<?php echo e(number_format($p->price, 2, ',', ' ')); ?></s>
            <?php else: ?>
                <strong><?php echo e($p->price_formatted); ?></strong>
            <?php endif; ?>
        </div>
    </a>
    <?php if($cardHasShades): ?>
        <a href="<?php echo e(route('product.show', $p->slug)); ?>" class="card-cta">
            <span>Vybrať odtieň</span><span aria-hidden="true">→</span>
        </a>
    <?php else: ?>
        <button type="button" class="card-cta" data-cart-add data-product="<?php echo e(json_encode($payload, JSON_UNESCAPED_UNICODE)); ?>">
            <span>Pridať do košíka</span><span aria-hidden="true">→</span>
        </button>
    <?php endif; ?>
</div>
<?php /**PATH /Users/martinachberger/Projects/public/previa/resources/views/partials/product-card.blade.php ENDPATH**/ ?>