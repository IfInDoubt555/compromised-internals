<?php

use App\Csp\Policies\ContentSecurityPolicy;

return [
    'enabled' => env('CSP_ENABLED', true),
    'report_only' => env('CSP_REPORT_ONLY', false),

    'policy' => ContentSecurityPolicy::class,
    // Use same policy for report-only, or set to null if not needed
    'report_only_policy' => ContentSecurityPolicy::class,

    'add_nonce_to_inline_scripts' => true,
    'script_hashes' => [],
    'hosts' => [],
    'headers' => [
        'X-Content-Type-Options' => 'nosniff',
    ],
];