<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Services\CategoryService;
use Livewire\WithPagination;

use App\Models\Category;
use Illuminate\Support\Facades\Gate;

class CategoryManagement extends Component
{
    use WithPagination;
    

    public $itemId;
    
    // Single Form Auto-generated fields
    public $name;
    public $description;
    public $status = true;
    
    // Bulk Form Auto-generated fields
    public $bulkItem_name;
    public $bulkItem_description;
    public $bulkItem_status = true;
    
    public $isModalOpen = false, $searchTerm = '', $perPage = 10;
    public $sortField = 'id', $sortDirection = 'desc';
    
    public $availableColumns = ['name' => 'Name', 'description' => 'Description', 'status' => 'Status'];
    public $selectedColumns = ['name', 'description', 'status']; 
    
    public $selectedItems = [], $selectAll = false;
    public $isDeleteModalOpen = false, $deleteId = null;

    public $isBulkEditModalOpen = false;
    public $selectedItemsQueue = []; 
    public $currentBulkIndex = 0;
    public $bulkItemId;

    protected $queryString = ['searchTerm', 'perPage'];

    protected function service() { return app(CategoryService::class); }

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
        abort_if(Gate::denies('edit-category'), 403);
        if (empty($this->selectedItems)) return;
        $this->selectedItemsQueue = array_values($this->selectedItems);
        $this->currentBulkIndex = 0;
        $this->loadBulkItemData($this->currentBulkIndex);
        $this->isBulkEditModalOpen = true;
    }

    private function loadBulkItemData($index) {
        if (!isset($this->selectedItemsQueue[$index])) return;
        $item = Category::find($this->selectedItemsQueue[$index]);
        if ($item) {
            $this->bulkItemId = $item->id;
            $this->bulkItem_name = $item->name;
            $this->bulkItem_description = $item->description;
            $this->bulkItem_status = (bool) $item->status;
        }
    }

    public function jumpToBulkItem($index) {
        $this->currentBulkIndex = $index;
        $this->loadBulkItemData($index);
        $this->resetErrorBag();
    }

    public function skipBulkItem() { $this->moveToNextBulkItem(); }

    public function saveAndNextBulkItem() {
        abort_if(Gate::denies('edit-category'), 403);
        $this->validate([
            'bulkItem_name' => 'required|string|max:255',
            'bulkItem_description' => 'nullable|string',
            'bulkItem_status' => 'required|boolean',
        ]);
        $this->service()->saveItem([
            'name' => $this->bulkItem_name,
            'description' => $this->bulkItem_description,
            'status' => $this->bulkItem_status,
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
        $this->reset(['selectedItemsQueue', 'currentBulkIndex', 'bulkItemId', 'selectedItems', 'selectAll', 'bulkItem_name', 'bulkItem_description', 'bulkItem_status']);
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
        abort_if(Gate::denies('create-category'), 403);
        $this->reset(['itemId']);
        
        $this->reset([
            'name', 'description', 'status'
        ]);

        $this->resetErrorBag();
        $this->isModalOpen = true;
    }

    public function editItem($id) {
        abort_if(Gate::denies('edit-category'), 403);
        $this->resetErrorBag();
        $item = Category::findOrFail($id);
        
        $this->itemId = $item->id;
        
        $this->name = $item->name;
        $this->description = $item->description;
        $this->status = (bool) $item->status;
        
        $this->isModalOpen = true;
    }

    public function toggleStatus($id) {
        if (Gate::denies('edit-category')) {
            $this->dispatch('notify', type: 'error', message: __('messages.no_permission') ?? 'No permission.');
            return; 
        }
        $item = Category::findOrFail($id);
        $item->status = !$item->status;
        $item->save();
        $this->dispatch('notify', type: 'success', message: $item->status ? __('messages.activated') ?? 'Activated' : __('messages.deactivated') ?? 'Deactivated');
    }

    public function saveItem() {
        if ($this->itemId) abort_if(Gate::denies('edit-category'), 403);
        else abort_if(Gate::denies('create-category'), 403);

        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $this->service()->saveItem([
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
        ], $this->itemId);

        $this->isModalOpen = false;
        $this->dispatch('notify', type: 'success', message: __('messages.saved_successfully') ?? 'Data saved successfully.');
    }

    public function confirmDelete($id = null) {
        abort_if(Gate::denies('delete-category'), 403);
        $this->deleteId = $id;
        $this->isDeleteModalOpen = true;
    }

    public function executeDelete() {
        abort_if(Gate::denies('delete-category'), 403);
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
        return view('livewire.product.category.category-management', [
            'items' => $this->service()->getItems($this->searchTerm, $this->perPage, $this->sortField, $this->sortDirection),
        ])->title(__('messages.category_management') ?? 'Categories Management');
    }
}