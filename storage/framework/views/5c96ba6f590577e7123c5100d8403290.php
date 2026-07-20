<?php ($active ??= null); ?>
<nav class="b2b-subnav">
    <div class="b2b-subnav-inner">
        <a href="<?php echo e(route('b2b.dashboard')); ?>" class="<?php echo e($active === 'dashboard' ? 'active' : ''); ?>">Prehľad</a>
        <a href="<?php echo e(route('b2b.orders')); ?>" class="<?php echo e($active === 'orders' ? 'active' : ''); ?>">Objednávky</a>
        <a href="<?php echo e(route('b2b.colors')); ?>" class="<?php echo e($active === 'colors' ? 'active' : ''); ?>">Farby</a>
        <a href="<?php echo e(route('shop.index')); ?>">Katalóg</a>
        <a href="<?php echo e(route('b2b.profile')); ?>" class="<?php echo e($active === 'profile' ? 'active' : ''); ?>">Profil</a>
    </div>
</nav>
<?php /**PATH /Users/martinachberger/Projects/public/previa/resources/views/partials/b2b-nav.blade.php ENDPATH**/ ?>