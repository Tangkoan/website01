<?php

namespace App\Services;

use App\Models\StoryTag;

class StoryTagService
{

    protected function model()
    {
        return \App\Models\StoryTag::class;
    }

    public function getItems($searchTerm, $perPage, $sortField, $sortDirection)
    {
        $query = $this->model()::query()->with(['story', 'tag'])
            ->when($searchTerm, function ($q) use ($searchTerm) {
                return $query->where('name', 'like', '%' . $searchTerm . '%');
            })
            ->orderBy($sortField, $sortDirection);

        if (strtolower($perPage) === 'all') {
            return $query->get();
        }

        return $query->paginate((int) $perPage);
    }

    public function saveItem(array $data, $id = null)
    {
        return StoryTag::updateOrCreate(['id' => $id], $data);
    }

    public function deleteItems($ids)
    {
        $ids = is_array($ids) ? $ids : [$ids];
        return StoryTag::whereIn('id', $ids)->get()->each->delete();
    }
}