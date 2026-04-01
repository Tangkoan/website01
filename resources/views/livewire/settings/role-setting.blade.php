<div class="w-full max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8 space-y-6 relative">
    
    {{-- Header Section --}}
    <div class="flex justify-between items-start sm:items-center gap-3 md:gap-4">
        <h2 class="flex-1 min-w-0 text-xl md:text-3xl font-black text-[var(--color-text-main)] tracking-tight flex items-center gap-2.5 md:gap-3">
            <span class="shrink-0 p-2 md:p-2.5 bg-[var(--color-primary)]/10 rounded-lg md:rounded-xl text-[var(--color-primary)] text-lg md:text-2xl flex items-center justify-center">
                🎛️
            </span>
            <span class="leading-tight truncate">{{ __('messages.role_ui_mode') ?? 'Role UI Mode' }}</span>
        </h2>
        
        {{-- Back Button --}}
        {{-- ចំណាំ: សូមប្រាកដថា route('settings.roles') ត្រឹមត្រូវតាមប្រព័ន្ធរបស់អ្នក បើមិនអញ្ចឹងទេអាចប្តូរដាក់ URL ផ្ទាល់ '/settings/roles' ក៏បាន --}}
        <a href="{{ route('settings.roles') }}" wire:navigate class="shrink-0 p-2 sm:px-4 sm:py-2.5 bg-[var(--color-card-bg)] border border-[var(--color-border-color)] rounded-lg shadow-sm text-sm font-black text-[var(--color-text-main)] hover:brightness-95 flex items-center gap-2 transition-all focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]/30 mt-0.5 sm:mt-0">
            <svg class="w-5 h-5 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span class="hidden sm:block">{{ __('messages.back') ?? 'Back' }}</span>
        </a>
    </div>

    {{-- Content Section --}}
    <div class="bg-[var(--color-card-bg)] rounded-xl border border-[var(--color-border-color)] shadow-sm max-w-3xl overflow-hidden">
        
        <div class="p-6 md:p-8">
            <div class="mb-8 border-b border-[var(--color-border-color)] pb-6">
                <h3 class="text-lg font-black text-[var(--color-text-main)]">{{ __('messages.button_display_options') ?? 'Button Display Options' }}</h3>
                <p class="text-sm font-medium text-[var(--color-text-muted)] mt-2 leading-relaxed">
                    {{ __('messages.role_ui_mode_desc') ?? 'Configure how the system should react and display action buttons when a user lacks permission to use them. This setting takes effect globally.' }}
                </p>
            </div>

            <div class="flex flex-col gap-4">
                {{-- ជម្រើសទី ១: Hide --}}
                <label class="flex items-start gap-4 p-5 border-2 rounded-xl cursor-pointer transition-all {{ $roleUiMode === 'hide' ? 'border-[var(--color-primary)] bg-[var(--color-primary)]/5' : 'border-[var(--color-border-color)] hover:bg-[var(--color-background)]/80' }}">
                    <div class="flex items-center h-5 mt-0.5 shrink-0">
                        <input type="radio" wire:model="roleUiMode" value="hide" class="w-5 h-5 text-[var(--color-primary)] bg-transparent border-2 border-[var(--color-text-muted)] checked:border-[var(--color-primary)] focus:ring-0 focus:ring-offset-0 cursor-pointer transition-all">
                    </div>
                    <div class="flex flex-col">
                        <span class="text-base font-black text-[var(--color-text-main)] flex items-center gap-2">
                            {{ __('messages.hide_completely') ?? 'Hide Completely (Hide)' }}
                            <span class="px-2 py-0.5 rounded-md bg-green-500/10 text-green-600 dark:text-green-400 text-[10px] uppercase tracking-wider">{{ __('messages.recommended') ?? 'Recommended' }}</span>
                        </span>
                        <span class="text-xs sm:text-sm font-medium text-[var(--color-text-muted)] mt-1.5 leading-relaxed">
                            {{ __('messages.hide_desc') ?? 'Buttons will not be displayed on the screen at all. This keeps the interface clean and provides high security by not exposing unauthorized features.' }}
                        </span>
                    </div>
                </label>

                {{-- ជម្រើសទី ២: Disable --}}
                <label class="flex items-start gap-4 p-5 border-2 rounded-xl cursor-pointer transition-all {{ $roleUiMode === 'disable' ? 'border-[var(--color-primary)] bg-[var(--color-primary)]/5' : 'border-[var(--color-border-color)] hover:bg-[var(--color-background)]/80' }}">
                    <div class="flex items-center h-5 mt-0.5 shrink-0">
                        <input type="radio" wire:model="roleUiMode" value="disable" class="w-5 h-5 text-[var(--color-primary)] bg-transparent border-2 border-[var(--color-text-muted)] checked:border-[var(--color-primary)] focus:ring-0 focus:ring-offset-0 cursor-pointer transition-all">
                    </div>
                    <div class="flex flex-col">
                        <span class="text-base font-black text-[var(--color-text-main)]">
                            {{ __('messages.show_disabled') ?? 'Show but Disabled (Disable)' }}
                        </span>
                        <span class="text-xs sm:text-sm font-medium text-[var(--color-text-muted)] mt-1.5 leading-relaxed">
                            {{ __('messages.disable_desc') ?? 'Buttons remain visible on the screen so users know the feature exists, but they are grayed out and unclickable.' }}
                        </span>
                    </div>
                </label>
            </div>
        </div>

        {{-- Footer / Action Area --}}
        <div class="px-6 md:px-8 py-5 bg-[var(--color-background)]/50 border-t border-[var(--color-border-color)] flex justify-end">
            <button wire:click="saveSettings" class="px-6 py-2.5 md:py-3 bg-[var(--color-primary)] text-[var(--color-primary-text)] font-black text-sm rounded-lg hover:brightness-110 active:scale-95 transition-all flex items-center gap-2 shadow-sm focus:outline-none focus:ring-4 focus:ring-[var(--color-primary)]/30">
                <svg wire:loading wire:target="saveSettings" class="animate-spin -ml-1 mr-2 h-5 w-5 text-current" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span>{{ __('messages.save_changes') ?? 'Save Changes' }}</span>
            </button>
        </div>

    </div>

</div>