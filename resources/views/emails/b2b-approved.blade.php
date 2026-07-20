@extends('emails.layout')
@section('title', 'Partnerský účet aktivovaný')
@section('body')
    <p style="margin:0 0 16px;">Dobrý deň {{ $user->contact_name }},</p>
    <p style="margin:0 0 16px;">Vaša registrácia salónu <strong>{{ $user->salon_name }}</strong> bola schválená. Partnerský účet je aktívny, môžete sa prihlásiť a nakupovať s veľkoobchodnými cenami.</p>
    @if ($user->discount_pct > 0)
        <p style="margin:0 0 16px;">Vaša partnerská zľava: <strong>−{{ $user->discount_pct }} %</strong>.</p>
    @endif
    <p style="margin:0 0 16px;"><a href="{{ route('b2b.login') }}" style="color:#12110f;">Prihlásiť sa do partnerského účtu →</a></p>
    <p style="margin:16px 0 0;">S pozdravom,<br>tím PREVIA</p>
@endsection
