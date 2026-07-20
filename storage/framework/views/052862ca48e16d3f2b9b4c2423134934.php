<?php $__env->startSection('title', 'Diagnostika vlasov - PREVIA'); ?>
<?php $__env->startSection('description', 'Personalizovaná rutina pre tvoje vlasy. Štyri otázky, presné odporúčanie zo siedmich línií PREVIA.'); ?>

<?php $__env->startSection('content'); ?>

<section class="quiz">
    <div class="quiz-head">
        <div class="eyebrow">Lorem ipsum · Dolor sit</div>
        <h1>Lorem ipsum<br><em>dolor sit amet.</em></h1>
        <p class="lede">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore.</p>
    </div>

    <div class="quiz-progress" data-quiz-progress>
        <div class="quiz-progress-bar"><div class="fill" data-quiz-fill style="width:25%"></div></div>
        <div class="quiz-progress-label"><span data-quiz-step>1</span> / 4</div>
    </div>

    <form method="POST" action="<?php echo e(route('quiz.result')); ?>" class="quiz-form" data-quiz-form>
        <?php echo csrf_field(); ?>

        <?php if($errors->any()): ?>
            <div class="quiz-err">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div><?php echo e($err); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <div class="quiz-step" data-quiz-step-el="1">
            <h2>Aký je tvoj typ vlasov?</h2>
            <div class="quiz-opts">
                <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="quiz-opt">
                        <input type="radio" name="type" value="<?php echo e($val); ?>" required>
                        <span><?php echo e($label); ?></span>
                    </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <div class="quiz-step" data-quiz-step-el="2" hidden>
            <h2>Aký je tvoj hlavný problém?</h2>
            <div class="quiz-opts">
                <?php $__currentLoopData = $problems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="quiz-opt">
                        <input type="radio" name="problem" value="<?php echo e($val); ?>" required>
                        <span><?php echo e($label); ?></span>
                    </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <div class="quiz-step" data-quiz-step-el="3" hidden>
            <h2>Prešli vlasy niektorou z týchto úprav?</h2>
            <div class="quiz-opts">
                <?php $__currentLoopData = $treatments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="quiz-opt">
                        <input type="radio" name="treatment" value="<?php echo e($val); ?>" required>
                        <span><?php echo e($label); ?></span>
                    </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <div class="quiz-step" data-quiz-step-el="4" hidden>
            <h2>Ako často si umývate vlasy?</h2>
            <div class="quiz-opts">
                <?php $__currentLoopData = $frequencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="quiz-opt">
                        <input type="radio" name="frequency" value="<?php echo e($val); ?>" required>
                        <span><?php echo e($label); ?></span>
                    </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <button type="submit" class="btn quiz-submit">Objaviť moje produkty →</button>
        </div>

        <div class="quiz-back-wrap">
            <button type="button" class="quiz-back" data-quiz-back hidden>← Späť</button>
        </div>
    </form>
</section>

<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    var form = document.querySelector('[data-quiz-form]');
    if (!form) return;
    var steps = form.querySelectorAll('[data-quiz-step-el]');
    var fill = document.querySelector('[data-quiz-fill]');
    var label = document.querySelector('[data-quiz-step]');
    var back = form.querySelector('[data-quiz-back]');
    var current = 1;
    var total = steps.length;

    function show(n) {
        steps.forEach(function (el) {
            el.hidden = parseInt(el.dataset.quizStepEl, 10) !== n;
        });
        fill.style.width = (n / total * 100) + '%';
        label.textContent = n;
        back.hidden = n === 1;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function autoAdvance(input) {
        var stepEl = input.closest('[data-quiz-step-el]');
        if (!stepEl) return;
        var stepNum = parseInt(stepEl.dataset.quizStepEl, 10);
        if (input.type === 'radio' && stepNum < total) {
            setTimeout(function () {
                current = stepNum + 1;
                show(current);
            }, 220);
        }
    }

    form.addEventListener('change', function (e) {
        if (e.target.matches('input[type="radio"]')) autoAdvance(e.target);
    });

    form.querySelectorAll('[data-quiz-next]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (current < total) { current++; show(current); }
        });
    });

    back.addEventListener('click', function () {
        if (current > 1) { current--; show(current); }
    });

    show(1);
})();
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/martinachberger/Projects/public/previa/resources/views/pages/quiz.blade.php ENDPATH**/ ?>