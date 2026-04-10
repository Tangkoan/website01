<div class="w-full max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8 space-y-6 relative">
    @include('livewire.partials.settings.sidebar.header')
    @include('livewire.partials.settings.sidebar.filters')
    <div class="space-y-4">
        @include('livewire.partials.settings.sidebar.table')
        @include('livewire.partials.settings.sidebar.cards-mobile')
    </div>
    <x-modals.delete 
        :isOpen="$isDeleteModalOpen" 
        onClose="$set('isDeleteModalOpen', false)" 
        onConfirm="executeDelete" 
        message="{!! $deleteId ? '' : __('messages.bulk_delete_warning', ['count' => count($selectedItems)]) !!}"
    />
    @include('livewire.partials.settings.sidebar.modal-form')
    @include('livewire.partials.settings.sidebar.modal-bulk-edit')
</div>