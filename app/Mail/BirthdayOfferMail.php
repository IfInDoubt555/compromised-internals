<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BirthdayOfferMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $offerCode;

    public function __construct(User $user, string $offerCode = 'BIRTHDAY20')
    {
        $this->user = $user;
        $this->offerCode = $offerCode;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎁 Happy Birthday from Compromised Internals!',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'email.birthday.offer',
            with: [
                'user' => $this->user,
                'offerCode' => $this->offerCode,
            ],
        );
    }

    /**
     * @return list<Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}