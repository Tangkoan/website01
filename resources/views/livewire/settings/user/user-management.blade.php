<div class="w-full max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8 space-y-6 relative">
    

    @include('livewire.settings.partials.user.header')
    @include('livewire.settings.partials.user.filters')

    <div class="space-y-4">
        @include('livewire.settings.partials.user.table')
        @include('livewire.settings.partials.user.cards-mobile')
    </div>

    {{-- ប្រើ Modal Delete Component ដូច Role --}}
    <x-modals.delete 
        :isOpen="$isDeleteModalOpen" 
        onClose="$set('isDeleteModalOpen', false)" 
        onConfirm="executeDelete" 
        message="{!! $deleteId ? '' : __('messages.bulk_delete_warning', ['count' => count($selectedUsers)]) !!}"
    />

    @include('livewire.settings.partials.user.modal-form')
    @include('livewire.settings.partials.user.modal-bulk-edit')
    

</div>