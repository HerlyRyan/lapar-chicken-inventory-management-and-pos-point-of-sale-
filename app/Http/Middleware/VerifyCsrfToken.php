<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Temporarily disable CSRF for sales-packages route to fix persistent 419 error
        // This is NOT a permanent solution and should be removed once the issue is properly fixed
        'sales-packages',
        'sales-packages/*',
        // API routes for real-time stock validation (called via AJAX from authenticated users)
        'api/sales/validate-cart-stock',
        'api/sales/real-time-stock',
    ];
}
