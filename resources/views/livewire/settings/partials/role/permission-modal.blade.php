@if($isPermissionModalOpen)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 backdrop-blur-sm bg-black/40 animate-in fade-in duration-300">
        <div class="bg-[var(--color-card-bg)] w-full max-w-4xl rounded-xl shadow-2xl border border-[var(--color-border-color)] overflow-hidden flex flex-col max-h-[90vh]" 
             x-data="{ activeTab: 'direct' }"> 
             
            {{-- Header --}}
            <div class="p-5 border-b border-[var(--color-border-color)] flex justify-between items-center bg-[var(--color-background)]/50">
                <div>
                    <h3 class="text-xl font-black text-[var(--color-text-main)]">{{ __('messages.manage_permissions') ?? 'Manage Permissions' }}</h3>
                    <p class="text-xs font-bold text-[var(--color-text-muted)] mt-1">Role: <span class="text-[var(--color-primary)] uppercase tracking-widest">{{ $managingRoleName }}</span></p>
                </div>
                <button wire:click="$set('isPermissionModalOpen', false)" class="text-[var(--color-text-muted)] hover:text-red-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            {{-- Tabs Navigation --}}
            <div class="flex border-b border-[var(--color-border-color)] bg-[var(--color-card-bg)] px-5 pt-3 gap-6 overflow-x-auto no-scrollbar">
                <button @click="activeTab = 'direct'" 
                        :class="{'border-[var(--color-primary)] text-[var(--color-primary)]': activeTab === 'direct', 'border-transparent text-[var(--color-text-muted)] hover:text-[var(--color-text-main)]': activeTab !== 'direct'}" 
                        class="pb-3 border-b-2 font-black text-sm uppercase tracking-widest transition-all whitespace-nowrap">
                    1. Role Permissions
                </button>
                
                <button @click="activeTab = 'assignable'" 
                        :class="{'border-amber-500 text-amber-500': activeTab === 'assignable', 'border-transparent text-[var(--color-text-muted)] hover:text-[var(--color-text-main)]': activeTab !== 'assignable'}" 
                        class="pb-3 border-b-2 font-black text-sm uppercase tracking-widest transition-all flex items-center gap-2 whitespace-nowrap">
                    2. Assignable Rules
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                </button>
            </div>

            {{-- Content Area --}}
            <div class="p-6 overflow-y-auto flex-1 bg-[var(--color-background)]/30">
                
                {{-- TAB 1: Direct Permissions --}}
                <div x-show="activeTab === 'direct'" class="space-y-6">
                    <div class="mb-4 p-3 bg-[var(--color-primary)]/10 text-[var(--color-primary)] rounded-lg text-xs font-bold border border-[var(--color-primary)]/20">
                        កំណត់សិទ្ធិសម្រាប់ឱ្យ Role នេះអាចមានសិទ្ធិមើលឃើញ ឬធ្វើសកម្មភាពអ្វីខ្លះនៅក្នុងប្រព័ន្ធ។
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($groupedPermissions as $group => $permissions)
                            <div class="bg-[var(--color-card-bg)] border border-[var(--color-border-color)] rounded-xl p-4 shadow-sm">
                                <h4 class="font-black text-[var(--color-text-main)] border-b border-[var(--color-border-color)] pb-2 mb-3 uppercase text-xs tracking-widest flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-[var(--color-primary)]"></span> {{ $group }}
                                </h4>
                                <div class="space-y-2.5">
                                    @foreach($permissions as $permission)
                                        <label class="flex items-start gap-3 cursor-pointer group">
                                            <input type="checkbox" wire:model.live="rolePermissionsSelected" value="{{ $permission->name }}" 
                                                class="mt-0.5 w-4 h-4 rounded border-2 border-[var(--color-border-color)] text-[var(--color-primary)] bg-[var(--color-background)] checked:bg-[var(--color-primary)] checked:border-[var(--color-primary)] focus:ring-[var(--color-primary)] transition-all cursor-pointer">
                                            <span class="text-sm font-bold text-[var(--color-text-muted)] group-hover:text-[var(--color-text-main)] transition-colors select-none">
                                                {{ $permission->name }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- TAB 2: Assignable Permissions (មុខងារថ្មី) --}}
                <div x-show="activeTab === 'assignable'" style="display: none;" class="space-y-6">
                    <div class="mb-4 p-3 bg-amber-500/10 text-amber-500 rounded-lg text-xs font-bold border border-amber-500/20">
                        កំណត់សិទ្ធិដែល Role នេះអាចយកទៅបំពាក់ (Assign) បន្តឱ្យ User ឬ Role ផ្សេងទៀតបាន។
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($groupedPermissions as $group => $permissions)
                            <div class="bg-[var(--color-card-bg)] border border-[var(--color-border-color)] rounded-xl p-4 shadow-sm">
                                <h4 class="font-black text-[var(--color-text-main)] border-b border-[var(--color-border-color)] pb-2 mb-3 uppercase text-xs tracking-widest flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span> {{ $group }}
                                </h4>
                                <div class="space-y-2.5">
                                    @foreach($permissions as $permission)
                                        <label class="flex items-start gap-3 cursor-pointer group">
                                            <input type="checkbox" wire:model.live="roleAssignablePermissionsSelected" value="{{ $permission->name }}" 
                                                class="mt-0.5 w-4 h-4 rounded border-2 border-[var(--color-border-color)] text-amber-500 bg-[var(--color-background)] checked:bg-amber-500 checked:border-amber-500 focus:ring-amber-500 transition-all cursor-pointer">
                                            <span class="text-sm font-bold text-[var(--color-text-muted)] group-hover:text-[var(--color-text-main)] transition-colors select-none">
                                                {{ $permission->name }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            {{-- Footer & Actions --}}
            <div class="p-5 border-t border-[var(--color-border-color)] flex flex-col sm:flex-row items-center justify-between bg-[var(--color-background)] gap-4">
                {{-- Dynamic Select All Buttons based on Active Tab --}}
                <div class="flex gap-2 w-full sm:w-auto">
                    <button x-show="activeTab === 'direct'" wire:click="selectAllDirectPermissions" class="flex-1 sm:flex-none px-4 py-2 text-[10px] font-black uppercase tracking-widest bg-[var(--color-primary)]/10 text-[var(--color-primary)] rounded-lg hover:bg-[var(--color-primary)] hover:text-white transition-all">{{ __('messages.select_all') ?? 'Select All' }}</button>
                    <button x-show="activeTab === 'direct'" wire:click="unselectAllDirectPermissions" class="flex-1 sm:flex-none px-4 py-2 text-[10px] font-black uppercase tracking-widest bg-[var(--color-card-bg)] border border-[var(--color-border-color)] text-[var(--color-text-muted)] rounded-lg hover:text-red-500 transition-all">{{ __('messages.uncheck_all') ?? 'Uncheck All' }}</button>

                    <button x-show="activeTab === 'assignable'" wire:click="selectAllAssignablePermissions" style="display: none;" class="flex-1 sm:flex-none px-4 py-2 text-[10px] font-black uppercase tracking-widest bg-amber-500/10 text-amber-500 rounded-lg hover:bg-amber-500 hover:text-white transition-all">{{ __('messages.select_all') ?? 'Select All' }}</button>
                    <button x-show="activeTab === 'assignable'" wire:click="unselectAllAssignablePermissions" style="display: none;" class="flex-1 sm:flex-none px-4 py-2 text-[10px] font-black uppercase tracking-widest bg-[var(--color-card-bg)] border border-[var(--color-border-color)] text-[var(--color-text-muted)] rounded-lg hover:text-red-500 transition-all">{{ __('messages.uncheck_all') ?? 'Uncheck All' }}</button>
                </div>

                <div class="flex gap-3 w-full sm:w-auto">
                    <button wire:click="$set('isPermissionModalOpen', false)" class="flex-1 sm:flex-none px-6 py-2.5 bg-[var(--color-card-bg)] border border-[var(--color-border-color)] text-[var(--color-text-main)] font-black rounded-lg uppercase tracking-widest text-xs hover:brightness-95 transition-all">{{ __('messages.cancel') ?? 'Cancel' }}</button>
                    <button wire:click="saveRolePermissions" class="flex-[2] sm:flex-none px-6 py-2.5 bg-[var(--color-primary)] text-[var(--color-primary-text)] font-black rounded-lg shadow-xl uppercase tracking-widest text-xs hover:brightness-110 active:scale-95 transition-all">{{ __('messages.save_permissions') ?? 'Save Permissions' }}</button>
                </div>
            </div>
        </div>
    </div>
@endif