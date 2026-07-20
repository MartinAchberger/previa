@extends('layouts.app')

@section('title', $article->title . ' - Blog · PREVIA')
@section('description', $article->excerpt)

@section('content')

<section class="shop-head">
    <div class="crumbs">
        <a href="{{ route('home') }}" style="color:inherit;text-decoration:none">PREVIA</a>
        <span class="sep">/</span>
        <a href="{{ route('blog.index') }}" style="color:inherit;text-decoration:none">Blog</a>
        <span class="sep">/</span>
        <span>{{ $article->title }}</span>
    </div>
    <div class="eyebrow">{{ $article->category }} · {{ $article->read_time }} · {{ $article->published_at?->translatedFormat('j. F Y') }}</div>
    <h1>{{ $article->title }}</h1>
    <div class="meta">
        <div class="ds">{{ $article->excerpt }}</div>
    </div>
</section>

@if($article->cover_image_url)
    <section class="article-cover">
        <div class="ph" style="background-image:url('{{ $article->cover_image_url }}')"></div>
    </section>
@endif

<section class="article-body">
    <div class="article-inner">
        @if($article->body)
            {!! clean($article->body) !!}
        @else
            <p class="lede">{{ $article->excerpt }}</p>
            <p style="color:var(--mute)">Plné znenie článku zatiaľ pripravujeme.</p>
        @endif
    </div>
</section>

@if($related->isNotEmpty())
<section class="blog">
    <div class="section-head">
        <h2 class="h2">Ďalšie články</h2>
        <a href="{{ route('blog.index') }}" class="section-sub" style="text-decoration:none">Všetky →</a>
    </div>
    <div class="blog-grid">
        @foreach($related as $a)
            <a href="{{ route('blog.show', $a->slug) }}" class="j-card" style="text-decoration:none;color:inherit">
                <div class="ph" style="background-image:url('{{ $a->cover_image_url }}')"></div>
                <div class="meta">{{ $a->meta }}</div>
                <div class="ti">{{ $a->title }}</div>
                <div class="ex">{{ \Illuminate\Support\Str::limit($a->excerpt, 120) }}</div>
            </a>
        @endforeach
    </div>
</section>
@endif

@endsection
