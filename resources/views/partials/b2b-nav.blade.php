@php($active ??= null)
<nav class="b2b-subnav">
    <div class="b2b-subnav-inner">
        <a href="{{ route('b2b.dashboard') }}" class="{{ $active === 'dashboard' ? 'active' : '' }}">Prehľad</a>
        <a href="{{ route('b2b.orders') }}" class="{{ $active === 'orders' ? 'active' : '' }}">Objednávky</a>
        <a href="{{ route('b2b.colors') }}" class="{{ $active === 'colors' ? 'active' : '' }}">Farby</a>
        <a href="{{ route('shop.index') }}">Katalóg</a>
        <a href="{{ route('b2b.profile') }}" class="{{ $active === 'profile' ? 'active' : '' }}">Profil</a>
    </div>
</nav>
