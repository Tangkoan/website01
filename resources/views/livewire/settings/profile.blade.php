<div class="w-full max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="mb-6">
        <h2 class="text-2xl sm:text-3xl font-black text-text-main tracking-tight flex items-center gap-2">
            <span class="text-3xl">👤</span>
            {{ __('messages.profile') }}
        </h2>
       
    </div>

    <div class="bg-card-bg rounded-2xl shadow-sm border border-border-color overflow-hidden">
        <form wire:submit.prevent="updateProfile" class="p-6 sm:p-8 space-y-6">
            
            <div class="flex items-center gap-6 pb-6 border-b border-border-color">
                <div class="relative w-24 h-24 rounded-full shadow-md bg-gray-100 dark:bg-gray-800 flex items-center justify-center border-4 border-white dark:border-gray-700 overflow-hidden group">
                    
                    @if ($photo)
                        <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover">
                    @elseif ($existing_photo)
                        <img src="{{ Storage::url($existing_photo) }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-3xl font-black text-primary uppercase">{{ substr($name, 0, 1) }}</span>
                    @endif
                    
                    <label class="absolute inset-0 bg-black/40 hidden group-hover:flex flex-col items-center justify-center cursor-pointer text-white text-xs font-bold transition-all">
                        <svg class="w-6 h-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        {{ __('messages.upload') }}
                        <input type="file" wire:model.live="photo" accept="image/*" class="hidden">
                    </label>
                </div>
                
                <div class="flex-1">
                    <h3 class="font-bold text-text-main">{{ __('messages.profile_photo') }}</h3>
                    <p class="text-sm text-text-muted mt-1">{{ __('messages.profile_photo_desc') }}</p>
                    
                    <div wire:loading wire:target="photo" class="text-sm text-primary font-bold mt-2 flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        {{ __('messages.uploading') }}
                    </div>
                    @error('photo') <span class="text-red-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-bold text-text-muted">{{ __('messages.name') }}</label>
                <input type="text" wire:model="name" class="w-full h-12 bg-background border border-border-color text-text-main rounded-xl px-4 text-sm focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-bold text-text-muted">{{ __('messages.email') }}</label>
                <input type="email" wire:model="email" class="w-full h-12 bg-background border border-border-color text-text-main rounded-xl px-4 text-sm focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="pt-4 border-t border-border-color flex justify-end">
                <button type="submit" class="relative flex items-center gap-2 px-8 py-2.5 rounded-xl font-bold text-sm text-primary-text bg-primary shadow-sm hover:shadow-md hover:brightness-110 active:scale-[0.98] transition-all focus:outline-none focus:ring-2 focus:ring-primary/50">
                    <span wire:loading.remove wire:target="updateProfile">{{ __('messages.save') }}</span>
                    <span wire:loading wire:target="updateProfile">{{ __('messages.saving') }}</span>
                </button>
            </div>
            
        </form>
    </div>
</div>