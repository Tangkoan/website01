<?php
namespace App\Traits;

trait HasTrash {
    public function restore($id, $modelClass) {
        $item = $modelClass::onlyTrashed()->findOrFail($id);
        $item->restore();
        $this->dispatch('notify', type: 'success', message: 'Restored successfully!');
    }

    public function forceDelete($id, $modelClass) {
        $item = $modelClass::onlyTrashed()->findOrFail($id);
        $item->forceDelete();
        $this->dispatch('notify', type: 'success', message: 'Permanently deleted!');
    }
}