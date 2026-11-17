<?php

namespace App\Mail;

use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CurrentTimeMail extends Mailable
{
  use Queueable, SerializesModels;

  /**
   * Create a new message instance.
   *
   * The timestamp is passed in (instead of reading now() inside the view) so
   * the rendered mail always shows the exact moment the job ran.
   */
  public function __construct(
    public CarbonInterface $currentTime,
    public ?string $recipientName = null,
  ) {}

  /**
   * Get the message envelope: subject, sender, reply-to.
   */
  public function envelope(): Envelope
  {
    return new Envelope(
      subject: 'Laptop opened at: ' . $this->currentTime->format('Y-m-d H:i:s T'),
    );
  }

  /**
   * Get the message content definition: which Blade view renders the body.
   */
  public function content(): Content
  {
    return new Content(
      view: 'emails.current-time',
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
