<?php ($active ??= null); ?>
<?php ($b2b = auth('b2b')->user()); ?>
<header class="nav">
    <div class="nav-c">
        <a href="<?php echo e(route('home')); ?>" class="<?php echo e($active === 'home' ? 'active' : ''); ?>">Domov</a>
        <a href="<?php echo e(route('shop.index')); ?>" class="<?php echo e($active === 'shop' ? 'active' : ''); ?>">Eshop</a>
        <a href="<?php echo e(route('quiz.show')); ?>" class="<?php echo e($active === 'quiz' ? 'active' : ''); ?>">Diagnostika</a>
        <a href="<?php echo e(route('philosophy.show')); ?>" class="<?php echo e($active === 'philosophy' ? 'active' : ''); ?>">Filozofia</a>
    </div>
    <a href="<?php echo e(route('home')); ?>" class="brand" aria-label="PREVIA">
        <span class="brand-word">Previa</span>
        <span class="brand-sub">Italian Haircare · Distribúcia SK</span>
    </a>
    <div class="nav-r">
        <?php if($b2b): ?>
            <a href="<?php echo e(route('b2b.dashboard')); ?>" class="ic" style="color:var(--ink);text-decoration:none"><?php echo e($b2b->salon_name); ?> · −<?php echo e($b2b->discount_pct); ?>%</a>
        <?php endif; ?>
        <a href="<?php echo e(route('cart.show')); ?>" class="ic" data-cart-open data-cart-count style="text-decoration:none;color:inherit">Košík · 0</a>
        <?php if($b2b): ?>
            <a href="<?php echo e(route('b2b.logout')); ?>" class="b2b" onclick="event.preventDefault();document.getElementById('b2b-logout').submit();" style="text-decoration:none">Odhlásiť</a>
            <form id="b2b-logout" action="<?php echo e(route('b2b.logout')); ?>" method="POST" style="display:none"><?php echo csrf_field(); ?></form>
        <?php else: ?>
            <a href="<?php echo e(route('b2b.login')); ?>" class="b2b" style="text-decoration:none">Per saloni</a>
        <?php endif; ?>
    </div>
</header>
<?php /**PATH /Users/martinachberger/Projects/public/previa/resources/views/partials/nav.blade.php ENDPATH**/ ?>