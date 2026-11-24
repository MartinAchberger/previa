<?php

return [
    /*
     * Pay-by-invoice (B2B). By default a salon may always order on invoice,
     * until an admin ticks "Zablokovať platbu na faktúru" on the salon.
     *
     * Set INVOICE_BLOCK_WHEN_UNPAID=true to also block it automatically while
     * the salon has an unpaid invoice order (agreed off for now, 11/2026).
     */
    'invoice_block_when_unpaid' => (bool) env('INVOICE_BLOCK_WHEN_UNPAID', false),
];
