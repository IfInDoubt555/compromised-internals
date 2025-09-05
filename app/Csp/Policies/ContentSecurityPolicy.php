<?php

namespace App\Csp\Policies;

use Spatie\Csp\Directive;
use Spatie\Csp\Policies\Basic as BasePolicy;

class ContentSecurityPolicy extends BasePolicy
{
    public function configure(): void
    {
        parent::configure();

        $this
            ->addDirective(Directive::IMG,  ["'self'", 'https:', 'data:', 'blob:'])
            ->addDirective(Directive::FONT, ["'self'", 'https:', 'data:']);
    }
}