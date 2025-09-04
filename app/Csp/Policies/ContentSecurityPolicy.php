<?php

namespace App\Csp\Policies;

use Spatie\Csp\Directive;
use Spatie\Csp\Policies\Policy;

class ContentSecurityPolicy extends Policy
{
    public function configure(): void
    {
        parent::configure();

        $this->addDirective(Directive::IMG, ["'self'", 'https:', 'data:', 'blob:']);
        $this->addDirective(Directive::FONT, ["'self'", 'https:', 'data:']);
        // $this->addDirective(Directive::MEDIA, ['https:', 'data:', 'blob:']); // optional
    }
}