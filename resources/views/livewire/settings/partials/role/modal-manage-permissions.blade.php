@if($isPermissionModalOpen)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 backdrop-blur-xl bg-black/40 animate-in fade-in duration-300">
        <div class="bg-[var(--color-card-bg)] w-full max-w-4xl rounded-xl shadow-2xl border border-[var(--color-border-color)] overflow-hidden flex flex-col max-h-[90vh]">
            
            {{-- Modal Header --}}
            <div class="p-5 border-b border-[var(--color-border-color)] flex justify-between items-center bg-[var(--color-background)]/50 shrink-0">
                <div>
                    <h3 class="text-xl font-black text-[var(--color-text-main)]">{{ __('messages.manage_permissions') ?? 'Manage Permissions' }}</h3>
                    <p class="text-xs font-bold text-[var(--color-primary)] uppercase tracking-widest mt-1">{{ __('messages.role') ?? 'Role' }}: {{ $managingRoleName }}</p>
                </div>
                <button wire:click="$set('isPermissionModalOpen', false)" class="text-[var(--color-text-muted)] hover:text-red-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            {{-- Modal Body ជាមួយការ Group Permission --}}
            <form wire:submit.prevent="saveRolePermissions" class="flex flex-col overflow-hidden h-full">
                <div class="p-6 overflow-y-auto space-y-6 flex-1 bg-[var(--color-background)]/20">
                    
                    {{-- ផ្នែក Select All / Uncheck All --}}
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 bg-[var(--color-card-bg)] p-4 rounded-xl border border-[var(--color-border-color)] shadow-sm">
                        <div class="text-xs font-black text-[var(--color-text-muted)] uppercase tracking-widest flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                            {{ __('messages.quick_actions') ?? 'Quick Actions' }}
                        </div>
                        <div class="flex gap-2 w-full sm:w-auto">
                            <button type="button" wire:click="selectAllPermissions" class="flex-1 sm:flex-none px-4 py-2 text-[10px] font-black uppercase tracking-widest bg-[var(--color-primary)]/10 text-[var(--color-primary)] rounded-lg hover:bg-[var(--color-primary)] hover:text-white transition-all flex items-center justify-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                {{ __('messages.select_all') ?? 'Select All' }}
                            </button>
                            <button type="button" wire:click="unselectAllPermissions" class="flex-1 sm:flex-none px-4 py-2 text-[10px] font-black uppercase tracking-widest bg-red-500/10 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-all flex items-center justify-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                {{ __('messages.uncheck_all') ?? 'Uncheck All' }}
                            </button>
                        </div>
                    </div>

                    {{-- បញ្ជី Permission តាម Group --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($groupedPermissions as $groupName => $permissions)
                            <div class="bg-[var(--color-card-bg)] border border-[var(--color-border-color)] rounded-xl p-4 shadow-sm">
                                
                                {{-- ឈ្មោះ Group --}}
                                <div class="flex items-center gap-2 mb-4 pb-2 border-b border-[var(--color-border-color)]">
                                    <span class="w-2 h-2 rounded-full bg-[var(--color-primary)]"></span>
                                    <h4 class="font-black text-[var(--color-text-main)] uppercase tracking-widest text-xs">{{ $groupName }}</h4>
                                    <span class="ml-auto text-[10px] font-bold px-2 py-0.5 bg-[var(--color-background)] rounded-md text-[var(--color-text-muted)]">{{ count($permissions) }}</span>
                                </div>
                                
                                {{-- បញ្ជី Permission ក្នុង Group --}}
                                <div class="space-y-2">
                                    @foreach($permissions as $perm)
                                        <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-[var(--color-background)] cursor-pointer transition-colors group">
                                            <input type="checkbox" wire:model="rolePermissionsSelected" value="{{ $perm->name }}" 
                                                class="w-4 h-4 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] checked:bg-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                                            <span class="text-xs font-bold text-[var(--color-text-main)] group-hover:text-[var(--color-primary)] transition-colors">{{ $perm->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="p-4 border-t border-[var(--color-border-color)] bg-[var(--color-card-bg)] flex gap-3 shrink-0">
                    <button type="button" wire:click="$set('isPermissionModalOpen', false)" class="flex-1 h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] font-black rounded-lg uppercase tracking-widest text-[10px] hover:brightness-95 transition-all">{{ __('messages.cancel') ?? 'Cancel' }}</button>
                    <button type="submit" class="flex-[2] h-11 bg-[var(--color-primary)] text-[var(--color-primary-text)] font-black rounded-lg shadow-xl uppercase tracking-widest text-[10px] hover:brightness-110 transition-all flex justify-center items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ __('messages.save_permissions') ?? 'Save Permissions' }}
                    </button>
                </div>
            </form>

        </div>
    </div>
@endif