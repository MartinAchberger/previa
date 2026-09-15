@extends('emails.layout')
@section('title', $subjectLine)
@section('body')
    <p style="margin:0 0 6px;font-size:11px;letter-spacing:0.18em;text-transform:uppercase;color:#8a857b;">Upozornenie pre správcu</p>
    <p style="margin:0 0 16px;font-size:16px;font-weight:bold;">{{ $subjectLine }}</p>
    <p style="margin:0 0 16px;">{{ $intro }}</p>
    @if (!empty($rows))
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 16px;font-size:13px;">
        @foreach ($rows as $label => $value)
            <tr><td style="padding:3px 0;color:#8a857b;width:160px;vertical-align:top;">{{ $label }}</td><td style="padding:3px 0;">{!! nl2br(e($value)) !!}</td></tr>
        @endforeach
    </table>
    @endif
    @if ($url)
        <p style="margin:0;font-size:13px;"><a href="{{ $url }}" style="color:#12110f;">{{ $urlLabel ?: 'Otvoriť v administrácii' }} →</a></p>
    @endif
@endsection
