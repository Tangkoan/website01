<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\StoryService;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

use App\Models\Story;
use Illuminate\Support\Facades\Gate;

class StoryManagement extends Component
{
    use WithPagination;
    use WithFileUploads;


    public $itemId;
    
    // Single Form Auto-generated fields
    public $category_id;
    public $user_id;
    public $title;
    public $slug;
    public $thumbnail;
    public $content;
    public $meta_title;
    public $meta_description;
    public $views_count;
    public $status = true;
    public $published_at;
    
    // Bulk Form Auto-generated fields
    public $bulkItem_category_id;
    public $bulkItem_user_id;
    public $bulkItem_title;
    public $bulkItem_slug;
    public $bulkItem_thumbnail;
    public $bulkItem_content;
    public $bulkItem_meta_title;
    public $bulkItem_meta_description;
    public $bulkItem_views_count;
    public $bulkItem_status = true;
    public $bulkItem_published_at;
    
    public $isModalOpen = false, $searchTerm = '', $perPage = 10;
    public $sortField = 'id', $sortDirection = 'desc';
    
    public $availableColumns = ['category_id' => 'Category', 'user_id' => 'User', 'title' => 'Title', 'slug' => 'Slug', 'thumbnail' => 'Thumbnail', 'content' => 'Content', 'meta_title' => 'Meta Title', 'meta_description' => 'Meta Description', 'views_count' => 'Views Count', 'status' => 'Status', 'published_at' => 'Published At'];
    public $selectedColumns = ['category_id', 'user_id', 'title', 'slug', 'thumbnail', 'content', 'meta_title', 'meta_description', 'views_count', 'status', 'published_at']; 
    
    public $selectedItems = [], $selectAll = false;
    public $isDeleteModalOpen = false, $deleteId = null;

    public $isBulkEditModalOpen = false;
    public $selectedItemsQueue = []; 
    public $currentBulkIndex = 0;
    public $bulkItemId;

    protected $queryString = ['searchTerm', 'perPage'];

    protected function service() { return app(StoryService::class); }

    public function updatedSelectAll($value) {
        if ($value) {
            $this->selectedItems = $this->service()->getItems($this->searchTerm, 'all', $this->sortField, $this->sortDirection)->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    public function sortBy($field) {
        $this->sortDirection = ($this->sortField === $field && $this->sortDirection === 'asc') ? 'desc' : 'asc';
        $this->sortField = $field;
    }

    // --- Bulk Edit ---
    public function bulkEdit() {
        abort_if(Gate::denies('edit-story'), 403);
        if (empty($this->selectedItems)) return;
        $this->selectedItemsQueue = array_values($this->selectedItems);
        $this->currentBulkIndex = 0;
        $this->loadBulkItemData($this->currentBulkIndex);
        $this->isBulkEditModalOpen = true;
    }

    private function loadBulkItemData($index) {
        if (!isset($this->selectedItemsQueue[$index])) return;
        $item = Story::find($this->selectedItemsQueue[$index]);
        if ($item) {
            $this->bulkItemId = $item->id;
            $this->bulkItem_category_id = $item->category_id;
            $this->bulkItem_user_id = $item->user_id;
            $this->bulkItem_title = $item->title;
            $this->bulkItem_slug = $item->slug;
            $this->bulkItem_thumbnail = is_string($item->thumbnail) ? json_decode($item->thumbnail, true) ?? $item->thumbnail : $item->thumbnail;
            $this->bulkItem_content = $item->content;
            $this->bulkItem_meta_title = $item->meta_title;
            $this->bulkItem_meta_description = $item->meta_description;
            $this->bulkItem_views_count = $item->views_count;
            $this->bulkItem_status = (bool) $item->status;
            $this->bulkItem_published_at = $item->published_at;
        }
    }

    public function jumpToBulkItem($index) {
        $this->currentBulkIndex = $index;
        $this->loadBulkItemData($index);
        $this->resetErrorBag();
    }

    public function skipBulkItem() { $this->moveToNextBulkItem(); }

    public function saveAndNextBulkItem() {
        abort_if(Gate::denies('edit-story'), 403);
        $this->validate([
            'bulkItem_category_id' => 'required',
            'bulkItem_user_id' => 'required',
            'bulkItem_title' => 'required|string|max:255',
            'bulkItem_slug' => 'required|string|max:255',
            'bulkItem_thumbnail' => 'nullable',
            'bulkItem_content' => 'nullable|string',
            'bulkItem_meta_title' => 'nullable|string|max:255',
            'bulkItem_meta_description' => 'nullable|string',
            'bulkItem_views_count' => 'required|integer',
            'bulkItem_status' => 'nullable|boolean',
            'bulkItem_published_at' => 'nullable|string|max:255',
        ]);
        $this->service()->saveItem([
            'category_id' => $this->bulkItem_category_id,
            'user_id' => $this->bulkItem_user_id,
            'title' => $this->bulkItem_title,
            'slug' => $this->bulkItem_slug,
            'thumbnail' => empty($this->bulkItem_thumbnail) ? null : (is_string($this->bulkItem_thumbnail) ? $this->bulkItem_thumbnail : $this->bulkItem_thumbnail->store('uploads/stories', 'public')),
            'content' => $this->bulkItem_content,
            'meta_title' => $this->bulkItem_meta_title,
            'meta_description' => $this->bulkItem_meta_description,
            'views_count' => $this->bulkItem_views_count,
            'status' => $this->bulkItem_status,
            'published_at' => $this->bulkItem_published_at,
        ], $this->bulkItemId);
        $this->moveToNextBulkItem();
    }

    private function moveToNextBulkItem() {
        $this->resetErrorBag();
        if ($this->currentBulkIndex < count($this->selectedItemsQueue) - 1) {
            $this->currentBulkIndex++;
            $this->loadBulkItemData($this->currentBulkIndex);
        } else {
            $this->closeBulkEdit();
            $this->dispatch('notify', type: 'success', message: __('messages.bulk_edit_completed') ?? 'Bulk edit completed.');
        }
    }

    public function closeBulkEdit() {
        $this->isBulkEditModalOpen = false;
        $this->reset(['selectedItemsQueue', 'currentBulkIndex', 'bulkItemId', 'selectedItems', 'selectAll', 'bulkItem_category_id', 'bulkItem_user_id', 'bulkItem_title', 'bulkItem_slug', 'bulkItem_thumbnail', 'bulkItem_content', 'bulkItem_meta_title', 'bulkItem_meta_description', 'bulkItem_views_count', 'bulkItem_status', 'bulkItem_published_at']);
        $this->resetErrorBag();
    }

    // --- Image Handling ---
    public function removeFile($field, $index) {
        if (is_array($this->$field) && isset($this->$field[$index])) {
            $files = $this->$field;
            unset($files[$index]);
            $this->$field = array_values($files); 
        }
    }

    // --- Single Actions ---
    public function openModal() {
        abort_if(Gate::denies('create-story'), 403);
        $this->reset(['itemId']);
        
        $this->reset([
            'category_id', 'user_id', 'title', 'slug', 'thumbnail', 'content', 'meta_title', 'meta_description', 'views_count', 'status', 'published_at'
        ]);

        $this->resetErrorBag();
        $this->isModalOpen = true;
    }

    public function editItem($id) {
        abort_if(Gate::denies('edit-story'), 403);
        $this->resetErrorBag();
        $item = Story::findOrFail($id);
        
        $this->itemId = $item->id;
        
        $this->category_id = $item->category_id;
        $this->user_id = $item->user_id;
        $this->title = $item->title;
        $this->slug = $item->slug;
        $this->thumbnail = is_string($item->thumbnail) ? json_decode($item->thumbnail, true) ?? $item->thumbnail : $item->thumbnail;
        $this->content = $item->content;
        $this->meta_title = $item->meta_title;
        $this->meta_description = $item->meta_description;
        $this->views_count = $item->views_count;
        $this->status = (bool) $item->status;
        $this->published_at = $item->published_at;
        
        $this->isModalOpen = true;
    }

    /**
     * ✅ កែប្រែទៅជា toggleField ដើម្បីឱ្យ Smart ជាងមុន
     * អាចបិទបើកបានគ្រប់ Field ដែលជាប្រភេទ Boolean
     */
    public function toggleField($id, $field) {
        if (Gate::denies('edit-story')) {
            $this->dispatch('notify', type: 'error', message: __('messages.no_permission') ?? 'No permission.');
            return; 
        }
        $item = Story::findOrFail($id);
        
        // ប្តូរតម្លៃ (Toggle logic)
        $item->$field = !$item->$field;
        $item->save();
        
        $this->dispatch('notify', 
            type: 'success', 
            message: $item->$field ? (__('messages.activated') ?? 'Activated') : (__('messages.deactivated') ?? 'Deactivated')
        );
    }

    public function saveItem() {
        if ($this->itemId) abort_if(\Illuminate\Support\Facades\Gate::denies('edit-story'), 403);
        else abort_if(\Illuminate\Support\Facades\Gate::denies('create-story'), 403);

        $this->validate([
            'category_id' => 'required',
            'user_id' => 'required',
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'thumbnail' => 'nullable',
            'content' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'views_count' => 'required|integer',
            'status' => 'nullable|boolean',
            'published_at' => 'nullable|string|max:255',
        ]);

        try {
            $this->service()->saveItem([
                'category_id' => $this->category_id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'thumbnail' => empty($this->thumbnail) ? null : (is_string($this->thumbnail) ? $this->thumbnail : $this->thumbnail->store('uploads/stories', 'public')),
            'content' => $this->content,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'views_count' => $this->views_count,
            'status' => $this->status,
            'published_at' => $this->published_at,
            ], $this->itemId);

            $this->isModalOpen = false;
            $this->dispatch('notify', type: 'success', message: __('messages.saved_successfully') ?? 'Data saved successfully.');

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '22001' || \Illuminate\Support\Str::contains($e->getMessage(), '1406')) {
                
                preg_match("/column '([^']+)'/", $e->getMessage(), $matches);
                $columnName = $matches[1] ?? '';

                $fieldLabel = __("messages.$columnName");
                if ($fieldLabel == "messages.$columnName") {
                    $fieldLabel = \Illuminate\Support\Str::headline($columnName);
                }

                $this->dispatch('notify', 
                    type: 'error', 
                    message: __('messages.field_data_too_large', ['field' => $fieldLabel]) 
                             ?? "The data in field [$fieldLabel] is too large."
                );
                
                if ($columnName) {
                    $this->addError($columnName, __('messages.data_too_large'));
                }
                
                return;
            }

            throw $e;
        }
    }

    public function confirmDelete($id = null) {
        abort_if(Gate::denies('delete-story'), 403);
        $this->deleteId = $id;
        $this->isDeleteModalOpen = true;
    }

    public function executeDelete() {
        abort_if(Gate::denies('delete-story'), 403);
        $ids = $this->deleteId ?: $this->selectedItems;
        $this->service()->deleteItems($ids);
        $this->reset(['selectedItems', 'selectAll', 'deleteId', 'isDeleteModalOpen']);
        $this->dispatch('notify', type: 'success', message: __('messages.deleted_successfully') ?? 'Deleted successfully.');
    }

    public function reloadData() {
        $this->reset(['searchTerm', 'selectedItems', 'selectAll']);
        $this->resetPage(); 
        $this->dispatch('notify', type: 'success', message: __('messages.data_reloaded') ?? 'Data reloaded.');
    }

    public function render() {
        return view('livewire.story.story-management', [
            'items' => $this->service()->getItems($this->searchTerm, $this->perPage, $this->sortField, $this->sortDirection),
        ])->title(__('messages.story_management') ?? 'Stories Management');
    }
}