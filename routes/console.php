<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule campaign processing
Schedule::command('campaigns:process-scheduled')->everyMinute();

// Schedule cleanup of old delivery records
Schedule::command('deliveries:cleanup')->daily();
