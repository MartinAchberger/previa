<?php $__env->startSection('title', 'Prihlásenie pre salóny - PREVIA'); ?>

<?php $__env->startSection('content'); ?>

<section class="auth-page">
    <div class="auth-card">
        <div class="auth-head">
            <p class="eyebrow">Prístup pre salóny</p>
            <h1>Prihlásenie</h1>
            <p class="lede" style="margin:0">Zadajte svoje prihlasovacie údaje. Účet musí byť overený zo strany PREVIA.</p>
        </div>

        <?php if(session('success')): ?>
            <div class="auth-msg auth-msg--ok"><?php echo e(session('success')); ?></div>
        <?php endif; ?>
        <?php if($errors->any()): ?>
            <div class="auth-msg auth-msg--err">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div><?php echo e($err); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('b2b.login.submit')); ?>" class="auth-form">
            <?php echo csrf_field(); ?>
            <div class="ck-fi">
                <label>E-mail</label>
                <input type="email" name="email" value="<?php echo e(old('email')); ?>" required autofocus>
            </div>
            <div class="ck-fi">
                <label>Heslo</label>
                <input type="password" name="password" required>
            </div>
            <label class="auth-remember">
                <input type="checkbox" name="remember" value="1"> Zapamätať si ma
            </label>
            <button type="submit" class="btn">Prihlásiť sa</button>
        </form>

        <div class="auth-foot">
            Nemáte účet? <a href="<?php echo e(route('b2b.register')); ?>">Registrujte salón</a>
        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/martinachberger/Projects/public/previa/resources/views/pages/b2b/login.blade.php ENDPATH**/ ?>