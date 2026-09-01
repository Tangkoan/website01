<?php

namespace App\Services;

use App\Models\StoryTag;
use Illuminate\Database\Eloquent\Builder;

class StoryTagService
{
    /**
     * កំណត់ Field ដែលអ្នកចង់ឲ្យអាច Search បាន (អ្នកអាចប្តូរវាបានតាម Service នីមួយៗ)
     */
    protected array $searchableFields = ['story_id', 'tag_id'];

    protected function model()
    {
        return \App\Models\StoryTag::class;
    }

    public function getItems($searchTerm, $perPage, $sortField, $sortDirection)
    {
        $modelInstance = app($this->model());
        $primaryKey = $modelInstance->getKeyName();

        // ✅ ការពារ Error: បើ Sort តាម 'id' តែ Table នោះប្រើ Primary Key ឈ្មោះផ្សេង (ឧ. Pivot Table)
        if ($sortField === 'id' && $primaryKey !== 'id') {
            $sortField = $primaryKey;
        }

        $query = $this->model()::query()->with(['story', 'tag'])
            ->when($searchTerm, function (Builder $q) use ($searchTerm) {
                // ✅ Smart Search: វានឹង Search គ្រប់ Field ដែលបានកំណត់ក្នុង $searchableFields
                $q->where(function ($subQuery) use ($searchTerm) {
                    foreach ($this->searchableFields as $field) {
                        $subQuery->orWhere($field, 'like', '%' . $searchTerm . '%');
                    }
                });
            })
            ->orderBy($sortField, $sortDirection);

        // ✅ ដោះស្រាយបញ្ហា $items->links() Error ពេលជ្រើសរើស "All"
        if (strtolower($perPage) === 'all') {
            $total = $query->count();
            return $query->paginate($total > 0 ? $total : 1); 
        }

        return $query->paginate((int) $perPage);
    }

    public function saveItem(array $data, $id = null)
    {
        // ✅ Smart Primary Key: ទាញ Key ពី Model ដោយស្វ័យប្រវត្តិ មិនចាំបាច់ Hardcode 'id'
        $primaryKey = app($this->model())->getKeyName();
        return StoryTag::updateOrCreate([$primaryKey => $id], $data);
    }

    public function deleteItems($ids)
    {
        $primaryKey = app($this->model())->getKeyName();
        $ids = is_array($ids) ? $ids : [$ids];
        
        // ✅ ប្រើ $primaryKey ជំនួស 'id'
        return StoryTag::whereIn($primaryKey, $ids)->get()->each->delete();
    }
}