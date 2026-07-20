<?php $__env->startSection('title', 'Eshop - všetky línie · PREVIA'); ?>

<?php $__env->startSection('content'); ?>

<?php
    $typeLabels = \App\Models\Product::typeLabels();

    $current = [
        'line' => $activeLine,
        'type' => $activeType,
        'sort' => $activeSort,
    ];
    $toggleUrl = function ($key, $value) use ($current) {
        $params = $current;
        $params[$key] = ($params[$key] ?? null) === $value ? null : $value;
        return route('shop.index', array_filter($params, fn ($v) => $v !== null && $v !== ''));
    };
    $setSortUrl = function ($value) use ($current) {
        $params = $current;
        $params['sort'] = $value;
        return route('shop.index', array_filter($params, fn ($v) => $v !== null && $v !== ''));
    };
    $hasAnyFilter = (bool) ($activeLine || $activeType);
?>

<section class="shop-head">
    <div class="crumbs">
        <a href="<?php echo e(route('home')); ?>" style="color:inherit;text-decoration:none">PREVIA</a>
        <span class="sep">/</span>
        <span>Eshop</span>
    </div>
    <h1>Všetky<br><em>produkty.</em></h1>
    <div class="meta">
        <div class="ds">Lorem ipsum dolor sit amet, consectetur adipiscing elit sed do eiusmod.</div>
        <a href="<?php echo e(route('quiz.show')); ?>" class="cnt" style="text-decoration:none;color:inherit;border:1px solid var(--line);padding:10px 18px">Lorem ipsum dolor sit amet consectetur?</a>
    </div>
</section>

<div class="shop-body">
    <aside class="shop-fil">
        <div class="grp">
            <h4>Línia</h4>
            <?php $__currentLoopData = $lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e($toggleUrl('line', $line->slug)); ?>" class="opt <?php echo e($activeLine === $line->slug ? 'on' : ''); ?>" style="text-decoration:none;color:inherit;display:flex">
                    <div class="box"></div>
                    <div class="lab"><?php echo e($line->name); ?></div>
                    <span class="cnt"><?php echo e(str_pad($line->products()->where('published', true)->count(), 2, '0', STR_PAD_LEFT)); ?></span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="grp">
            <h4>Typ produktu</h4>
            <?php $__currentLoopData = $typeLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $typeSlug => $typeLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(($typeCounts[$typeSlug] ?? 0) > 0): ?>
                    <a href="<?php echo e($toggleUrl('type', $typeSlug)); ?>" class="opt <?php echo e($activeType === $typeSlug ? 'on' : ''); ?>" style="text-decoration:none;color:inherit;display:flex">
                        <div class="box"></div>
                        <div class="lab"><?php echo e($typeLabel); ?></div>
                        <span class="cnt"><?php echo e(str_pad($typeCounts[$typeSlug], 2, '0', STR_PAD_LEFT)); ?></span>
                    </a>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </aside>

    <div class="shop-list">
        <?php if($activeLineModel && $activeLineModel->description && $lines->contains('slug', $activeLine)): ?>
            <div class="line-intro">
                <div class="line-intro-eyebrow"><?php echo e($activeLineModel->eyebrow ?: 'Línia'); ?></div>
                <h2><?php echo e($activeLineModel->name); ?></h2>
                <p><?php echo e($activeLineModel->description); ?></p>
            </div>
        <?php endif; ?>
        <div class="shop-list-bar">
            <div class="chips">
                <?php if($activeLine): ?>
                    <a href="<?php echo e($toggleUrl('line', $activeLine)); ?>" class="chip" style="text-decoration:none;color:inherit"><?php echo e($lines->firstWhere('slug', $activeLine)?->name ?? $activeLine); ?> <span class="x">×</span></a>
                <?php endif; ?>
                <?php if($activeType): ?>
                    <a href="<?php echo e($toggleUrl('type', $activeType)); ?>" class="chip" style="text-decoration:none;color:inherit"><?php echo e($typeLabels[$activeType] ?? $activeType); ?> <span class="x">×</span></a>
                <?php endif; ?>
                <?php if($hasAnyFilter): ?>
                    <a href="<?php echo e(route('shop.index', array_filter(['sort' => $activeSort]))); ?>" class="chip" style="background:transparent;color:var(--mute);text-decoration:none">Vyčistiť filtre</a>
                <?php endif; ?>
            </div>
            <div style="display:flex;gap:14px;align-items:center;font-size:12px;color:var(--mute)">
                <span>Zoradiť:</span>
                <a href="<?php echo e($setSortUrl(null)); ?>" style="color:<?php echo e(!$activeSort ? 'var(--ink)' : 'inherit'); ?>;text-decoration:<?php echo e(!$activeSort ? 'underline' : 'none'); ?>;text-underline-offset:4px">Predvolené</a>
                <a href="<?php echo e($setSortUrl('price-asc')); ?>" style="color:<?php echo e($activeSort === 'price-asc' ? 'var(--ink)' : 'inherit'); ?>;text-decoration:<?php echo e($activeSort === 'price-asc' ? 'underline' : 'none'); ?>;text-underline-offset:4px">Cena ↑</a>
                <a href="<?php echo e($setSortUrl('price-desc')); ?>" style="color:<?php echo e($activeSort === 'price-desc' ? 'var(--ink)' : 'inherit'); ?>;text-decoration:<?php echo e($activeSort === 'price-desc' ? 'underline' : 'none'); ?>;text-underline-offset:4px">Cena ↓</a>
            </div>
        </div>
        <?php if($products->isEmpty()): ?>
            <div style="padding:64px 0;text-align:center;color:var(--mute)">
                <p>Pre zvolené filtre žiadne produkty.</p>
                <a href="<?php echo e(route('shop.index')); ?>" style="color:inherit">Vyčistiť filtre →</a>
            </div>
        <?php else: ?>
            <div class="shop-grid">
                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo $__env->make('partials.product-card', ['p' => $p], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/martinachberger/Projects/public/previa/resources/views/pages/shop.blade.php ENDPATH**/ ?>