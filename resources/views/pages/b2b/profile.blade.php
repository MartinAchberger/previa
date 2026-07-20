@extends('layouts.app')

@section('title', 'Profil - ' . $b2b->salon_name)

@section('content')

@include('partials.b2b-nav', ['active' => 'profile'])

<section class="shop-head">
    <div class="crumbs">
        <a href="{{ route('b2b.dashboard') }}" style="color:inherit;text-decoration:none">Salón</a>
        <span class="sep">/</span>
        <span>Profil</span>
    </div>
    <h1>Profil <em>salónu.</em></h1>
    <div class="meta">
        <div class="ds">Aktualizuj kontaktné a fakturačné údaje. Zľava a tier sa upravujú zo strany PREVIA.</div>
    </div>
</section>

@if (session('success'))
    <div style="padding: 16px 56px; background: #ecfdf5; color: #047857; border-bottom: 1px solid var(--line); font-size: 14px;">{{ session('success') }}</div>
@endif
@if ($errors->any())
    <div style="padding: 16px 56px; background: #fdecec; color: #b91c1c; border-bottom: 1px solid var(--line); font-size: 14px;">
        @foreach ($errors->all() as $err)<div>{{ $err }}</div>@endforeach
    </div>
@endif

<form method="POST" action="{{ route('b2b.profile.update') }}" class="profile-form">
    @csrf
    <section class="checkout">
        <div class="ck-l">
            <div class="ck-grp">
                <h3>Kontakt</h3>
                <div class="ck-row">
                    <div class="ck-fi"><label>Kontaktná osoba</label><input type="text" name="contact_name" value="{{ old('contact_name', $b2b->contact_name) }}" required></div>
                    <div class="ck-fi"><label>Salón</label><input type="text" name="salon_name" value="{{ old('salon_name', $b2b->salon_name) }}" required></div>
                </div>
                <div class="ck-row">
                    <div class="ck-fi"><label>Telefón</label><input type="tel" name="phone" value="{{ old('phone', $b2b->phone) }}"></div>
                    <div class="ck-fi"><label>E-mail (read-only)</label><input type="email" value="{{ $b2b->email }}" disabled></div>
                </div>
            </div>

            <div class="ck-grp">
                <h3>Fakturačné</h3>
                <div class="ck-row">
                    <div class="ck-fi"><label>IČO</label><input type="text" name="ico" value="{{ old('ico', $b2b->ico) }}"></div>
                    <div class="ck-fi"><label>IČ DPH</label><input type="text" name="vat_id" value="{{ old('vat_id', $b2b->vat_id) }}"></div>
                </div>
                <div class="ck-row">
                    <div class="ck-fi"><label>Ulica a číslo</label><input type="text" name="address" value="{{ old('address', $b2b->address) }}"></div>
                </div>
                <div class="ck-row">
                    <div class="ck-fi"><label>Mesto</label><input type="text" name="city" value="{{ old('city', $b2b->city) }}"></div>
                    <div class="ck-fi" style="max-width:160px"><label>PSČ</label><input type="text" name="zip" value="{{ old('zip', $b2b->zip) }}"></div>
                </div>
            </div>

            <div class="ck-grp">
                <h3>Zmena hesla (voliteľné)</h3>
                <div class="ck-row">
                    <div class="ck-fi"><label>Nové heslo</label><input type="password" name="password" autocomplete="new-password"></div>
                    <div class="ck-fi"><label>Nové heslo znova</label><input type="password" name="password_confirmation" autocomplete="new-password"></div>
                </div>
            </div>
        </div>

        <aside class="ck-r">
            <h3>Aktuálne nastavenia</h3>
            <div class="oc-block" style="margin-bottom:24px">
                <div style="font-size:11px;letter-spacing:0.18em;text-transform:uppercase;color:var(--mute)">Zľava</div>
                <div style="font-size:32px;font-weight:300;color:var(--ink)">−{{ $b2b->discount_pct }} %</div>
                <div style="font-size:12px;color:var(--mute);margin-top:4px">Aplikuje sa automaticky pri každom produkte</div>
            </div>
            <button type="submit" class="btn">Uložiť zmeny</button>
        </aside>
    </section>
</form>

@endsection
