<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Time;

class ShowTimes extends Controller
{
  /**
   * Handle the incoming request.
   */
  public function __invoke(Request $request)
  {
    $sort = $request->string('sort')->toString();

    $times = match ($sort) {
      'oldest' => Time::orderBy('time', 'asc'),
      'email_asc' => Time::orderBy('email', 'asc'),
      'email_desc' => Time::orderBy('email', 'desc'),
      'timezone_asc' => Time::orderBy('timezone', 'asc'),
      'timezone_desc' => Time::orderBy('timezone', 'desc'),
      default => Time::orderBy('time', 'desc'),
    };

    $times = $times->get();

    return view('show-times', [
      'times' => $times,
      'latest' => Time::latest('time')->first(),
      'sort' => in_array($sort, ['oldest', 'email_asc', 'email_desc', 'timezone_asc', 'timezone_desc'])
        ? $sort
        : 'latest',
    ]);
  }
}
