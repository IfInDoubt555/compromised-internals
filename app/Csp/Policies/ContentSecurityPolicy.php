<?php

namespace App\Csp\Policies;

use Spatie\Csp\Directive;
use Spatie\Csp\Policy as BasePolicy;

class ContentSecurityPolicy extends BasePolicy
{
    public function configure(BasePolicy $policy): void
    {
        // extend with your own directives
        $policy
            ->add(Directive::IMG,  ["'self'", 'https:', 'data:', 'blob:'])
            ->add(Directive::FONT, ["'self'", 'https:', 'data:']);
    }
}
