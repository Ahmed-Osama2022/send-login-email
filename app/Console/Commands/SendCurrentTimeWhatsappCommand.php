<?php

namespace App\Console\Commands;

use App\Jobs\SendCurrentTimeWhatsapp;
use Illuminate\Console\Command;

class SendCurrentTimeWhatsappCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * --sync bypasses the queue and posts during the command run, matching the
   * behaviour of mail:send-current-time.
   */
  protected $signature = 'whatsapp:send-current-time
    {chatId : Phone number with country code, digits only, e.g. 201234567890}
    {--text= : Custom message body; defaults to the current time}
    {--sync : Send immediately instead of pushing the job onto the queue}';

  /**
   * The console command description.
   */
  protected $description = 'Queue (or send) a WhatsApp message containing the current time via WAHA';

  /**
   * Execute the console command.
   */
  public function handle(): int
  {
    // Accept a phone number in any common shape ("+20 100 123 4567",
    // "201001234567") and reduce it to digits. The @c.us suffix that WAHA
    // expects is added by the job, so callers only ever pass the number.
    $chatId = preg_replace('/\D+/', '', (string) $this->argument('chatId'));

    if (! is_string($chatId) || strlen($chatId) < 6 || strlen($chatId) > 20) {
      $this->components->error(
        'Invalid phone number. Pass the number with its country code, e.g. 201234567890.'
      );

      return self::FAILURE;
    }

    if (blank(config('services.waha.url')) || blank(config('services.waha.session'))) {
      $this->components->error('WAHA is not configured. Set WAHA_URL and WAHA_SESSION in .env.');

      return self::FAILURE;
    }

    $text = $this->option('text');

    $job = new SendCurrentTimeWhatsapp(
      $chatId,
      is_string($text) && $text !== '' ? $text : null,
    );

    if ($this->option('sync')) {
      dispatch_sync($job);
      $this->components->info("WhatsApp message sent to {$chatId}.");
    } else {
      dispatch($job);
      $this->components->info("Job queued to send the current time to {$chatId} on WhatsApp.");
    }

    return self::SUCCESS;
  }
}
