<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    public function getItems($searchTerm, $perPage, $sortField, $sortDirection)
    {
        $query = Category::query()
            ->where('name', 'like', '%' . $searchTerm . '%')
            ->orderBy($sortField, $sortDirection);

        return $perPage === 'all' ? $query->paginate($query->count()) : $query->paginate((int)$perPage);
    }

    public function saveItem(array $data, $id = null)
    {
        return Category::updateOrCreate(['id' => $id], $data);
    }

    // ✅ ប្រើការលុបបែបធម្មតា ឬ Soft Delete អាស្រ័យលើ Model
    public function deleteItems($ids)
    {
        $ids = is_array($ids) ? $ids : [$ids];
        return Category::whereIn('id', $ids)->get()->each->delete();
    }
}