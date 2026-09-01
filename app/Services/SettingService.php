<?php

namespace App\Services;

use App\Models\Setting;

class SettingService
{
    protected function model()
    {
        return \App\Models\Setting::class;
    }

    public function getItems($searchTerm, $perPage, $sortField, $sortDirection)
    {
        $query = $this->model()::query()
            ->when($searchTerm, function ($q) use ($searchTerm) {
                // ✅ កែពី $query មកជា $q វិញ
                return $q->where('name', 'like', '%' . $searchTerm . '%');
            })
            ->orderBy($sortField, $sortDirection);

        // ✅ ដោះស្រាយបញ្ហា $items->links() Error ពេលជ្រើសរើស "All"
        if (strtolower($perPage) === 'all') {
            $total = $query->count();
            // ប្រើ paginate ជំនួស get() ដើម្បីរក្សាទម្រង់ Paginator Object អោយ View ស្គាល់
            return $query->paginate($total > 0 ? $total : 1); 
        }

        return $query->paginate((int) $perPage);
    }

    public function saveItem(array $data, $id = null)
    {
        return Setting::updateOrCreate(['id' => $id], $data);
    }

    public function deleteItems($ids)
    {
        $ids = is_array($ids) ? $ids : [$ids];
        return Setting::whereIn('id', $ids)->get()->each->delete();
    }
}