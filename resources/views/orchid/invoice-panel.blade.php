@php /** @var \App\Models\Order $order */ @endphp
<div class="bg-white rounded shadow-sm p-4">
    <h5 style="font-size:14px;text-transform:uppercase;letter-spacing:0.18em;color:#6b6b66;margin-bottom:18px">Fakturácia</h5>

    @php
        $statusLabels = [
            'none'      => ['Nevystavená', '#6b6b66'],
            'proforma'  => ['Proforma', '#b08a3e'],
            'issued'    => ['Vystavená', '#2f7d3a'],
            'cancelled' => ['Stornovaná', '#9a2a2a'],
            'error'     => ['Chyba', '#9a2a2a'],
        ];
        [$label, $color] = $statusLabels[$order->invoice_status] ?? ['-', '#6b6b66'];
    @endphp

    <div style="display:grid;grid-template-columns:140px 1fr;gap:8px 16px;font-size:14px;line-height:1.5">
        <div style="color:#6b6b66">Stav</div>
        <div><strong style="color:{{ $color }}">{{ $label }}</strong></div>

        @if ($order->proforma_number)
            <div style="color:#6b6b66">Proforma</div>
            <div>
                {{ $order->proforma_number }}
                @if ($order->proforma_pdf_path)
                    · <a href="{{ route('platform.orders.invoice.download', ['order' => $order->id, 'kind' => 'proforma']) }}">PDF</a>
                @endif
            </div>
        @endif

        @if ($order->invoice_number)
            <div style="color:#6b6b66">Faktúra</div>
            <div>
                {{ $order->invoice_number }}
                @if ($order->invoice_pdf_path)
                    · <a href="{{ route('platform.orders.invoice.download', ['order' => $order->id, 'kind' => 'invoice']) }}">PDF</a>
                @endif
            </div>
            <div style="color:#6b6b66">Vystavená</div>
            <div>{{ $order->invoiced_at?->format('j.n.Y · H:i') }}</div>
        @endif

        @if ($order->invoice_sent_at)
            <div style="color:#6b6b66">Email odoslaný</div>
            <div>{{ $order->invoice_sent_at->format('j.n.Y · H:i') }}</div>
        @endif

        @if ($order->invoice_error)
            <div style="color:#6b6b66">Chyba</div>
            <div style="color:#9a2a2a">{{ $order->invoice_error }}</div>
        @endif
    </div>
</div>
