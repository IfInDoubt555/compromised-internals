<?php

namespace App\Csp\Policies;

use Spatie\Csp\Directive;
use Spatie\Csp\Policies\Basic;

class ContentSecurityPolicy extends Basic
{
    public function configure(): void
    {
        parent::configure();

        $this->addDirective(Directive::IMG, ["'self'", 'https:', 'data:', 'blob:']);
        $this->addDirective(Directive::FONT, ["'self'", 'https:', 'data:']);
        // $this->addDirective(Directive::MEDIA, ['https:', 'data:', 'blob:']); // optional
    }
}