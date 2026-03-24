<div class="w-full max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8 space-y-6 relative">
    
    @include('livewire.settings.partials.role.header')
    @include('livewire.settings.partials.role.filters')

    <div class="space-y-4">
        @include('livewire.settings.partials.role.table')
        @include('livewire.settings.partials.role.cards-mobile')
    </div>

    <x-modals.delete 
        :isOpen="$isDeleteModalOpen" 
        onClose="$set('isDeleteModalOpen', false)" 
        onConfirm="executeDelete" 
        message="{!! $deleteId ? '' : __('messages.bulk_delete_warning', ['count' => count($selectedRoles)]) !!}"
    />

    @include('livewire.settings.partials.role.modal-form')
    @include('livewire.settings.partials.role.modal-bulk-edit')
    
    {{-- បន្ថែម Modal ថ្មីនៅទីនេះ --}}
    @include('livewire.settings.partials.role.modal-manage-permissions')

</div>