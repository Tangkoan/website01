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
    public $des;
    
    // Bulk Form Auto-generated fields
    public $bulkItem_name;
    public $bulkItem_description;
    public $bulkItem_status = true;
    public $bulkItem_des;
    
    public $isModalOpen = false, $searchTerm = '', $perPage = 10;
    public $sortField = 'id', $sortDirection = 'desc';
    
    public $availableColumns = ['name' => 'Name', 'description' => 'Description', 'status' => 'Status', 'des' => 'Des'];
    public $selectedColumns = ['name', 'description', 'status', 'des']; 
    
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
            $this->bulkItem_des = $item->des;
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
            'bulkItem_des' => 'nullable|string',
        ]);
        $this->service()->saveItem([
            'name' => $this->bulkItem_name,
            'description' => $this->bulkItem_description,
            'status' => $this->bulkItem_status,
            'des' => $this->bulkItem_des,
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
        $this->reset(['selectedItemsQueue', 'currentBulkIndex', 'bulkItemId', 'selectedItems', 'selectAll', 'bulkItem_name', 'bulkItem_description', 'bulkItem_status', 'bulkItem_des']);
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
            'name', 'description', 'status', 'des'
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
        $this->des = $item->des;
        
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
        if ($this->itemId) abort_if(\Illuminate\Support\Facades\Gate::denies('edit-category'), 403);
        else abort_if(\Illuminate\Support\Facades\Gate::denies('create-category'), 403);

        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
            'des' => 'nullable|string',
        ]);

        try {
            $this->service()->saveItem([
                'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
            'des' => $this->des,
            ], $this->itemId);

            $this->isModalOpen = false;
            $this->dispatch('notify', type: 'success', message: __('messages.saved_successfully') ?? 'Data saved successfully.');

        } catch (\Illuminate\Database\QueryException $e) {
            // ✅ ចាប់យកកំហុស "Data too long" (1406) - កូដត្រឹមត្រូវគ្មានសញ្ញា \ នៅពីមុខ $ ឡើយ
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