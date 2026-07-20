<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php echo $__env->yieldContent('title', 'PREVIA - Eshop'); ?></title>
    <meta name="description" content="<?php echo $__env->yieldContent('description', 'Talianska profesionálna vlasová kozmetika PREVIA. Prírodné ingrediencie, vegan a cruelty-free. Distribúcia pre Slovensko.'); ?>">

    <meta property="og:title" content="<?php echo $__env->yieldContent('title', 'PREVIA'); ?>">
    <meta property="og:description" content="<?php echo $__env->yieldContent('description', 'Talianska profesionálna vlasová kozmetika PREVIA. Distribúcia pre Slovensko.'); ?>">
    <meta property="og:type" content="website">

    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=Inter:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>?v=<?php echo e(filemtime(public_path('css/app.css'))); ?>">
    <script>document.documentElement.classList.add('reveal-host');</script>
</head>
<body>

    <?php echo $__env->make('partials.bar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.nav', ['active' => $active ?? null], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->yieldContent('content'); ?>

    <?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('partials.cart-drawer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <script src="<?php echo e(asset('js/cart.js')); ?>?v=<?php echo e(filemtime(public_path('js/cart.js'))); ?>"></script>
    <script src="<?php echo e(asset('js/reveal.js')); ?>?v=<?php echo e(filemtime(public_path('js/reveal.js'))); ?>" defer></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>

</body>
</html>
<?php /**PATH /Users/martinachberger/Projects/public/previa/resources/views/layouts/app.blade.php ENDPATH**/ ?>