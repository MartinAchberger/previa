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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=Inter:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    <script>document.documentElement.classList.add('reveal-host');</script>
</head>
<body>

    @include('partials.bar')
    @include('partials.nav', ['active' => $active ?? null])

    @yield('content')

    @include('partials.footer')

    @include('partials.cart-drawer')

    <script src="{{ asset('js/cart.js') }}?v={{ filemtime(public_path('js/cart.js')) }}"></script>
    <script src="{{ asset('js/reveal.js') }}?v={{ filemtime(public_path('js/reveal.js')) }}" defer></script>
    @stack('scripts')

</body>
</html>
