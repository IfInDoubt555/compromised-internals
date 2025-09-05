<?php

namespace App\Csp\Policies;

use Spatie\Csp\Directive;
use Spatie\Csp\Policies\Policy;

class ContentSecurityPolicy extends Policy
{
    public function configure(): void
    {
        $this
            ->addDirective(Directive::IMG,  ["'self'", 'https:', 'data:', 'blob:'])
            ->addDirective(Directive::FONT, ["'self'", 'https:', 'data:']);
    }
}