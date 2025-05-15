<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class BirthdayOfferMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $offerCode;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $offerCode = 'BIRTHDAY20')
    {
        $this->user = $user;
        $this->offerCode = $offerCode;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎁 Happy Birthday from Compromised Internals!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'email.birthday.offer', // <- using Markdown view
            with: [
                'user' => $this->user,
                'offerCode' => $this->offerCode,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}