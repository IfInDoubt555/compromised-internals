<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeUser extends Mailable
{
    use Queueable, SerializesModels;

    /** @return $this */
    public function build()
    {
        /** @var view-string $view */
        $view = 'emails.welcome';

        return $this->subject('Welcome to Compromised Internals 🚀')
            ->view($view);
    }
}