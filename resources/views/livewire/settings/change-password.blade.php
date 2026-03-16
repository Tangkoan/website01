<div class="w-full max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="mb-6">
        <h2 class="text-2xl sm:text-3xl font-black text-text-main tracking-tight flex items-center gap-2">
            <span class="text-3xl">🔒</span>
            {{ __('messages.change_password') }}
        </h2>
        <p class="mt-1 text-sm text-text-muted">
            {{ __('messages.change_password_desc') }}
        </p>
    </div>

    <div class="bg-card-bg rounded-2xl shadow-sm border border-border-color overflow-hidden transition-colors duration-300">
        <form wire:submit.prevent="updatePassword" class="p-6 sm:p-8 space-y-6">
            
            <div class="space-y-2" x-data="{ show: false }">
                <label class="block text-sm font-bold text-text-muted">{{ __('messages.current_password') }}</label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'" wire:model="current_password" 
                           class="w-full h-12 bg-background border border-border-color text-text-main rounded-xl pl-4 pr-12 text-sm focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all placeholder-gray-400"
                           placeholder="••••••••">
                    
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none transition-colors">
                        <svg x-show="!show" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <svg x-show="show" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                    </button>
                </div>
                @error('current_password') 
                    <p class="text-red-500 text-xs font-medium flex items-center gap-1 mt-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ $message }}
                    </p> 
                @enderror
            </div>

            <div class="h-px w-full bg-border-color my-2"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2" x-data="{ show: false }">
                    <label class="block text-sm font-bold text-text-muted">{{ __('messages.new_password') }}</label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" wire:model="password" 
                               class="w-full h-12 bg-background border border-border-color text-text-main rounded-xl pl-4 pr-12 text-sm focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all placeholder-gray-400"
                               placeholder="••••••••">
                        
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none transition-colors">
                            <svg x-show="!show" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <svg x-show="show" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                        </button>
                    </div>
                    @error('password') 
                        <p class="text-red-500 text-xs font-medium flex items-center gap-1 mt-1">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ $message }}
                        </p> 
                    @enderror
                </div>

                <div class="space-y-2" x-data="{ show: false }">
                    <label class="block text-sm font-bold text-text-muted">{{ __('messages.confirm_password') }}</label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" wire:model="password_confirmation" 
                               class="w-full h-12 bg-background border border-border-color text-text-main rounded-xl pl-4 pr-12 text-sm focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all placeholder-gray-400"
                               placeholder="••••••••">
                        
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none transition-colors">
                            <svg x-show="!show" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <svg x-show="show" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="pt-6 mt-4 border-t border-border-color flex justify-end gap-3">
                <button type="button" wire:click="$refresh" class="px-6 py-2.5 rounded-xl font-semibold text-sm text-text-muted hover:text-text-main hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-700">
                    {{ __('messages.cancel') }}
                </button>
                <button type="submit" class="relative flex items-center gap-2 px-8 py-2.5 rounded-xl font-bold text-sm text-primary-text bg-primary shadow-sm hover:shadow-md hover:brightness-110 active:scale-[0.98] transition-all focus:outline-none focus:ring-2 focus:ring-primary/50 overflow-hidden group">
                    <div class="absolute inset-0 -translate-x-full group-hover:animate-[shimmer_1.5s_infinite] bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                    <span wire:loading.remove wire:target="updatePassword">{{ __('messages.update_password') }}</span>
                    <span wire:loading wire:target="updatePassword">{{ __('messages.saving') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes shimmer { 100% { transform: translateX(100%); } }
</style>