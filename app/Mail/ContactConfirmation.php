<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    /** @var array<string, mixed> */
    public array $data;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /** @return $this */
    public function build()
    {
        /** @var view-string $view */
        $view = 'email.confirmation';

        return $this->subject('Thanks for Contacting Compromised Internals!')
            ->view($view)
            ->with($this->data);
    }
}