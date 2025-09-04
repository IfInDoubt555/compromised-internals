<?php

use App\Csp\Policies\ContentSecurityPolicy;
use Spatie\Csp\Policies\Basic;

return [
    'enabled' => env('CSP_ENABLED', true),
    'report_only' => env('CSP_REPORT_ONLY', false),
    'policy' => ContentSecurityPolicy::class,
    'report_only_policy' => Basic::class,
    'add_nonce_to_inline_scripts' => true,
    'script_hashes' => [],
    'hosts' => [],
    'headers' => ['X-Content-Type-Options' => 'nosniff'],
];