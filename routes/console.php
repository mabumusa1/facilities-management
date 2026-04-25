<?php

use App\Console\Commands\ExpireLeaseQuotes;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Expire lease quotes whose valid_until has passed — runs once per day.
Schedule::command(ExpireLeaseQuotes::class)->daily();
