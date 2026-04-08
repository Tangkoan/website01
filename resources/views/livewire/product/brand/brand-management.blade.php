<div class="w-full max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8 space-y-6 relative">
    @include('livewire.partials.product.brand.header')
    @include('livewire.partials.product.brand.filters')
    <div class="space-y-4">
        @include('livewire.partials.product.brand.table')
        @include('livewire.partials.product.brand.cards-mobile')
    </div>
    <x-modals.delete 
        :isOpen="$isDeleteModalOpen" 
        onClose="$set('isDeleteModalOpen', false)" 
        onConfirm="executeDelete" 
        message="{!! $deleteId ? '' : __('messages.bulk_delete_warning', ['count' => count($selectedItems)]) !!}"
    />
    @include('livewire.partials.product.brand.modal-form')
    @include('livewire.partials.product.brand.modal-bulk-edit')
</div>