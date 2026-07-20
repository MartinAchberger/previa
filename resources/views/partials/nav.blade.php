@php($active ??= null)
@php($b2b = auth('b2b')->user())
<header class="nav">
    <div class="nav-c">
        <a href="{{ route('home') }}" class="{{ $active === 'home' ? 'active' : '' }}">Domov</a>
        <a href="{{ route('shop.index') }}" class="{{ $active === 'shop' ? 'active' : '' }}">Eshop</a>
        <a href="{{ route('quiz.show') }}" class="{{ $active === 'quiz' ? 'active' : '' }}">Diagnostika</a>
        <a href="{{ route('philosophy.show') }}" class="{{ $active === 'philosophy' ? 'active' : '' }}">Filozofia</a>
    </div>
    <a href="{{ route('home') }}" class="brand" aria-label="PREVIA">
        <span class="brand-word">Previa</span>
        <span class="brand-sub">Italian Haircare · Distribúcia SK</span>
    </a>
    <div class="nav-r">
        @if($b2b)
            <a href="{{ route('b2b.dashboard') }}" class="ic" style="color:var(--ink);text-decoration:none">{{ $b2b->salon_name }} · −{{ $b2b->discount_pct }}%</a>
        @endif
        <a href="{{ route('cart.show') }}" class="ic" data-cart-open data-cart-count style="text-decoration:none;color:inherit">Košík · 0</a>
        @if($b2b)
            <a href="{{ route('b2b.logout') }}" class="b2b" onclick="event.preventDefault();document.getElementById('b2b-logout').submit();" style="text-decoration:none">Odhlásiť</a>
            <form id="b2b-logout" action="{{ route('b2b.logout') }}" method="POST" style="display:none">@csrf</form>
        @else
            <a href="{{ route('b2b.login') }}" class="b2b" style="text-decoration:none">Per saloni</a>
        @endif
    </div>
</header>
