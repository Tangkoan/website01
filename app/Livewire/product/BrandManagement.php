<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Services\BrandService;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

use App\Models\Brand;
use Illuminate\Support\Facades\Gate;

class BrandManagement extends Component
{
    use WithPagination;
    use WithFileUploads;


    public $itemId;
    
    // Single Form Auto-generated fields
    public $parent_id;
    public $name;
    public $status = true;
    public $image;
    public $images;
    
    // Bulk Form Auto-generated fields
    public $bulkItem_parent_id;
    public $bulkItem_name;
    public $bulkItem_status = true;
    public $bulkItem_image;
    public $bulkItem_images;
    
    public $isModalOpen = false, $searchTerm = '', $perPage = 10;
    public $sortField = 'id', $sortDirection = 'desc';
    
    public $availableColumns = ['parent_id' => 'Parent', 'name' => 'Name', 'status' => 'Status', 'image' => 'Image', 'images' => 'Images'];
    public $selectedColumns = ['parent_id', 'name', 'status', 'image', 'images']; 
    
    public $selectedItems = [], $selectAll = false;
    public $isDeleteModalOpen = false, $deleteId = null;

    public $isBulkEditModalOpen = false;
    public $selectedItemsQueue = []; 
    public $currentBulkIndex = 0;
    public $bulkItemId;

    protected $queryString = ['searchTerm', 'perPage'];

    protected function service() { return app(BrandService::class); }

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
        abort_if(Gate::denies('edit-brand'), 403);
        if (empty($this->selectedItems)) return;
        $this->selectedItemsQueue = array_values($this->selectedItems);
        $this->currentBulkIndex = 0;
        $this->loadBulkItemData($this->currentBulkIndex);
        $this->isBulkEditModalOpen = true;
    }

    private function loadBulkItemData($index) {
        if (!isset($this->selectedItemsQueue[$index])) return;
        $item = Brand::find($this->selectedItemsQueue[$index]);
        if ($item) {
            $this->bulkItemId = $item->id;
            $this->bulkItem_parent_id = $item->parent_id;
            $this->bulkItem_name = $item->name;
            $this->bulkItem_status = (bool) $item->status;
            $this->bulkItem_image = is_string($item->image) ? json_decode($item->image, true) ?? $item->image : $item->image;
            $this->bulkItem_images = is_string($item->images) ? json_decode($item->images, true) ?? $item->images : $item->images;
        }
    }

    public function jumpToBulkItem($index) {
        $this->currentBulkIndex = $index;
        $this->loadBulkItemData($index);
        $this->resetErrorBag();
    }

    public function skipBulkItem() { $this->moveToNextBulkItem(); }

    public function saveAndNextBulkItem() {
        abort_if(Gate::denies('edit-brand'), 403);
        $this->validate([
            'bulkItem_parent_id' => 'nullable',
            'bulkItem_name' => 'required|string|max:255',
            'bulkItem_status' => 'required|boolean',
            'bulkItem_image' => 'nullable',
            'bulkItem_images' => 'nullable',
        ]);
        $this->service()->saveItem([
            'parent_id' => $this->bulkItem_parent_id,
            'name' => $this->bulkItem_name,
            'status' => $this->bulkItem_status,
            'image' => empty($this->bulkItem_image) ? null : (is_string($this->bulkItem_image) ? $this->bulkItem_image : $this->bulkItem_image->store('uploads/{{modelNameLower}}', 'public')),
            'images' => empty($this->bulkItem_images) ? null : collect($this->bulkItem_images)->map(fn($f) => is_string($f) ? $f : $f->store('uploads/{{modelNameLower}}', 'public'))->toJson(),
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
        $this->reset(['selectedItemsQueue', 'currentBulkIndex', 'bulkItemId', 'selectedItems', 'selectAll', 'bulkItem_parent_id', 'bulkItem_name', 'bulkItem_status', 'bulkItem_image', 'bulkItem_images']);
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
        abort_if(Gate::denies('create-brand'), 403);
        $this->reset(['itemId']);
        
        $this->reset([
            'parent_id', 'name', 'status', 'image', 'images'
        ]);

        $this->resetErrorBag();
        $this->isModalOpen = true;
    }

    public function editItem($id) {
        abort_if(Gate::denies('edit-brand'), 403);
        $this->resetErrorBag();
        $item = Brand::findOrFail($id);
        
        $this->itemId = $item->id;
        
        $this->parent_id = $item->parent_id;
        $this->name = $item->name;
        $this->status = (bool) $item->status;
        $this->image = is_string($item->image) ? json_decode($item->image, true) ?? $item->image : $item->image;
        $this->images = is_string($item->images) ? json_decode($item->images, true) ?? $item->images : $item->images;
        
        $this->isModalOpen = true;
    }

    public function toggleStatus($id) {
        if (Gate::denies('edit-brand')) {
            $this->dispatch('notify', type: 'error', message: __('messages.no_permission') ?? 'No permission.');
            return; 
        }
        $item = Brand::findOrFail($id);
        $item->status = !$item->status;
        $item->save();
        $this->dispatch('notify', type: 'success', message: $item->status ? __('messages.activated') ?? 'Activated' : __('messages.deactivated') ?? 'Deactivated');
    }

    public function saveItem() {
        if ($this->itemId) abort_if(Gate::denies('edit-brand'), 403);
        else abort_if(Gate::denies('create-brand'), 403);

        $this->validate([
            'parent_id' => 'nullable',
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
            'image' => 'nullable',
            'images' => 'nullable',
        ]);

        try {
            $this->service()->saveItem([
                'parent_id' => $this->parent_id,
            'name' => $this->name,
            'status' => $this->status,
            'image' => empty($this->image) ? null : (is_string($this->image) ? $this->image : $this->image->store('uploads/{{modelNameLower}}', 'public')),
            'images' => empty($this->images) ? null : collect($this->images)->map(fn($f) => is_string($f) ? $f : $f->store('uploads/{{modelNameLower}}', 'public'))->toJson(),
            ], $this->itemId);

            $this->isModalOpen = false;
            $this->dispatch('notify', type: 'success', message: __('messages.saved_successfully') ?? 'Data saved successfully.');

        } catch (\Illuminate\Database\QueryException $e) {
            // ✅ ចាប់យកកំហុស "Data too long" (1406)
            if ($e->getCode() === '22001' || \Illuminate\Support\Str::contains($e->getMessage(), '1406')) {
                
                // 🔍 វេទមន្តទាញយកឈ្មោះ Column ចេញពី Error Message (ឧទាហរណ៍៖ ...column 'description' at row 1)
                preg_match("/column '([^']+)'/", $e->getMessage(), $matches);
                $columnName = $matches[1] ?? '';

                // ប្តូរឈ្មោះ Column ទៅជា Label ដែលស្រួលមើល (ឧ. description -> Description)
                $fieldLabel = __("messages.$columnName");
                if ($fieldLabel == "messages.$columnName") {
                    $fieldLabel = \Illuminate\Support\Str::headline($columnName);
                }

                // បង្ហាញ Error ចំឈ្មោះ Field នោះតែម្តង
                $this->dispatch('notify', 
                    type: 'error', 
                    message: __('messages.field_data_too_large', ['field' => $fieldLabel]) 
                             ?? "The data in field [$fieldLabel] is too large."
                );
                
                // បន្ថែម Error ក្រហមនៅពីក្រោម Input នោះថែមទៀត
                if ($columnName) {
                    $this->addError($columnName, __('messages.data_too_large'));
                }
                
                return;
            }

            throw $e;
        }
    }

    public function confirmDelete($id = null) {
        abort_if(Gate::denies('delete-brand'), 403);
        $this->deleteId = $id;
        $this->isDeleteModalOpen = true;
    }

    public function executeDelete() {
        abort_if(Gate::denies('delete-brand'), 403);
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
        return view('livewire.product.brand.brand-management', [
            'items' => $this->service()->getItems($this->searchTerm, $this->perPage, $this->sortField, $this->sortDirection),
        ])->title(__('messages.brand_management') ?? 'Brands Management');
    }
}