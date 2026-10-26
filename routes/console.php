<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Cada día a las 00:05 marca como vencidos los préstamos atrasados
Schedule::command('prestamos:marcar-vencidos')->dailyAt('00:05');