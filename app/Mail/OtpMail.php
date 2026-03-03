<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $code;

    /**
     * Create a new message instance.
     */
    public function __construct(string $code)
    {
        $this->code = $code;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address((string)config('mail.from.address'), 'Leave Application System'),
            subject: 'Your OTP Code'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $base = (string) (config('mail.public_base') ?: config('app.url', ''));
        if ($base !== '') {
            $base = preg_replace('#^http://#', 'https://', $base) ?? $base;
            $base = rtrim($base, '/');
        }
        $rel = '/dilgLogo.png';
        if (!file_exists(public_path('dilgLogo.png'))) {
            $rel = '/images/dilg-logo.png';
        }
        $logoUrl = $base !== '' ? ($base.$rel) : 'https://example.com/images/dilg-logo.png';
        return new Content(
            view: 'emails.otp_html',
            with: [
                'code' => $this->code,
                'appName' => 'Leave Application System',
                'logoUrl' => $logoUrl,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
