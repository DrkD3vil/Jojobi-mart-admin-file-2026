<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Carries the RAW 6-digit code -- OtpService only ever stores a hash of it,
 * this is the one place the plaintext code exists outside memory. With
 * MAIL_MAILER=log, the rendered email (code included) lands in
 * storage/logs/laravel.log instead of an inbox.
 */
class CustomerOtpMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $code,
        public readonly string $purpose = 'login',
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->purpose === 'register'
                ? 'Verify your email to finish creating your account'
                : 'Your sign-in verification code',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.customer-otp',
            with: [
                'code' => $this->code,
                'purpose' => $this->purpose,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
