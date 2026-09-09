@php($active ??= null)
<nav class="b2b-subnav">
    <div class="b2b-subnav-inner">
        <a href="{{ route('b2b.dashboard') }}" class="{{ $active === 'dashboard' ? 'active' : '' }}">Prehľad</a>
        <a href="{{ route('b2b.orders') }}" class="{{ $active === 'orders' ? 'active' : '' }}">Objednávky</a>
        <a href="{{ route('b2b.pro') }}" class="{{ $active === 'pro' ? 'active' : '' }}">Previa Pro</a>
        <a href="{{ route('shop.index') }}" class="{{ $active === 'shop' ? 'active' : '' }}">Produkty</a>
        <a href="{{ route('b2b.profile') }}" class="{{ $active === 'profile' ? 'active' : '' }}">Profil</a>
    </div>
</nav>
