<?php $__env->startSection('title', 'Blog - listy z laboratória · PREVIA'); ?>

<?php $__env->startSection('content'); ?>

<section class="j-hero">
    <div>
        <div class="eyebrow">Blog · Edukácia</div>
        <h1>Lorem ipsum<br>dolor <em>sit amet.</em></h1>
    </div>
    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
</section>

<?php if($featured): ?>
    <section class="j-feat">
        <div class="ph" style="background-image:url('<?php echo e($featured->cover_image_url); ?>')"></div>
        <div class="body">
            <div class="meta">- Hlavný článok · <?php echo e($featured->category); ?> · <?php echo e($featured->read_time); ?> · <?php echo e($featured->published_at?->translatedFormat('F Y')); ?></div>
            <h2><?php echo e($featured->title); ?></h2>
            <p><?php echo e($featured->excerpt); ?></p>
            <a href="<?php echo e(route('blog.show', $featured->slug)); ?>" class="btn btn-line" style="align-self:flex-start;text-decoration:none">Čítať článok →</a>
        </div>
    </section>
<?php endif; ?>

<?php if($articles->isNotEmpty()): ?>
<section class="j-list">
    <h3>Ďalšie články <small><?php echo e(now()->format('m / Y')); ?></small></h3>
    <div class="blog-grid">
        <?php $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('blog.show', $a->slug)); ?>" class="j-card" style="text-decoration:none;color:inherit">
                <div class="ph" style="background-image:url('<?php echo e($a->cover_image_url); ?>')"></div>
                <div class="meta"><?php echo e($a->meta); ?></div>
                <div class="ti"><?php echo e($a->title); ?></div>
                <div class="ex"><?php echo e($a->excerpt); ?></div>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</section>
<?php endif; ?>

<section class="b2b-band">
    <div class="b2b-band-l">
        <div class="eyebrow" style="color:rgba(255,255,255,0.5)">- O značke</div>
        <h2 class="h2">PREVIA<br><em>Vyrobené v Taliansku.</em></h2>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit sed do eiusmod tempor.</p>
    </div>
    <div class="b2b-band-r">
        <div class="b2b-li"><div class="n">01</div><div><div class="t">Made in Italy</div><div class="ds">Vyvíjané a vyrábané kompletne v Taliansku.</div></div></div>
        <div class="b2b-li"><div class="n">02</div><div><div class="t">Pre salóny aj domácu starostlivosť</div><div class="ds">Profesionálne formulácie pre obe použitia.</div></div></div>
        <div class="b2b-li"><div class="n">03</div><div><div class="t">Bez sulfátov, parabénov a silikónov</div><div class="ds">Striktné štandardy pri výbere zložiek.</div></div></div>
        <div class="b2b-li"><div class="n">04</div><div><div class="t">Vegan a netestované na zvieratách</div><div class="ds">Pre celé portfólio.</div></div></div>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/martinachberger/Projects/public/previa/resources/views/pages/blog.blade.php ENDPATH**/ ?>