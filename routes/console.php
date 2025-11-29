<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Agendar limpeza automática de mensagens antigas do chat IA
// Executa todo dia às 3:00 AM para remover mensagens com mais de 30 dias
Schedule::command('chat:clean')->daily()->at('03:00');
