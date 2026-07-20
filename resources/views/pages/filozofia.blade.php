@extends('layouts.app')

@section('title', 'Filozofia - PREVIA')
@section('description', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt.')

@section('content')

<section class="j-feat j-feat--first">
    <div class="ph" style="background-image:url('{{ asset('images/redesign/filozofia1.jpg') }}')"></div>
    <div class="body">
        <div class="meta">- Lorem</div>
        <h2>Lorem ipsum<br><em>dolor sit.</em></h2>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
    </div>
</section>

<section class="j-feat">
    <div class="ph" style="background-image:url('{{ asset('images/redesign/filozofia2.jpg') }}')"></div>
    <div class="body">
        <div class="meta">- Ipsum</div>
        <h2>Duis aute<br><em>irure dolor.</em></h2>
        <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
    </div>
</section>

<section class="j-feat">
    <div class="ph" style="background-image:url('{{ asset('images/redesign/filozofia3.jpg') }}')"></div>
    <div class="body">
        <div class="meta">- Dolor</div>
        <h2>Sed ut<br><em>perspiciatis unde.</em></h2>
        <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>
    </div>
</section>

<section class="pure">
    <div class="pure-head">
        <div class="eyebrow center">- Lorem · Ipsum dolor</div>
        <h2 class="h2">Lorem ipsum <em>dolor.</em></h2>
    </div>
    <div class="pure-grid">
        <div class="pure-block">
            <div class="pure-no">01 — Lorem Ipsum</div>
            <p class="pure-lead">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod.</p>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco.</p>
        </div>
        <div class="pure-block">
            <div class="pure-no">02 — Dolor Sit</div>
            <p class="pure-lead">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum.</p>
            <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia.</p>
        </div>
        <div class="pure-block">
            <div class="pure-no">03 — Amet Elit</div>
            <p class="pure-lead">Sed ut perspiciatis unde omnis iste natus error sit voluptatem.</p>
            <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis.</p>
        </div>
    </div>
</section>

<section class="j-list">
    <h3>Lorem ipsum <small>Dolor sit amet</small></h3>
    <div class="howto-grid" style="grid-template-columns: repeat(4, 1fr)">
        <div class="howto-step">
            <div class="n">- 01</div>
            <div class="ti">Lorem</div>
            <div class="ds">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod.</div>
        </div>
        <div class="howto-step">
            <div class="n">- 02</div>
            <div class="ti">Ipsum</div>
            <div class="ds">Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</div>
        </div>
        <div class="howto-step">
            <div class="n">- 03</div>
            <div class="ti">Dolor</div>
            <div class="ds">Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.</div>
        </div>
        <div class="howto-step">
            <div class="n">- 04</div>
            <div class="ti">Amet</div>
            <div class="ds">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum.</div>
        </div>
    </div>
</section>

@endsection
