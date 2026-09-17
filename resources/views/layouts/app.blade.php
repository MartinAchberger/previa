<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'PREVIA - Eshop')</title>
    <meta name="description" content="@yield('description', 'Talianska profesionálna vlasová kozmetika PREVIA. Prírodné ingrediencie, vegan a cruelty-free. Distribúcia pre Slovensko.')">

    <meta property="og:title" content="@yield('title', 'PREVIA')">
    <meta property="og:description" content="@yield('description', 'Talianska profesionálna vlasová kozmetika PREVIA. Distribúcia pre Slovensko.')">
    <meta property="og:type" content="website">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if (filled(config('services.cookiebot.cbid')))
        {{-- Cookiebot must be the first script in <head>; auto blocking mode holds back
             third-party trackers until consent. Own scripts carry data-cookieconsent="ignore". --}}
        <script id="Cookiebot" src="https://consent.cookiebot.com/uc.js" data-cbid="{{ config('services.cookiebot.cbid') }}" data-blockingmode="auto" data-culture="sk" type="text/javascript"></script>
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=Inter:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    <script data-cookieconsent="ignore">document.documentElement.classList.add('reveal-host');</script>
</head>
<body>

    @include('partials.nav', ['active' => $active ?? null])
    {{-- Salon sub-navigation stays visible on every page once a salon is logged in (B2B pages include it themselves). --}}
    @if (auth('b2b')->check() && !request()->routeIs('b2b.*'))
        @include('partials.b2b-nav', ['active' => request()->routeIs('shop.*', 'product.*') ? 'shop' : null])
    @endif

    @yield('content')

    @include('partials.footer')

    @include('partials.cart-drawer')

    <script src="{{ asset('js/cart.js') }}?v={{ filemtime(public_path('js/cart.js')) }}" data-cookieconsent="ignore"></script>
    <script src="{{ asset('js/reveal.js') }}?v={{ filemtime(public_path('js/reveal.js')) }}" data-cookieconsent="ignore" defer></script>
    <script src="{{ asset('js/validate.js') }}?v={{ filemtime(public_path('js/validate.js')) }}" data-cookieconsent="ignore" defer></script>
    @stack('scripts')

</body>
</html>
