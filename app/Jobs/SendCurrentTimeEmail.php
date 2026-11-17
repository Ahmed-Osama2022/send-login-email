<?php

namespace App\Jobs;

use App\Mail\CurrentTimeMail;
use App\Models\Time;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendCurrentTimeEmail implements ShouldQueue
{
  use Queueable;

  /**
   * How many times the job may be attempted before it is marked as failed.
   */
  public int $tries = 3;

  /**
   * Create a new job instance.
   *
   * Only plain, serialisable data lives here (strings), because the job is
   * stored in the queue and rehydrated by a worker in a separate process.
   */
  public function __construct(
    public string $email,
    public ?string $recipientName = null,
  ) {}

  /**
   * Execute the job: persist the timestamp, then send the "current time" mail.
   *
   * now() is resolved here, not at dispatch time, so both the stored row and
   * the delivered email reflect the moment the worker ran. The value is
   * captured once and reused, keeping the record and the email identical.
   */
  public function handle(): void
  {
    $sentAt = now();

    Time::create([
      'email' => $this->email,
      'time' => $sentAt,
      'timezone' => $sentAt->timezoneName,
    ]);

    Mail::to($this->email)
      ->send(new CurrentTimeMail($sentAt, $this->recipientName));
  }

  /**
   * Seconds to wait before each retry attempt.
   *
   * @return array<int, int>
   */
  public function backoff(): array
  {
    return [10, 30, 60];
  }
}
