@extends('layouts.app')

@section('title', 'Prihlásenie pre salóny - PREVIA')

@section('content')

<section class="auth-page">
    <div class="auth-card">
        <div class="auth-head">
            <p class="eyebrow">Prístup pre salóny</p>
            <h1>Prihlásenie</h1>
            <p class="lede" style="margin:0">Zadajte svoje prihlasovacie údaje. Účet musí byť overený zo strany PREVIA.</p>
        </div>

        @if (session('success'))
            <div class="auth-msg auth-msg--ok">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="auth-msg auth-msg--err">
                @foreach ($errors->all() as $err)<div>{{ $err }}</div>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('b2b.login.submit') }}" class="auth-form">
            @csrf
            <div class="ck-fi">
                <label>E-mail</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="ck-fi">
                <label>Heslo</label>
                <input type="password" name="password" required>
            </div>
            <label class="auth-remember">
                <input type="checkbox" name="remember" value="1"> Zapamätať si ma
            </label>
            <button type="submit" class="btn">Prihlásiť sa</button>
        </form>

        <div class="auth-foot">
            Nemáte účet? <a href="{{ route('b2b.register') }}">Registrujte salón</a>
        </div>
    </div>
</section>

@endsection
