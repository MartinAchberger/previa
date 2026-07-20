@extends('emails.layout')
@section('title', 'Nová B2B registrácia')
@section('body')
    <p style="margin:0 0 16px;">Nová registrácia salónu čaká na schválenie.</p>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 16px;font-size:13px;">
        <tr><td style="padding:3px 0;color:#8a857b;width:140px;">Salón</td><td style="padding:3px 0;"><strong>{{ $user->salon_name }}</strong></td></tr>
        <tr><td style="padding:3px 0;color:#8a857b;">Kontakt</td><td style="padding:3px 0;">{{ $user->contact_name }}</td></tr>
        <tr><td style="padding:3px 0;color:#8a857b;">E-mail</td><td style="padding:3px 0;">{{ $user->email }}</td></tr>
        <tr><td style="padding:3px 0;color:#8a857b;">Telefón</td><td style="padding:3px 0;">{{ $user->phone ?: '—' }}</td></tr>
        <tr><td style="padding:3px 0;color:#8a857b;">IČO / IČ DPH</td><td style="padding:3px 0;">{{ $user->ico ?: '—' }} / {{ $user->vat_id ?: '—' }}</td></tr>
    </table>
    <p style="margin:0;font-size:13px;"><a href="{{ route('platform.b2b-users') }}" style="color:#12110f;">Otvoriť v administrácii →</a></p>
@endsection
