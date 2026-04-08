<?php

namespace App\Services;

use App\Models\Brand;

class BrandService
{
    public function getItems($searchTerm, $perPage, $sortField, $sortDirection)
    {
        $query = Brand::query()
            ->when($searchTerm, function ($query, $searchTerm) {
                return $query->where('name', 'like', '%' . $searchTerm . '%');
            })
            ->orderBy($sortField, $sortDirection);

        return $perPage === 'all' ? $query->paginate($query->count()) : $query->paginate((int)$perPage);
    }

    public function saveItem(array $data, $id = null)
    {
        return Brand::updateOrCreate(['id' => $id], $data);
    }

    public function deleteItems($ids)
    {
        $ids = is_array($ids) ? $ids : [$ids];
        return Brand::whereIn('id', $ids)->get()->each->delete();
    }
}