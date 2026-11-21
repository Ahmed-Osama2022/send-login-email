<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class SendCurrentTimeWhatsapp implements ShouldQueue
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
   * $chatId is a bare phone number with country code (e.g. "201234567890").
   */
  public function __construct(
    public string $chatId,
    public ?string $text = null,
  ) {}

  /**
   * Execute the job: post the current time to a WhatsApp chat via WAHA.
   *
   * now() is resolved at run time, not at dispatch time, so a queued job
   * reports the moment the worker actually sent it. WAHA renders WhatsApp
   * markup only (*bold*), not HTML — hence no <strong> in the default text.
   */
  public function handle(): void
  {
    $sentAt = now();

    $text = $this->text
      ?? 'Your laptop opened at: *' . $sentAt->format('l, F j, Y \a\t g:i:s A') . '*';

    // WAHA addresses chats as <number>@c.us; tolerate a full id if one is
    // passed directly (e.g. a group id ending in @g.us).
    $chatId = str_contains($this->chatId, '@')
      ? $this->chatId
      : $this->chatId . '@c.us';

    $response = Http::withHeaders([
      'X-Api-Key' => config('services.waha.api_key'),
    ])->post(rtrim(config('services.waha.url'), '/') . '/api/sendText', [
      'session' => config('services.waha.session'),
      'chatId' => $chatId,
      'text' => $text,
    ]);

    if ($response->failed()) {
      throw new RuntimeException(
        'WAHA sendText failed: ' . $response->status() . ' ' . $response->body()
      );
    }
  }

  /**
   * Seconds to wait before each retry attempt.

   * After 1st failure (before retry 1): waits 10 seconds
   * After 2nd failure (before retry 2): waits 30 seconds
   * After 3rd failure (before retry 3): waits 60 seconds
   *
   * @return array<int, int>
   */
  public function backoff(): array
  {
    return [10, 30, 60];
  }
}
