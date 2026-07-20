<?php $__env->startSection('title', 'PREVIA - Profesionálna vlasová kozmetika z Talianska'); ?>

<?php $__env->startSection('content'); ?>

<?php
    $featured = $heroProduct ?? $topProducts->first();
?>

<section class="hero">
    <div class="hero-l">
        <div class="eyebrow">Lorem ipsum · Dolor sit amet</div>
        <h1 class="h1">Lorem,<br><em>ipsum dolor</em><br>sit amet.</h1>
        <p class="lede">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
        <div class="hero-cta">
            <a href="<?php echo e(route('shop.index')); ?>" class="btn">Vstúpiť do eshopu</a>
            <a href="<?php echo e(route('b2b.login')); ?>" class="btn btn-line">Pre salóny ›</a>
        </div>
        <div class="hero-spec">
            <div>Lorem<strong>Ipsum dolor</strong></div>
            <div>Consectetur<strong>Adipiscing</strong></div>
            <div>Tempor<strong>Incididunt</strong></div>
        </div>
    </div>
    <div class="hero-r hero-r--photo">
        <img src="<?php echo e(asset('images/redesign/hero.jpg')); ?>" alt="PREVIA - profesionálna vlasová kozmetika" loading="eager">
        <?php if($featured): ?>
            <div class="hero-callout">
                <div class="meta">Novinka · <?php echo e(now()->format('m / y')); ?></div>
                <div class="name"><?php echo e($featured->name); ?></div>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="strip">
    <?php $__currentLoopData = [
        ['n' => 'i.',   't' => 'Vyrobená v Taliansku · vysoký podiel prírodných ingrediencií'],
        ['n' => 'ii.',  't' => 'Bez sulfátov, parabénov a silikónov'],
        ['n' => 'iii.', 't' => 'Vegan a cruelty-free · eco-friendly obaly'],
        ['n' => 'iv.',  't' => 'Zdravie vlasov a pokožky hlavy · aj pre citlivú pokožku'],
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="strip-it"><div class="n"><?php echo e($s['n']); ?></div><div class="t"><?php echo e($s['t']); ?></div></div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</section>

<section class="products">
    <div class="section-head">
        <h2 class="h2"><em>Lorem ipsum</em><br>dolor sit amet consectetur.</h2>
        <a href="<?php echo e(route('shop.index')); ?>" class="section-sub" style="text-decoration:none">Pozrieť všetkých <?php echo e($totalProducts); ?> →</a>
    </div>
    <div class="grid-4">
        <?php $__currentLoopData = $topProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo $__env->make('partials.product-card', ['p' => $p], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</section>

<section class="img-band">
    <div class="img-band-it"><img src="<?php echo e(asset('images/redesign/texture-foam.jpg')); ?>" alt="Textúra peny" loading="lazy"></div>
    <div class="img-band-it"><img src="<?php echo e(asset('images/redesign/band-2.jpg')); ?>" alt="Starostlivosť o vlasy" loading="lazy"></div>
    <div class="img-band-it"><img src="<?php echo e(asset('images/redesign/band-bottles.jpg')); ?>" alt="PREVIA produkty" loading="lazy"></div>
</section>



<section class="lines">
    <div class="section-head">
        <h2 class="h2">Lorem<br><em>ipsum.</em></h2>
        <div class="section-sub">Dolor sit amet consectetur</div>
    </div>
    <?php $__currentLoopData = $lines->chunk(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chunk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="lines-grid" <?php if(!$loop->first): ?> style="border-top:none" <?php endif; ?>>
            <?php $__currentLoopData = $chunk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('shop.index', ['line' => $l->slug])); ?>" class="line-it" style="text-decoration:none;color:inherit">
                    <div class="line-it-top">
                        <span class="num">línia <?php echo e($l->code); ?></span>
                        <span class="arrow">→</span>
                    </div>
                    <div class="nm"><?php echo e($l->name); ?><em><?php echo e($l->eyebrow); ?></em></div>
                    <div class="ds"><?php echo e($l->description); ?></div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</section>

<section class="cta-quiz">
    <div class="cta-quiz-l">
        <div class="eyebrow">Lorem ipsum · Dolor sit</div>
        <h2 class="h2">Lorem ipsum dolor<br><em>sit amet consectetur.</em></h2>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore.</p>
        <div class="cta-quiz-cta">
            <a href="<?php echo e(route('quiz.show')); ?>" class="btn btn-light">Začať diagnostiku →</a>
            <span class="cta-quiz-meta">Lorem ipsum · dolor sit</span>
        </div>
    </div>
    <div class="cta-quiz-r cta-quiz-r--photo">
        <img src="<?php echo e(asset('images/redesign/texture-oil.jpg')); ?>" alt="Diagnostika vlasov PREVIA" loading="lazy">
    </div>
</section>

<section class="claims-banner">
    <img src="<?php echo e(asset('images/redesign/banner.jpg')); ?>" alt="PREVIA" loading="lazy">
    <div class="claims-banner-in">
        <div class="eyebrow">Lorem ipsum. Dolor sit amet.</div>
        <h2 class="claims-head">Lorem ipsum<br>dolor <em>sit amet.</em></h2>
    </div>
</section>

<section class="b2b-band b2b-band--light">
    <div class="b2b-band-l">
        <div class="eyebrow">Lorem ipsum</div>
        <h2 class="h2">Lorem ipsum dolor<br><em>sit amet consectetur?</em></h2>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore.</p>
        <a href="<?php echo e(route('b2b.register')); ?>" class="btn" style="align-self:flex-start;text-decoration:none">Získať prístup pre salón →</a>
    </div>
    <div class="b2b-band-r">
        <div class="b2b-li"><div class="n">01</div><div><div class="t">Lorem ipsum dolor</div><div class="ds">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div></div></div>
        <div class="b2b-li"><div class="n">02</div><div><div class="t">Sed do eiusmod</div><div class="ds">Sed do eiusmod tempor incididunt ut labore et dolore magna.</div></div></div>
        <div class="b2b-li"><div class="n">03</div><div><div class="t">Ut enim ad minim</div><div class="ds">Ut enim ad minim veniam, quis nostrud exercitation ullamco.</div></div></div>
        <div class="b2b-li"><div class="n">04</div><div><div class="t">Duis aute irure</div><div class="ds">Duis aute irure dolor in reprehenderit in voluptate velit esse.</div></div></div>
    </div>
</section>

<section class="cta-philosophy">
    <div class="cta-philosophy-l">
        <div class="eyebrow">Lorem ipsum · Dolor sit</div>
        <h2 class="h2">Lorem ipsum dolor.<br><em>Sit amet consectetur.</em></h2>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
        <div class="cta-philosophy-pillars">
            <div><strong>Pôvod</strong><span>Vyrobená v Taliansku — kombinácia prírody a high-end technológie.</span></div>
            <div><strong>Zloženie</strong><span>Vysoký podiel prírodných ingrediencií, bez sulfátov, parabénov a silikónov.</span></div>
            <div><strong>Hodnoty</strong><span>Vegan a cruelty-free, eco-friendly prístup a obaly.</span></div>
            <div><strong>Zameranie</strong><span>Zdravie vlasov a pokožky hlavy, vhodná aj pre citlivú pokožku.</span></div>
        </div>
        <a href="<?php echo e(route('philosophy.show')); ?>" class="btn">Objaviť filozofiu →</a>
    </div>
    <div class="cta-philosophy-r cta-philosophy-r--photo">
        <img src="<?php echo e(asset('images/redesign/texture-cream.jpg')); ?>" alt="Textúra PREVIA" loading="lazy">
    </div>
</section>



<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/martinachberger/Projects/public/previa/resources/views/pages/home.blade.php ENDPATH**/ ?>