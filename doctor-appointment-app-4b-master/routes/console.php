<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Programar recordatorios automáticos de WhatsApp diariamente a las 8:00 AM
Schedule::command('appointments:reminders')->dailyAt('08:00');
Schedule::command('reports:daily-appointments')->dailyAt('08:00');
