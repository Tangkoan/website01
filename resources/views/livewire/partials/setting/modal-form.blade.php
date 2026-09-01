@if($isModalOpen)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 backdrop-blur-xl bg-black/40 dark:bg-black/60 animate-in fade-in duration-300">
        <div class="bg-[var(--color-card-bg)] w-full max-w-3xl rounded-xl shadow-2xl border border-[var(--color-border-color)] overflow-hidden transition-colors flex flex-col max-h-[90vh]">
            <div class="p-5 border-b border-[var(--color-border-color)] flex justify-between items-center bg-[var(--color-background)]/50 transition-colors shrink-0">
                <h3 class="text-lg font-bold text-[var(--color-text-main)]">{{ $itemId ? __('messages.edit') ?? 'Edit' : __('messages.add_new') ?? 'Add New' }} {{ __('messages.setting') ?? 'Setting' }}</h3>
                <button type="button" wire:click="$set('isModalOpen', false)" class="text-[var(--color-text-muted)] hover:text-red-500 font-bold text-2xl transition-colors">&times;</button>
            </div>
            
            <form wire:submit.prevent="saveItem" class="flex-1 flex flex-col overflow-hidden">
                <div class="p-6 overflow-y-auto">
                    {{-- ✅ ចាក់បញ្ចូល Layout ដែលបានរៀបចំជា Horizontal (Flexbox) ពីខាងម៉ាស៊ីន Generator --}}
                                        <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.key') ?? 'Key' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="text" wire:model="key" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors" placeholder="Enter Key...">
                            @error('key') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider md:pt-3">{{ __('messages.value') ?? 'Value' }}</label>
                        <div class="w-full md:w-3/4" wire:ignore>
                            <div x-data="{ value: @entangle('value'), init() { let self = this; let loadDeps = function() { if (typeof jQuery === 'undefined') { let jq = document.createElement('script'); jq.src = 'https://code.jquery.com/jquery-3.6.0.min.js'; document.head.appendChild(jq); jq.onload = loadSN; } else { loadSN(); } }; let loadSN = function() { if (typeof $.fn.summernote === 'undefined') { let css = document.createElement('link'); css.rel = 'stylesheet'; css.href = 'https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css'; document.head.appendChild(css); let sn = document.createElement('script'); sn.src = 'https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js'; document.head.appendChild(sn); sn.onload = self.initSN.bind(self); } else { self.initSN(); } }; loadDeps(); }, initSN() { let self = this; let editor = $(this.$refs.editor); editor.summernote({ height: 200, dialogsInBody: true, callbacks: { onChange: function(contents) { self.value = contents; } } }); if(this.value) editor.summernote('code', this.value); this.$watch('value', function(nv) { if (nv !== editor.summernote('code')) editor.summernote('code', nv || ''); }); } }">
                                <textarea x-ref="editor" class="hidden"></textarea>
                            </div>
                            @error('value') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                </div>
                
                <div class="p-5 flex justify-end gap-3 border-t border-[var(--color-border-color)] bg-[var(--color-background)]/30 transition-colors shrink-0">
                    <button type="button" wire:click="$set('isModalOpen', false)" class="px-5 py-2.5 bg-[var(--color-card-bg)] border border-[var(--color-border-color)] text-[var(--color-text-main)] font-bold rounded-lg text-sm hover:brightness-95 transition-all shadow-sm">{{ __('messages.cancel') ?? 'Cancel' }}</button>
                    <button type="submit" class="px-5 py-2.5 bg-[var(--color-primary)] text-[var(--color-primary-text)] font-bold rounded-lg shadow-md text-sm hover:brightness-110 transition-all flex items-center gap-2">
                        <span wire:loading.remove>{{ __('messages.save_data') ?? 'Save Data' }}</span>
                        <span wire:loading>{{ __('messages.processing') ?? 'Saving...' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif