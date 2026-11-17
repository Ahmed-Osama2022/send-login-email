<?php

namespace App\Console\Commands;

use App\Jobs\SendCurrentTimeEmail as SendCurrentTimeEmailJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SendCurrentTimeEmail extends Command
{
  /**
   * The name and signature of the console command.
   *
   * --sync bypasses the queue and sends during the command run, which is handy
   * when no queue worker is running.
   */
  protected $signature = 'mail:send-current-time
    {email : Recipient email address}
    {--name= : Optional recipient name used in the greeting}
    {--sync : Send immediately instead of pushing the job onto the queue}';

  /**
   * The console command description.
   */
  protected $description = 'Queue (or send) an email containing the current time';

  /**
   * Execute the console command.
   */
  public function handle(): int
  {
    $email = (string) $this->argument('email');

    // Never dispatch unvalidated input, even from the console.
    try {
      Validator::make(
        ['email' => $email],
        ['email' => ['required', 'string', 'email']],
      )->validate();
    } catch (ValidationException $e) {
      foreach ($e->validator->errors()->all() as $message) {
        $this->components->error($message);
      }

      return self::FAILURE;
    }

    $job = new SendCurrentTimeEmailJob($email, $this->option('name'));

    if ($this->option('sync')) {
      dispatch_sync($job);
      $this->components->info("Email with the current time sent to {$email}.");
    } else {
      dispatch($job);
      $this->components->info("Job queued to send the current time to {$email}.");
    }

    return self::SUCCESS;
  }
}
