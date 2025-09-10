<?php

namespace App\Csp\Policies;

use Spatie\Csp\Directive;
use Spatie\Csp\Policies\Basic;

class ContentSecurityPolicy extends Basic
{
    public function configure(): void
    {
        parent::configure();

        // Core defaults
        $this
            ->addDirective(Directive::DEFAULT, ["'self'"])
            ->addDirective(Directive::BASE, ["'self'"])
            ->addDirective(Directive::FRAME_ANCESTORS, ["'self'"]) // SAMEORIGIN
            ->addDirective(Directive::FORM_ACTION, ["'self'"])
            ->addDirective(Directive::OBJECT, ["'none'"]);

        // Scripts: self + Osano CMP; inline scripts are allowed via @cspNonce
        $this->addDirective(Directive::SCRIPT, [
            "'self'",
            'https://cmp.osano.com',
        ]);

        // Styles: self + Google Fonts stylesheet
        $this->addDirective(Directive::STYLE, [
            "'self'",
            'https://fonts.googleapis.com',
        ]);

        // Fonts: self + data: + Google Fonts asset host
        $this->addDirective(Directive::FONT, [
            "'self'",
            'data:',
            'https://fonts.gstatic.com',
        ]);

        // Images (safe and broad)
        $this->addDirective(Directive::IMG, [
            "'self'",
            'https:',
            'data:',
            'blob:',
        ]);

        // XHR/fetch/websocket endpoints (tight by default)
        $this->addDirective(Directive::CONNECT, [
            "'self'",
        ]);

        // Optional if you serve a manifest.json
        $this->addDirective(Directive::MANIFEST, ["'self'"]);

        // If you later add analytics, S3, YouTube, etc., extend the arrays above.
        // Since we use @cspNonce on inline <script>, we do NOT need 'unsafe-inline' for scripts.
    }
}