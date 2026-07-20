<?php $__env->startSection('title', 'Registrácia salónu - PREVIA'); ?>

<?php $__env->startSection('content'); ?>

<section class="auth-page auth-page--wide">
    <div class="auth-card">
        <div class="auth-head">
            <p class="eyebrow">Pre profesionálne salóny</p>
            <h1>Pridajte sa k PREVIA.</h1>
            <p class="lede" style="margin:0">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
        </div>

        <?php if($errors->any()): ?>
            <div class="auth-msg auth-msg--err">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div><?php echo e($err); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('b2b.register.submit')); ?>" class="auth-form">
            <?php echo csrf_field(); ?>
            <h3 class="auth-h3">Salón</h3>
            <div class="ck-row">
                <div class="ck-fi">
                    <label>Názov salónu</label>
                    <input type="text" name="salon_name" value="<?php echo e(old('salon_name')); ?>" required>
                </div>
                <div class="ck-fi">
                    <label>Kontaktná osoba</label>
                    <input type="text" name="contact_name" value="<?php echo e(old('contact_name')); ?>" required>
                </div>
            </div>
            <div class="ck-row">
                <div class="ck-fi">
                    <label>IČO</label>
                    <input type="text" name="ico" value="<?php echo e(old('ico')); ?>">
                </div>
                <div class="ck-fi">
                    <label>IČ DPH (voliteľné)</label>
                    <input type="text" name="vat_id" value="<?php echo e(old('vat_id')); ?>">
                </div>
            </div>

            <h3 class="auth-h3">Adresa</h3>
            <div class="ck-row">
                <div class="ck-fi">
                    <label>Ulica a číslo</label>
                    <input type="text" name="address" value="<?php echo e(old('address')); ?>">
                </div>
            </div>
            <div class="ck-row">
                <div class="ck-fi">
                    <label>Mesto</label>
                    <input type="text" name="city" value="<?php echo e(old('city')); ?>">
                </div>
                <div class="ck-fi" style="max-width:160px">
                    <label>PSČ</label>
                    <input type="text" name="zip" value="<?php echo e(old('zip')); ?>">
                </div>
            </div>

            <h3 class="auth-h3">Prihlasovacie údaje</h3>
            <div class="ck-row">
                <div class="ck-fi">
                    <label>E-mail</label>
                    <input type="email" name="email" value="<?php echo e(old('email')); ?>" required>
                </div>
                <div class="ck-fi">
                    <label>Telefón</label>
                    <input type="tel" name="phone" value="<?php echo e(old('phone')); ?>">
                </div>
            </div>
            <div class="ck-row">
                <div class="ck-fi">
                    <label>Heslo (min 8 znakov)</label>
                    <input type="password" name="password" required>
                </div>
                <div class="ck-fi">
                    <label>Heslo znova</label>
                    <input type="password" name="password_confirmation" required>
                </div>
            </div>

            <button type="submit" class="btn">Odoslať registráciu</button>
        </form>

        <div class="auth-foot">
            Už máš účet? <a href="<?php echo e(route('b2b.login')); ?>">Prihlás sa</a>
        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/martinachberger/Projects/public/previa/resources/views/pages/b2b/register.blade.php ENDPATH**/ ?>