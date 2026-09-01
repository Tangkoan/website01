@if($isModalOpen)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 backdrop-blur-xl bg-black/40 dark:bg-black/60 animate-in fade-in duration-300">
        <div class="bg-[var(--color-card-bg)] w-full max-w-3xl rounded-xl shadow-2xl border border-[var(--color-border-color)] overflow-hidden transition-colors flex flex-col max-h-[90vh]">
            <div class="p-5 border-b border-[var(--color-border-color)] flex justify-between items-center bg-[var(--color-background)]/50 transition-colors shrink-0">
                <h3 class="text-lg font-bold text-[var(--color-text-main)]">{{ $itemId ? __('messages.edit') ?? 'Edit' : __('messages.add_new') ?? 'Add New' }} {{ __('messages.story') ?? 'Story' }}</h3>
                <button type="button" wire:click="$set('isModalOpen', false)" class="text-[var(--color-text-muted)] hover:text-red-500 font-bold text-2xl transition-colors">&times;</button>
            </div>
            
            <form wire:submit.prevent="saveItem" class="flex-1 flex flex-col overflow-hidden">
                <div class="p-6 overflow-y-auto">
                    {{-- ✅ ចាក់បញ្ចូល Layout ដែលបានរៀបចំជា Horizontal (Flexbox) ពីខាងម៉ាស៊ីន Generator --}}
                                        <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.category_id') ?? 'Category' }}</label>
                        <div class="w-full md:w-3/4">
                            <select wire:model="category_id" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none cursor-pointer">
                                <option value="">Select Category...</option>
                                @php $opts = class_exists('\App\Models\Category') ? \App\Models\Category::limit(100)->get() : collect(); @endphp
                                @foreach($opts as $opt) <option value="{{ $opt->id }}">{{ $opt->name ?? $opt->title ?? 'ID: ' . $opt->id }}</option> @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.user_id') ?? 'User' }}</label>
                        <div class="w-full md:w-3/4">
                            <select wire:model="user_id" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none cursor-pointer">
                                <option value="">Select User...</option>
                                @php $opts = class_exists('\App\Models\User') ? \App\Models\User::limit(100)->get() : collect(); @endphp
                                @foreach($opts as $opt) <option value="{{ $opt->id }}">{{ $opt->name ?? $opt->title ?? 'ID: ' . $opt->id }}</option> @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.title') ?? 'Title' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="text" wire:model="title" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors" placeholder="Enter Title...">
                            @error('title') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.slug') ?? 'Slug' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="text" wire:model="slug" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors" placeholder="Enter Slug...">
                            @error('slug') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 p-4 bg-[var(--color-background)]/30 rounded-xl border border-[var(--color-border-color)] border-dashed mb-4 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.thumbnail') ?? 'Thumbnail' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="file" wire:model="thumbnail" id="f-thumbnail"  accept="image/*" class="hidden">
                            <label for="f-thumbnail" class="px-4 py-2 bg-[var(--color-primary)] text-white text-xs font-bold rounded-lg cursor-pointer inline-block">Upload Thumbnail</label>
                            @if ($thumbnail) 
                                <div class="mt-3 flex flex-wrap gap-2"> 
                                    @if(is_array($thumbnail) || is_iterable($thumbnail)) 
                                        @foreach($thumbnail as $i => $f) 
                                            <img src="{{ is_string($f) ? (str_starts_with($f, 'http') ? $f : asset('storage/'.$f)) : $f->temporaryUrl() }}" class="h-20 w-20 rounded-lg object-cover"> 
                                        @endforeach 
                                    @else 
                                        <img src="{{ is_string($thumbnail) ? (str_starts_with($thumbnail, 'http') ? $thumbnail : asset('storage/'.$thumbnail)) : $thumbnail->temporaryUrl() }}" class="h-20 w-20 rounded-lg object-cover"> 
                                    @endif 
                                </div> 
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider md:pt-3">{{ __('messages.content') ?? 'Content' }}</label>
                        <div class="w-full md:w-3/4" wire:ignore>
                            <div x-data="{ value: @entangle('content'), init() { let self = this; let loadDeps = function() { if (typeof jQuery === 'undefined') { let jq = document.createElement('script'); jq.src = 'https://code.jquery.com/jquery-3.6.0.min.js'; document.head.appendChild(jq); jq.onload = loadSN; } else { loadSN(); } }; let loadSN = function() { if (typeof $.fn.summernote === 'undefined') { let css = document.createElement('link'); css.rel = 'stylesheet'; css.href = 'https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css'; document.head.appendChild(css); let sn = document.createElement('script'); sn.src = 'https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js'; document.head.appendChild(sn); sn.onload = self.initSN.bind(self); } else { self.initSN(); } }; loadDeps(); }, initSN() { let self = this; let editor = $(this.$refs.editor); editor.summernote({ height: 200, dialogsInBody: true, callbacks: { onChange: function(contents) { self.value = contents; } } }); if(this.value) editor.summernote('code', this.value); this.$watch('value', function(nv) { if (nv !== editor.summernote('code')) editor.summernote('code', nv || ''); }); } }">
                                <textarea x-ref="editor" class="hidden"></textarea>
                            </div>
                            @error('content') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.meta_title') ?? 'Meta Title' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="text" wire:model="meta_title" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors" placeholder="Enter Meta Title...">
                            @error('meta_title') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider md:pt-3">{{ __('messages.meta_description') ?? 'Meta Description' }}</label>
                        <div class="w-full md:w-3/4" wire:ignore>
                            <div x-data="{ value: @entangle('meta_description'), init() { let self = this; let loadDeps = function() { if (typeof jQuery === 'undefined') { let jq = document.createElement('script'); jq.src = 'https://code.jquery.com/jquery-3.6.0.min.js'; document.head.appendChild(jq); jq.onload = loadSN; } else { loadSN(); } }; let loadSN = function() { if (typeof $.fn.summernote === 'undefined') { let css = document.createElement('link'); css.rel = 'stylesheet'; css.href = 'https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css'; document.head.appendChild(css); let sn = document.createElement('script'); sn.src = 'https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js'; document.head.appendChild(sn); sn.onload = self.initSN.bind(self); } else { self.initSN(); } }; loadDeps(); }, initSN() { let self = this; let editor = $(this.$refs.editor); editor.summernote({ height: 200, dialogsInBody: true, callbacks: { onChange: function(contents) { self.value = contents; } } }); if(this.value) editor.summernote('code', this.value); this.$watch('value', function(nv) { if (nv !== editor.summernote('code')) editor.summernote('code', nv || ''); }); } }">
                                <textarea x-ref="editor" class="hidden"></textarea>
                            </div>
                            @error('meta_description') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.views_count') ?? 'Views Count' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="text" wire:model="views_count" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors" placeholder="Enter Views Count...">
                            @error('views_count') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 p-4 bg-[var(--color-background)]/30 rounded-xl border border-[var(--color-border-color)] mb-4 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.status') ?? 'Status' }}</label>
                        <div class="w-full md:w-3/4 flex items-center gap-3">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="status" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-[var(--color-primary)] after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                            </label>
                            <span class="text-sm font-bold text-[var(--color-text-main)] uppercase">{{ $status ? __('messages.active') : __('messages.inactive') }}</span>
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.published_at') ?? 'Published At' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="text" wire:model="published_at" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors" placeholder="Enter Published At...">
                            @error('published_at') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
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