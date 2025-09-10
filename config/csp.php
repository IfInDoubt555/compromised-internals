<?php

use Spatie\Csp\Directive;
use Spatie\Csp\Nonce\RandomString;

return [
    'enabled' => env('CSP_ENABLED', true),
    'enabled_while_hot_reloading' => env('CSP_ENABLED_WHILE_HOT_RELOADING', false),

    'nonce_enabled' => env('CSP_NONCE_ENABLED', true),
    'nonce_generator' => RandomString::class,

    'report_uri' => env('CSP_REPORT_URI', ''),

    'presets' => [
        // \Spatie\Csp\Presets\Basic::class, // keep minimal; add later if needed
    ],

    'directives' => [
        [Directive::DEFAULT, "'self'"],
        [Directive::BASE, "'self'"],
        [Directive::FRAME_ANCESTORS, "'self'"],
        [Directive::FORM_ACTION, "'self'"],
        [Directive::OBJECT, "'none'"],

        // scripts you actually use
        [Directive::SCRIPT, "'self' https://cmp.osano.com"],

        // styles/fonts for Google Fonts
        [Directive::STYLE, "'self' https://fonts.googleapis.com"],
        [Directive::FONT, "'self' data: https://fonts.gstatic.com"],

        // images
        [Directive::IMG, "'self' https: data: blob:"],

        // network
        [Directive::CONNECT, "'self'"],

        // optional if you serve a manifest
        [Directive::MANIFEST, "'self'"],
    ],

    'report_only_presets' => [],
    'report_only_directives' => [],
];