@extends('layouts.app')

@section('title', 'Diagnostika vlasov - PREVIA')
@section('description', 'Personalizovaná rutina pre tvoje vlasy. Štyri otázky, presné odporúčanie zo siedmich línií PREVIA.')

@section('content')

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

    <form method="POST" action="{{ route('quiz.result') }}" class="quiz-form" data-quiz-form>
        @csrf

        @if ($errors->any())
            <div class="quiz-err">
                @foreach ($errors->all() as $err)<div>{{ $err }}</div>@endforeach
            </div>
        @endif

        <div class="quiz-step" data-quiz-step-el="1">
            <h2>Aký je tvoj typ vlasov?</h2>
            <div class="quiz-opts">
                @foreach ($types as $val => $label)
                    <label class="quiz-opt">
                        <input type="radio" name="type" value="{{ $val }}" required>
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="quiz-step" data-quiz-step-el="2" hidden>
            <h2>Aký je tvoj hlavný problém?</h2>
            <div class="quiz-opts">
                @foreach ($problems as $val => $label)
                    <label class="quiz-opt">
                        <input type="radio" name="problem" value="{{ $val }}" required>
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="quiz-step" data-quiz-step-el="3" hidden>
            <h2>Prešli vlasy niektorou z týchto úprav?</h2>
            <div class="quiz-opts">
                @foreach ($treatments as $val => $label)
                    <label class="quiz-opt">
                        <input type="radio" name="treatment" value="{{ $val }}" required>
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="quiz-step" data-quiz-step-el="4" hidden>
            <h2>Ako často si umývate vlasy?</h2>
            <div class="quiz-opts">
                @foreach ($frequencies as $val => $label)
                    <label class="quiz-opt">
                        <input type="radio" name="frequency" value="{{ $val }}" required>
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>
            <button type="submit" class="btn quiz-submit">Objaviť moje produkty →</button>
        </div>

        <div class="quiz-back-wrap">
            <button type="button" class="quiz-back" data-quiz-back hidden>← Späť</button>
        </div>
    </form>
</section>

@push('scripts')
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
@endpush

@endsection
