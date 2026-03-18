<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Permission; // ប្រើ Model របស់អ្នក
use Carbon\Carbon;

class AutoEmptyTrash extends Command
{
    // ឈ្មោះ Command សម្រាប់ហៅប្រើ
    protected $signature = 'trash:auto-empty';

    // ការពិពណ៌នា
    protected $description = 'លុបទិន្នន័យក្នុងធុងសំរាមដែលចាស់ជាង ៣០ ថ្ងៃដោយស្វ័យប្រវត្តិ';

    public function handle()
    {
        // កំណត់យកថ្ងៃដែលមុន ៣០ ថ្ងៃគិតចាប់ពីថ្ងៃនេះ
        $daysAgo = Carbon::now()->subDays(30);

        // ទាញយកទិន្នន័យដែលលុបហើយ និងមានអាយុកាលលើសពី ៣០ថ្ងៃ មក Force Delete
        $deletedCount = Permission::onlyTrashed()
            ->where('deleted_at', '<', $daysAgo)
            ->forceDelete();

        $this->info("បានលុបទិន្នន័យចាស់ៗចំនួន {$deletedCount} ចេញពីធុងសំរាមដោយជោគជ័យ។");
    }
}