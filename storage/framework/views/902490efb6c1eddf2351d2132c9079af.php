<?php $__env->startSection('title', 'Portál pre salóny - ' . $b2b->salon_name); ?>

<?php $__env->startSection('content'); ?>

<?php echo $__env->make('partials.b2b-nav', ['active' => 'dashboard'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<section class="b2b-hero">
    <div class="b2b-hero-l">
        <p class="eyebrow">Portál pre salóny</p>
        <h1><?php echo e($b2b->salon_name); ?><br><em>−<?php echo e($b2b->discount_pct); ?> % na celý katalóg.</em></h1>
        <p class="lede">Vitaj späť, <?php echo e($b2b->contact_name); ?>. Tvoj veľkoobchodný cenník je aktívny - pri každom produkte v eshope vidíš automaticky upravenú cenu.</p>
        <div class="hero-cta">
            <a href="<?php echo e(route('shop.index')); ?>" class="btn">Otvoriť katalóg →</a>
            <a href="<?php echo e(route('b2b.orders')); ?>" class="btn btn-line">Moje objednávky</a>
        </div>
    </div>
    <div class="b2b-hero-r">
        <div class="b2b-stat"><div class="n"><?php echo e($stats['orders_total']); ?></div><div class="l">Objednávok celkovo</div></div>
        <div class="b2b-stat"><div class="n"><?php echo e($stats['orders_pending']); ?></div><div class="l">V spracovaní</div></div>
        <div class="b2b-stat"><div class="n">€<?php echo e(number_format($stats['spend_total'], 0, ',', ' ')); ?></div><div class="l">Obrat celkom</div></div>
        <div class="b2b-stat"><div class="n">−<?php echo e($b2b->discount_pct); ?>%</div><div class="l">Aktuálna zľava</div></div>
    </div>
</section>

<section class="b2b-recent">
    <div class="section-head">
        <h2 class="h2">Posledné objednávky</h2>
        <a href="<?php echo e(route('b2b.orders')); ?>" class="section-sub" style="text-decoration:none">Všetky →</a>
    </div>
    <?php if($orders->isEmpty()): ?>
        <div class="b2b-empty">
            <p>Zatiaľ žiadne objednávky. <a href="<?php echo e(route('shop.index')); ?>">Začni nakupovať →</a></p>
        </div>
    <?php else: ?>
        <div class="b2b-orders-table">
            <div class="b2b-or-head">
                <div>Číslo</div>
                <div>Dátum</div>
                <div>Položky</div>
                <div>Suma</div>
                <div>Stav</div>
                <div></div>
            </div>
            <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('b2b.order.detail', $o->order_number)); ?>" class="b2b-or-row">
                    <div><strong><?php echo e($o->order_number); ?></strong></div>
                    <div><?php echo e($o->created_at->format('j.n.Y')); ?></div>
                    <div><?php echo e($o->items()->sum('qty')); ?> ks</div>
                    <div><strong><?php echo e($o->totalFormatted()); ?></strong></div>
                    <div><span class="b2b-status b2b-status--<?php echo e($o->status); ?>"><?php echo e($o->statusLabel()); ?></span></div>
                    <div style="text-align:right">→</div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/martinachberger/Projects/public/previa/resources/views/pages/b2b/dashboard.blade.php ENDPATH**/ ?>