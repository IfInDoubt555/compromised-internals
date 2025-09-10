<?php

use Spatie\Csp\Directive;
use Spatie\Csp\Nonce\RandomString;

return [
    /*
     | Enable/disable CSP headers entirely.
     */
    'enabled' => env('CSP_ENABLED', true),

    /*
     | When using Vite HMR locally, keep this false to avoid noisy CSP errors.
     */
    'enabled_while_hot_reloading' => env('CSP_ENABLED_WHILE_HOT_RELOADING', false),

    /*
     | Nonce support for inline <script> / <style> with @cspNonce.
     */
    'nonce_enabled' => env('CSP_NONCE_ENABLED', true),
    'nonce_generator' => RandomString::class,

    /*
     | Report-Only mode: keep empty unless you explicitly want report-only.
     | If you want it, move your presets/directives to the *_report_only* arrays.
     */
    'report_uri' => env('CSP_REPORT_URI', ''),

    /*
     | Presets are ready-made allowlists (e.g., fonts, analytics, etc.).
     | Start minimal; add only what you need later.
     */
    'presets' => [
        // \Spatie\Csp\Presets\Basic::class,
        // \Spatie\Csp\Presets\GoogleFonts::class,
        // \Spatie\Csp\Presets\Vimeo::class,
        // \Spatie\Csp\Presets\YouTube::class,
    ],

    /*
     | Fine-grained directives. Start strict and open up as needed.
     | Example shows a minimal self-only policy.
     */
    'directives' => [
        [Directive::DEFAULT, "'self'"],
        [Directive::SCRIPT, "'self'"],
        [Directive::STYLE, "'self'"],
        [Directive::IMG, "'self' data:"],
        [Directive::FONT, "'self' data:"],
        [Directive::CONNECT, "'self'"],
        [Directive::OBJECT, "'none'"],
        [Directive::BASE, "'self'"],
        [Directive::FRAME_ANCESTORS, "'self'"],
        // Add more as your app/services require (e.g. analytics, S3, etc.).
    ],

    /*
     | Report-only variants (leave empty unless testing report-only).
     */
    'report_only_presets' => [
        // \Spatie\Csp\Presets\Basic::class,
    ],

    'report_only_directives' => [
        // e.g. [[Directive::SCRIPT, "'self'"]],
    ],
];