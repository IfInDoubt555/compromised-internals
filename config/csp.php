<?php

use Spatie\Csp\Directive;
use Spatie\Csp\Nonce\RandomString;

$allowUnsafeEval = env('CSP_ALLOW_UNSAFE_EVAL', false); // TEMP switch while migrating to @alpinejs/csp
$osano = ' https://cmp.osano.com';

return [
    'enabled' => env('CSP_ENABLED', true),
    'enabled_while_hot_reloading' => env('CSP_ENABLED_WHILE_HOT_RELOADING', false),

    'nonce_enabled' => env('CSP_NONCE_ENABLED', true),
    'nonce_generator' => RandomString::class,

    'report_uri' => env('CSP_REPORT_URI', ''),

    'presets' => [
        // Keep empty; we define an explicit policy below.
    ],

    'directives' => [
        // Baseline
        [Directive::DEFAULT, "'self'"],
        [Directive::BASE, "'self'"],
        [Directive::FRAME_ANCESTORS, "'self'"],
        [Directive::FORM_ACTION, "'self'"],
        [Directive::OBJECT, "'none'"],

        // Scripts (nonces are added automatically when nonce_enabled=true)
        // Toggle 'unsafe-eval' temporarily via .env while you switch to @alpinejs/csp
        [Directive::SCRIPT, "'self'".($allowUnsafeEval ? " 'unsafe-eval'" : '').$osano],

        // Styles (Tailwind/FullCalendar need inline styles for transitions)
        [Directive::STYLE, "'self' 'unsafe-inline'"],

        // Fonts — served locally via Vite/@fontsource; allow data: as a safe fallback
        [Directive::FONT, "'self' data:"],

        // Images
        [Directive::IMG, "'self' data: blob: https:"],

        // XHR / fetch
        [Directive::CONNECT, "'self'".$osano],

        // Frames (Osano CMP)
        [Directive::FRAME, trim($osano)],

        // Web app manifest (if any)
        [Directive::MANIFEST, "'self'"],
    ],

    'report_only_presets' => [],
    'report_only_directives' => [],
];