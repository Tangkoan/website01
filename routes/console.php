<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// រត់ Command នេះជារៀងរាល់ពាក់កណ្តាលអធ្រាត្រ (ម៉ោង ១២ យប់) ជារៀងរាល់ថ្ងៃ
Schedule::command('trash:auto-empty')->daily();