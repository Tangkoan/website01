{{-- modal-assignable-permission.blade.php --}}

@if($isAssignableModalOpen)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 backdrop-blur-sm bg-black/40 animate-in fade-in duration-300">
        <div class="bg-white dark:bg-[var(--color-card-bg)] w-full max-w-4xl rounded-xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
            
            {{-- Header (ដូចក្នុងរូបភាពរបស់អ្នក) --}}
            <div class="p-5 flex justify-between items-start">
                <div>
                    <h3 class="text-xl font-black text-[var(--color-text-main)]">គ្រប់គ្រងសិទ្ធិសម្រាប់ចែកចាយបន្ត</h3>
                    <p class="text-xs font-bold text-[var(--color-primary)] uppercase tracking-widest mt-1">តួនាទី: {{ $managingRoleName }}</p>
                </div>
                <button wire:click="$set('isAssignableModalOpen', false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto flex-1 bg-gray-50/50 dark:bg-[var(--color-background)]/30">
                
                {{-- Quick Actions Row (ដូចក្នុងរូបភាព) --}}
                <div class="flex items-center justify-between bg-white dark:bg-[var(--color-card-bg)] p-3 rounded-xl border border-gray-100 dark:border-[var(--color-border-color)] mb-6 shadow-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                        <span class="text-sm font-bold text-gray-600 dark:text-[var(--color-text-muted)]">សកម្មភាពរហ័ស</span>
                    </div>
                    <div class="flex gap-2">
                        <button wire:click="selectAllAssignablePermissions" class="px-4 py-1.5 text-xs font-bold bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition-colors flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> ជ្រើសរើសទាំងអស់
                        </button>
                        <button wire:click="unselectAllAssignablePermissions" class="px-4 py-1.5 text-xs font-bold bg-red-50 text-red-500 rounded-lg hover:bg-red-100 transition-colors flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg> ដកចេញទាំងអស់
                        </button>
                    </div>
                </div>
                
                {{-- Permission Cards Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($groupedPermissions as $group => $permissions)
                        <div class="bg-white dark:bg-[var(--color-card-bg)] border border-gray-100 dark:border-[var(--color-border-color)] rounded-xl p-4 shadow-sm">
                            <div class="flex items-center justify-between border-b border-gray-100 dark:border-[var(--color-border-color)] pb-3 mb-3">
                                <h4 class="font-black text-gray-800 dark:text-[var(--color-text-main)] uppercase text-xs flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-purple-600"></span> {{ $group }}
                                </h4>
                                <span class="bg-gray-100 dark:bg-gray-800 text-gray-500 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ count($permissions) }}</span>
                            </div>
                            
                            <div class="space-y-3">
                                @foreach($permissions as $permission)
                                    <label class="flex items-start gap-3 cursor-pointer group">
                                        {{-- ចង wire:model ទៅ roleAssignablePermissionsSelected --}}
                                        <input type="checkbox" wire:model.live="roleAssignablePermissionsSelected" value="{{ $permission->name }}" 
                                            class="mt-0.5 w-4 h-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500 transition-all cursor-pointer">
                                        <span class="text-sm font-medium text-gray-600 dark:text-[var(--color-text-muted)] group-hover:text-gray-900 dark:group-hover:text-[var(--color-text-main)] transition-colors select-none">
                                            {{ $permission->name }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>

            {{-- Footer --}}
            <div class="p-5 border-t border-gray-100 dark:border-[var(--color-border-color)] flex items-center justify-between bg-white dark:bg-[var(--color-card-bg)]">
                <button wire:click="$set('isAssignableModalOpen', false)" class="px-6 py-2.5 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-[var(--color-text-muted)] font-bold rounded-lg text-sm hover:bg-gray-200 transition-all">បោះបង់</button>
                <button wire:click="saveAssignablePermissions" class="px-6 py-2.5 bg-[#4F25E9] text-white font-bold rounded-lg shadow-lg text-sm hover:bg-[#431dd1] transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> រក្សាទុកសិទ្ធិចែកចាយ
                </button>
            </div>
        </div>
    </div>
@endif