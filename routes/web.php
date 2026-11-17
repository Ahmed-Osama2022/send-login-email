<?php

use App\Http\Controllers\ShowTimes;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
  return view('welcome');
});


Route::get('/times', ShowTimes::class);
