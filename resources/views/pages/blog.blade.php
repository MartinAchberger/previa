@extends('layouts.app')

@section('title', 'Blog - listy z laboratória · PREVIA')

@section('content')

<section class="j-hero">
    <div>
        <div class="eyebrow">Blog · Edukácia</div>
        <h1>Lorem ipsum<br>dolor <em>sit amet.</em></h1>
    </div>
    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
</section>

@if($featured)
    <section class="j-feat">
        <div class="ph" style="background-image:url('{{ $featured->cover_image_url }}')"></div>
        <div class="body">
            <div class="meta">- Hlavný článok · {{ $featured->category }} · {{ $featured->read_time }} · {{ $featured->published_at?->translatedFormat('F Y') }}</div>
            <h2>{{ $featured->title }}</h2>
            <p>{{ $featured->excerpt }}</p>
            <a href="{{ route('blog.show', $featured->slug) }}" class="btn btn-line" style="align-self:flex-start;text-decoration:none">Čítať článok →</a>
        </div>
    </section>
@endif

@if($articles->isNotEmpty())
<section class="j-list">
    <h3>Ďalšie články <small>{{ now()->format('m / Y') }}</small></h3>
    <div class="blog-grid">
        @foreach($articles as $a)
            <a href="{{ route('blog.show', $a->slug) }}" class="j-card" style="text-decoration:none;color:inherit">
                <div class="ph" style="background-image:url('{{ $a->cover_image_url }}')"></div>
                <div class="meta">{{ $a->meta }}</div>
                <div class="ti">{{ $a->title }}</div>
                <div class="ex">{{ $a->excerpt }}</div>
            </a>
        @endforeach
    </div>
</section>
@endif

<section class="b2b-band">
    <div class="b2b-band-l">
        <div class="eyebrow" style="color:rgba(255,255,255,0.5)">- O značke</div>
        <h2 class="h2">PREVIA<br><em>Vyrobené v Taliansku.</em></h2>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit sed do eiusmod tempor.</p>
    </div>
    <div class="b2b-band-r">
        <div class="b2b-li"><div class="n">01</div><div><div class="t">Made in Italy</div><div class="ds">Vyvíjané a vyrábané kompletne v Taliansku.</div></div></div>
        <div class="b2b-li"><div class="n">02</div><div><div class="t">Pre salóny aj domácu starostlivosť</div><div class="ds">Profesionálne formulácie pre obe použitia.</div></div></div>
        <div class="b2b-li"><div class="n">03</div><div><div class="t">Bez sulfátov, parabénov a silikónov</div><div class="ds">Striktné štandardy pri výbere zložiek.</div></div></div>
        <div class="b2b-li"><div class="n">04</div><div><div class="t">Vegan a netestované na zvieratách</div><div class="ds">Pre celé portfólio.</div></div></div>
    </div>
</section>

@endsection
