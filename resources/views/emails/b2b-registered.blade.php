@extends('emails.layout')
@section('title', 'Registrácia prijatá')
@section('body')
    <p style="margin:0 0 16px;">Dobrý deň {{ $user->contact_name }},</p>
    <p style="margin:0 0 16px;">ďakujeme za registráciu salónu <strong>{{ $user->salon_name }}</strong> do partnerského programu PREVIA.</p>
    <p style="margin:0 0 16px;">Vašu registráciu sme prijali a práve ju overujeme. Schválenie prebieha zvyčajne do 3 pracovných dní - o výsledku schvaľovacieho procesu Vás budeme informovať prostredníctvom e-mailu.</p>
    <p style="margin:16px 0 0;">S pozdravom,<br>tím PREVIA</p>
@endsection
