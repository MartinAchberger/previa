<?php

namespace App\Exceptions;

use RuntimeException;

/** Thrown inside the checkout transaction to roll it back when stock is short. */
class InsufficientStockException extends RuntimeException
{
    /** @param string[] $items human-readable "product (only N ks)" labels */
    public function __construct(public readonly array $items)
    {
        parent::__construct('Insufficient stock: ' . implode(', ', $items));
    }
}
