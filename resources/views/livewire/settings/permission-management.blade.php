<div class="w-full max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8 space-y-6 relative">
    
    {{-- ១. Header (Title & Top Buttons) --}}
    @include('livewire.settings.partials.permission.header')

    {{-- ២. Filters (Search & Bulk Action Bar) --}}
    @include('livewire.settings.partials.permission.filters')

    <div class="space-y-4">
        {{-- ៣. Desktop Table --}}
        @include('livewire.settings.partials.permission.table')

        {{-- ៤. Mobile Cards --}}
        @include('livewire.settings.partials.permission.cards-mobile')
    </div>

    {{-- ៥. Modals --}}
    <x-modals.delete 
        :isOpen="$isDeleteModalOpen" 
        onClose="$set('isDeleteModalOpen', false)" 
        onConfirm="executeDelete" 
        message="{!! $deleteId ? '' : __('messages.bulk_delete_warning', ['count' => '<span class=\'text-red-500 font-black\'>' . count($selectedPermissions) . '</span>']) !!}"
    />

    @include('livewire.settings.partials.permission.modal-form')
    @include('livewire.settings.partials.permission.modal-bulk-edit')

</div>