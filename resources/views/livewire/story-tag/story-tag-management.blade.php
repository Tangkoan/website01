<div class="w-full max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8 space-y-6 relative">
    @include('livewire.partials.story-tag.header')
    @include('livewire.partials.story-tag.filters')
    <div class="space-y-4">
        @include('livewire.partials.story-tag.table')
        @include('livewire.partials.story-tag.cards-mobile')
    </div>
    <x-modals.delete 
        :isOpen="$isDeleteModalOpen" 
        onClose="$set('isDeleteModalOpen', false)" 
        onConfirm="executeDelete" 
        message="{!! $deleteId ? '' : __('messages.bulk_delete_warning', ['count' => count($selectedItems)]) !!}"
    />
    @include('livewire.partials.story-tag.modal-form')
    @include('livewire.partials.story-tag.modal-bulk-edit')
</div>