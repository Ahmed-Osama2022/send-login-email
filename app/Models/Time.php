<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Time extends Model
{
  /**
   * Attributes that may be mass assigned.
   *
   * @var list<string>
   */
  protected $fillable = [
    'email',
    'time',
    'timezone',
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'time' => 'datetime',
    ];
  }
}
