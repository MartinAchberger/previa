<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Mirror bank-paired invoice payments from SuperFaktúra onto our orders.
Schedule::command('superfaktura:sync-payments')->hourly();
