<?php

namespace App\Services;

use App\Models\Sidebar;

class SidebarService
{

    protected function model()
    {
        return \App\Models\Sidebar::class;
    }

    public function getItems($searchTerm, $perPage, $sortField, $sortDirection)
    {
        $query = Sidebar::query();
        $query->whereNull('parent_id');

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('url', 'like', '%' . $searchTerm . '%');
            });
        }

        // 🌟 ដំណោះស្រាយថ្មី៖ ទោះយក ALL ក៏នៅតែប្រើ paginate() ដើម្បីកុំឱ្យបាត់ UI
        if ($perPage === 'all') {
            $total = $query->count();
            // បើ total = 0 យក 1 ដើម្បីកុំឱ្យ Error ពេលអត់មានទិន្នន័យ
            return $query->orderBy($sortField, $sortDirection)->paginate($total > 0 ? $total : 1); 
        }
        
        return $query->orderBy($sortField, $sortDirection)->paginate($perPage);
    }

    public function saveItem(array $data, $id = null)
    {
        return Sidebar::updateOrCreate(['id' => $id], $data);
    }

    public function deleteItems($ids)
    {
        $ids = is_array($ids) ? $ids : [$ids];
        return Sidebar::whereIn('id', $ids)->get()->each->delete();
    }
}