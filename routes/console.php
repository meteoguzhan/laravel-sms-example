<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\ProcessPendingSmsBatch;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule SMS batch processing every five seconds
Schedule::job(new ProcessPendingSmsBatch(), config('sms.queue', 'default'))
    ->onOneServer()
    ->everyFiveSeconds()
    ->withoutOverlapping();
