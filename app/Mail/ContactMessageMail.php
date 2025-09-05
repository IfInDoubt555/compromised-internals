<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    /** @var array<string, mixed> */
    public array $data;

    /**
     * Create a new message instance.
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
        $view = 'email.contact';

        return $this->subject('New Contact Message')
            ->view($view)
            ->with($this->data);
    }

    /**
     * Get the attachments for the message.
     * @return list<\Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}