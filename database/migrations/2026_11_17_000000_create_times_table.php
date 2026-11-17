<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('times', function (Blueprint $table) {
      $table->id();

      // Recipient the email was prepared for, useful for tracing an audit row.
      $table->string('email')->index();

      // The moment captured before sending. Stored without offset, hence the
      // separate timezone column below.
      $table->timestamp('time');

      // Zone name (e.g. "UTC") so the stored value can be interpreted again.
      $table->string('timezone', 64);

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('times');
  }
};
