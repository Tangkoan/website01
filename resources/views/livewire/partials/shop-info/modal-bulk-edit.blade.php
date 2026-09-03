@if($isBulkEditModalOpen)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 backdrop-blur-xl bg-black/40 dark:bg-black/60 animate-in fade-in duration-300">
        <div class="bg-[var(--color-card-bg)] w-full max-w-4xl rounded-xl shadow-2xl border border-[var(--color-border-color)] overflow-hidden flex flex-col md:flex-row max-h-[90vh]">
            
            {{-- ផ្នែកខាងឆ្វេង: បញ្ជី Items ដែលត្រូវ Edit (មាន Scroll ដាច់ដោយឡែក) --}}
            <div class="w-full md:w-1/3 bg-[var(--color-background)] border-r border-[var(--color-border-color)] flex flex-col max-h-[35vh] md:max-h-full">
                <div class="p-5 border-b border-[var(--color-border-color)] shrink-0 bg-[var(--color-background)]">
                    <h3 class="text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.editing') ?? 'Editing' }} {{ count($selectedItemsQueue) }} {{ __('messages.items') ?? 'Items' }}</h3>
                </div>
                <div class="p-3 overflow-y-auto flex-1 space-y-2 no-scrollbar">
                    @foreach($selectedItemsQueue as $index => $id)
                        @php $itemName = \App\Models\ShopInfo::find($id)?->name ?? 'Unknown'; @endphp
                        <button type="button" wire:key="bulk-item-{{ $index }}" wire:click="jumpToBulkItem({{ $index }})" class="w-full text-left p-2.5 md:p-3 rounded-lg font-bold text-xs md:text-sm transition-all flex items-center justify-between {{ $currentBulkIndex === $index ? 'bg-[var(--color-primary)] text-[var(--color-primary-text)] shadow-md' : 'bg-[var(--color-card-bg)] text-[var(--color-text-main)] border border-[var(--color-border-color)] hover:border-[var(--color-primary)]/50' }}">
                            <span class="truncate pr-2">{{ $itemName }}</span>
                            @if($currentBulkIndex === $index) <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg> @endif
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- ផ្នែកខាងស្តាំ: Form សម្រាប់ Edit (មាន Scroll ត្រឹមតែតួ Form ប៉ុណ្ណោះ) --}}
            <div class="w-full md:w-2/3 flex flex-col bg-[var(--color-card-bg)] max-h-[55vh] md:max-h-full overflow-hidden">
                {{-- Header របស់ Form --}}
                <div class="p-5 md:p-6 border-b border-[var(--color-border-color)] flex justify-between items-center shrink-0">
                    <h3 class="text-lg md:text-xl font-black text-[var(--color-text-main)]">{{ __('messages.edit') ?? 'Edit' }} <span class="text-[var(--color-primary)]">({{ $currentBulkIndex + 1 }}/{{ count($selectedItemsQueue) }})</span></h3>
                    <button type="button" wire:click="closeBulkEdit" class="text-[var(--color-text-muted)] hover:text-red-500 transition-colors bg-[var(--color-background)] rounded-full p-1.5"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                
                {{-- តួ Form --}}
                <form wire:submit.prevent="saveAndNextBulkItem" class="flex-1 overflow-y-auto p-5 md:p-6 flex flex-col">
                    <div class="space-y-4 flex-1">
                        {{-- ✅ បញ្ចូលទម្រង់ Field ដែល Generate ស្វ័យប្រវត្តិតាម Database --}}
                                            <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.site_name') ?? 'Site Name' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="text" wire:model="bulkItem_site_name" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors" placeholder="Enter Site Name...">
                            @error('bulkItem_site_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.site_tagline') ?? 'Site Tagline' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="text" wire:model="bulkItem_site_tagline" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors" placeholder="Enter Site Tagline...">
                            @error('bulkItem_site_tagline') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 p-4 bg-[var(--color-background)]/30 rounded-xl border border-[var(--color-border-color)] border-dashed mb-4 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.logo') ?? 'Logo' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="file" wire:model="bulkItem_logo" id="f-bulkItem_logo"  accept="image/*" class="hidden">
                            <label for="f-bulkItem_logo" class="px-4 py-2 bg-[var(--color-primary)] text-white text-xs font-bold rounded-lg cursor-pointer inline-block">Upload Logo</label>
                            @if ($bulkItem_logo) 
                                <div class="mt-3 flex flex-wrap gap-2"> 
                                    @if(is_array($bulkItem_logo) || is_iterable($bulkItem_logo)) 
                                        @foreach($bulkItem_logo as $i => $f) 
                                            <img src="{{ is_string($f) ? (str_starts_with($f, 'http') ? $f : asset('storage/'.$f)) : $f->temporaryUrl() }}" class="h-20 w-20 rounded-lg object-cover"> 
                                        @endforeach 
                                    @else 
                                        <img src="{{ is_string($bulkItem_logo) ? (str_starts_with($bulkItem_logo, 'http') ? $bulkItem_logo : asset('storage/'.$bulkItem_logo)) : $bulkItem_logo->temporaryUrl() }}" class="h-20 w-20 rounded-lg object-cover"> 
                                    @endif 
                                </div> 
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 p-4 bg-[var(--color-background)]/30 rounded-xl border border-[var(--color-border-color)] border-dashed mb-4 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.favicon') ?? 'Favicon' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="file" wire:model="bulkItem_favicon" id="f-bulkItem_favicon"  accept="image/*" class="hidden">
                            <label for="f-bulkItem_favicon" class="px-4 py-2 bg-[var(--color-primary)] text-white text-xs font-bold rounded-lg cursor-pointer inline-block">Upload Favicon</label>
                            @if ($bulkItem_favicon) 
                                <div class="mt-3 flex flex-wrap gap-2"> 
                                    @if(is_array($bulkItem_favicon) || is_iterable($bulkItem_favicon)) 
                                        @foreach($bulkItem_favicon as $i => $f) 
                                            <img src="{{ is_string($f) ? (str_starts_with($f, 'http') ? $f : asset('storage/'.$f)) : $f->temporaryUrl() }}" class="h-20 w-20 rounded-lg object-cover"> 
                                        @endforeach 
                                    @else 
                                        <img src="{{ is_string($bulkItem_favicon) ? (str_starts_with($bulkItem_favicon, 'http') ? $bulkItem_favicon : asset('storage/'.$bulkItem_favicon)) : $bulkItem_favicon->temporaryUrl() }}" class="h-20 w-20 rounded-lg object-cover"> 
                                    @endif 
                                </div> 
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.phone') ?? 'Phone' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="text" wire:model="bulkItem_phone" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors" placeholder="Enter Phone...">
                            @error('bulkItem_phone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.email') ?? 'Email' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="text" wire:model="bulkItem_email" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors" placeholder="Enter Email...">
                            @error('bulkItem_email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider md:pt-3">{{ __('messages.address') ?? 'Address' }}</label>
                        <div class="w-full md:w-3/4" wire:ignore>
                            <div x-data="{ value: @entangle('bulkItem_address'), init() { let self = this; let loadDeps = function() { if (typeof jQuery === 'undefined') { let jq = document.createElement('script'); jq.src = 'https://code.jquery.com/jquery-3.6.0.min.js'; document.head.appendChild(jq); jq.onload = loadSN; } else { loadSN(); } }; let loadSN = function() { if (typeof $.fn.summernote === 'undefined') { let css = document.createElement('link'); css.rel = 'stylesheet'; css.href = 'https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css'; document.head.appendChild(css); let sn = document.createElement('script'); sn.src = 'https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js'; document.head.appendChild(sn); sn.onload = self.initSN.bind(self); } else { self.initSN(); } }; loadDeps(); }, initSN() { let self = this; let editor = $(this.$refs.editor); editor.summernote({ height: 200, dialogsInBody: true, callbacks: { onChange: function(contents) { self.value = contents; } } }); if(this.value) editor.summernote('code', this.value); this.$watch('value', function(nv) { if (nv !== editor.summernote('code')) editor.summernote('code', nv || ''); }); } }">
                                <textarea x-ref="editor" class="hidden"></textarea>
                            </div>
                            @error('bulkItem_address') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.facebook_url') ?? 'Facebook Url' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="text" wire:model="bulkItem_facebook_url" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors" placeholder="Enter Facebook Url...">
                            @error('bulkItem_facebook_url') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.youtube_url') ?? 'Youtube Url' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="text" wire:model="bulkItem_youtube_url" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors" placeholder="Enter Youtube Url...">
                            @error('bulkItem_youtube_url') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.twitter_url') ?? 'Twitter Url' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="text" wire:model="bulkItem_twitter_url" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors" placeholder="Enter Twitter Url...">
                            @error('bulkItem_twitter_url') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.meta_title') ?? 'Meta Title' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="text" wire:model="bulkItem_meta_title" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors" placeholder="Enter Meta Title...">
                            @error('bulkItem_meta_title') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider md:pt-3">{{ __('messages.meta_description') ?? 'Meta Description' }}</label>
                        <div class="w-full md:w-3/4" wire:ignore>
                            <div x-data="{ value: @entangle('bulkItem_meta_description'), init() { let self = this; let loadDeps = function() { if (typeof jQuery === 'undefined') { let jq = document.createElement('script'); jq.src = 'https://code.jquery.com/jquery-3.6.0.min.js'; document.head.appendChild(jq); jq.onload = loadSN; } else { loadSN(); } }; let loadSN = function() { if (typeof $.fn.summernote === 'undefined') { let css = document.createElement('link'); css.rel = 'stylesheet'; css.href = 'https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css'; document.head.appendChild(css); let sn = document.createElement('script'); sn.src = 'https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js'; document.head.appendChild(sn); sn.onload = self.initSN.bind(self); } else { self.initSN(); } }; loadDeps(); }, initSN() { let self = this; let editor = $(this.$refs.editor); editor.summernote({ height: 200, dialogsInBody: true, callbacks: { onChange: function(contents) { self.value = contents; } } }); if(this.value) editor.summernote('code', this.value); this.$watch('value', function(nv) { if (nv !== editor.summernote('code')) editor.summernote('code', nv || ''); }); } }">
                                <textarea x-ref="editor" class="hidden"></textarea>
                            </div>
                            @error('bulkItem_meta_description') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 p-4 bg-[var(--color-background)]/30 rounded-xl border border-[var(--color-border-color)] border-dashed mb-4 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.og_image') ?? 'Og Image' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="file" wire:model="bulkItem_og_image" id="f-bulkItem_og_image"  accept="image/*" class="hidden">
                            <label for="f-bulkItem_og_image" class="px-4 py-2 bg-[var(--color-primary)] text-white text-xs font-bold rounded-lg cursor-pointer inline-block">Upload Og Image</label>
                            @if ($bulkItem_og_image) 
                                <div class="mt-3 flex flex-wrap gap-2"> 
                                    @if(is_array($bulkItem_og_image) || is_iterable($bulkItem_og_image)) 
                                        @foreach($bulkItem_og_image as $i => $f) 
                                            <img src="{{ is_string($f) ? (str_starts_with($f, 'http') ? $f : asset('storage/'.$f)) : $f->temporaryUrl() }}" class="h-20 w-20 rounded-lg object-cover"> 
                                        @endforeach 
                                    @else 
                                        <img src="{{ is_string($bulkItem_og_image) ? (str_starts_with($bulkItem_og_image, 'http') ? $bulkItem_og_image : asset('storage/'.$bulkItem_og_image)) : $bulkItem_og_image->temporaryUrl() }}" class="h-20 w-20 rounded-lg object-cover"> 
                                    @endif 
                                </div> 
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.google_site_verification') ?? 'Google Site Verification' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="text" wire:model="bulkItem_google_site_verification" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors" placeholder="Enter Google Site Verification...">
                            @error('bulkItem_google_site_verification') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider md:pt-3">{{ __('messages.google_analytics') ?? 'Google Analytics' }}</label>
                        <div class="w-full md:w-3/4" wire:ignore>
                            <div x-data="{ value: @entangle('bulkItem_google_analytics'), init() { let self = this; let loadDeps = function() { if (typeof jQuery === 'undefined') { let jq = document.createElement('script'); jq.src = 'https://code.jquery.com/jquery-3.6.0.min.js'; document.head.appendChild(jq); jq.onload = loadSN; } else { loadSN(); } }; let loadSN = function() { if (typeof $.fn.summernote === 'undefined') { let css = document.createElement('link'); css.rel = 'stylesheet'; css.href = 'https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css'; document.head.appendChild(css); let sn = document.createElement('script'); sn.src = 'https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js'; document.head.appendChild(sn); sn.onload = self.initSN.bind(self); } else { self.initSN(); } }; loadDeps(); }, initSN() { let self = this; let editor = $(this.$refs.editor); editor.summernote({ height: 200, dialogsInBody: true, callbacks: { onChange: function(contents) { self.value = contents; } } }); if(this.value) editor.summernote('code', this.value); this.$watch('value', function(nv) { if (nv !== editor.summernote('code')) editor.summernote('code', nv || ''); }); } }">
                                <textarea x-ref="editor" class="hidden"></textarea>
                            </div>
                            @error('bulkItem_google_analytics') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider md:pt-3">{{ __('messages.adsense_script') ?? 'Adsense Script' }}</label>
                        <div class="w-full md:w-3/4" wire:ignore>
                            <div x-data="{ value: @entangle('bulkItem_adsense_script'), init() { let self = this; let loadDeps = function() { if (typeof jQuery === 'undefined') { let jq = document.createElement('script'); jq.src = 'https://code.jquery.com/jquery-3.6.0.min.js'; document.head.appendChild(jq); jq.onload = loadSN; } else { loadSN(); } }; let loadSN = function() { if (typeof $.fn.summernote === 'undefined') { let css = document.createElement('link'); css.rel = 'stylesheet'; css.href = 'https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css'; document.head.appendChild(css); let sn = document.createElement('script'); sn.src = 'https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js'; document.head.appendChild(sn); sn.onload = self.initSN.bind(self); } else { self.initSN(); } }; loadDeps(); }, initSN() { let self = this; let editor = $(this.$refs.editor); editor.summernote({ height: 200, dialogsInBody: true, callbacks: { onChange: function(contents) { self.value = contents; } } }); if(this.value) editor.summernote('code', this.value); this.$watch('value', function(nv) { if (nv !== editor.summernote('code')) editor.summernote('code', nv || ''); }); } }">
                                <textarea x-ref="editor" class="hidden"></textarea>
                            </div>
                            @error('bulkItem_adsense_script') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider md:pt-3">{{ __('messages.adsterra_script') ?? 'Adsterra Script' }}</label>
                        <div class="w-full md:w-3/4" wire:ignore>
                            <div x-data="{ value: @entangle('bulkItem_adsterra_script'), init() { let self = this; let loadDeps = function() { if (typeof jQuery === 'undefined') { let jq = document.createElement('script'); jq.src = 'https://code.jquery.com/jquery-3.6.0.min.js'; document.head.appendChild(jq); jq.onload = loadSN; } else { loadSN(); } }; let loadSN = function() { if (typeof $.fn.summernote === 'undefined') { let css = document.createElement('link'); css.rel = 'stylesheet'; css.href = 'https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css'; document.head.appendChild(css); let sn = document.createElement('script'); sn.src = 'https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js'; document.head.appendChild(sn); sn.onload = self.initSN.bind(self); } else { self.initSN(); } }; loadDeps(); }, initSN() { let self = this; let editor = $(this.$refs.editor); editor.summernote({ height: 200, dialogsInBody: true, callbacks: { onChange: function(contents) { self.value = contents; } } }); if(this.value) editor.summernote('code', this.value); this.$watch('value', function(nv) { if (nv !== editor.summernote('code')) editor.summernote('code', nv || ''); }); } }">
                                <textarea x-ref="editor" class="hidden"></textarea>
                            </div>
                            @error('bulkItem_adsterra_script') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 p-4 bg-[var(--color-background)]/30 rounded-xl border border-[var(--color-border-color)] border-dashed mb-4 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.ad_top_banner') ?? 'Ad Top Banner' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="file" wire:model="bulkItem_ad_top_banner" id="f-bulkItem_ad_top_banner"  accept="image/*" class="hidden">
                            <label for="f-bulkItem_ad_top_banner" class="px-4 py-2 bg-[var(--color-primary)] text-white text-xs font-bold rounded-lg cursor-pointer inline-block">Upload Ad Top Banner</label>
                            @if ($bulkItem_ad_top_banner) 
                                <div class="mt-3 flex flex-wrap gap-2"> 
                                    @if(is_array($bulkItem_ad_top_banner) || is_iterable($bulkItem_ad_top_banner)) 
                                        @foreach($bulkItem_ad_top_banner as $i => $f) 
                                            <img src="{{ is_string($f) ? (str_starts_with($f, 'http') ? $f : asset('storage/'.$f)) : $f->temporaryUrl() }}" class="h-20 w-20 rounded-lg object-cover"> 
                                        @endforeach 
                                    @else 
                                        <img src="{{ is_string($bulkItem_ad_top_banner) ? (str_starts_with($bulkItem_ad_top_banner, 'http') ? $bulkItem_ad_top_banner : asset('storage/'.$bulkItem_ad_top_banner)) : $bulkItem_ad_top_banner->temporaryUrl() }}" class="h-20 w-20 rounded-lg object-cover"> 
                                    @endif 
                                </div> 
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 p-4 bg-[var(--color-background)]/30 rounded-xl border border-[var(--color-border-color)] border-dashed mb-4 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.ad_sidebar_banner') ?? 'Ad Sidebar Banner' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="file" wire:model="bulkItem_ad_sidebar_banner" id="f-bulkItem_ad_sidebar_banner"  accept="image/*" class="hidden">
                            <label for="f-bulkItem_ad_sidebar_banner" class="px-4 py-2 bg-[var(--color-primary)] text-white text-xs font-bold rounded-lg cursor-pointer inline-block">Upload Ad Sidebar Banner</label>
                            @if ($bulkItem_ad_sidebar_banner) 
                                <div class="mt-3 flex flex-wrap gap-2"> 
                                    @if(is_array($bulkItem_ad_sidebar_banner) || is_iterable($bulkItem_ad_sidebar_banner)) 
                                        @foreach($bulkItem_ad_sidebar_banner as $i => $f) 
                                            <img src="{{ is_string($f) ? (str_starts_with($f, 'http') ? $f : asset('storage/'.$f)) : $f->temporaryUrl() }}" class="h-20 w-20 rounded-lg object-cover"> 
                                        @endforeach 
                                    @else 
                                        <img src="{{ is_string($bulkItem_ad_sidebar_banner) ? (str_starts_with($bulkItem_ad_sidebar_banner, 'http') ? $bulkItem_ad_sidebar_banner : asset('storage/'.$bulkItem_ad_sidebar_banner)) : $bulkItem_ad_sidebar_banner->temporaryUrl() }}" class="h-20 w-20 rounded-lg object-cover"> 
                                    @endif 
                                </div> 
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 p-4 bg-[var(--color-background)]/30 rounded-xl border border-[var(--color-border-color)] border-dashed mb-4 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.ad_in_article_banner') ?? 'Ad In Article Banner' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="file" wire:model="bulkItem_ad_in_article_banner" id="f-bulkItem_ad_in_article_banner"  accept="image/*" class="hidden">
                            <label for="f-bulkItem_ad_in_article_banner" class="px-4 py-2 bg-[var(--color-primary)] text-white text-xs font-bold rounded-lg cursor-pointer inline-block">Upload Ad In Article Banner</label>
                            @if ($bulkItem_ad_in_article_banner) 
                                <div class="mt-3 flex flex-wrap gap-2"> 
                                    @if(is_array($bulkItem_ad_in_article_banner) || is_iterable($bulkItem_ad_in_article_banner)) 
                                        @foreach($bulkItem_ad_in_article_banner as $i => $f) 
                                            <img src="{{ is_string($f) ? (str_starts_with($f, 'http') ? $f : asset('storage/'.$f)) : $f->temporaryUrl() }}" class="h-20 w-20 rounded-lg object-cover"> 
                                        @endforeach 
                                    @else 
                                        <img src="{{ is_string($bulkItem_ad_in_article_banner) ? (str_starts_with($bulkItem_ad_in_article_banner, 'http') ? $bulkItem_ad_in_article_banner : asset('storage/'.$bulkItem_ad_in_article_banner)) : $bulkItem_ad_in_article_banner->temporaryUrl() }}" class="h-20 w-20 rounded-lg object-cover"> 
                                    @endif 
                                </div> 
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider md:pt-3">{{ __('messages.adskeeper_widget') ?? 'Adskeeper Widget' }}</label>
                        <div class="w-full md:w-3/4" wire:ignore>
                            <div x-data="{ value: @entangle('bulkItem_adskeeper_widget'), init() { let self = this; let loadDeps = function() { if (typeof jQuery === 'undefined') { let jq = document.createElement('script'); jq.src = 'https://code.jquery.com/jquery-3.6.0.min.js'; document.head.appendChild(jq); jq.onload = loadSN; } else { loadSN(); } }; let loadSN = function() { if (typeof $.fn.summernote === 'undefined') { let css = document.createElement('link'); css.rel = 'stylesheet'; css.href = 'https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css'; document.head.appendChild(css); let sn = document.createElement('script'); sn.src = 'https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js'; document.head.appendChild(sn); sn.onload = self.initSN.bind(self); } else { self.initSN(); } }; loadDeps(); }, initSN() { let self = this; let editor = $(this.$refs.editor); editor.summernote({ height: 200, dialogsInBody: true, callbacks: { onChange: function(contents) { self.value = contents; } } }); if(this.value) editor.summernote('code', this.value); this.$watch('value', function(nv) { if (nv !== editor.summernote('code')) editor.summernote('code', nv || ''); }); } }">
                                <textarea x-ref="editor" class="hidden"></textarea>
                            </div>
                            @error('bulkItem_adskeeper_widget') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    </div>

                    {{-- Footer (ប៊ូតុង Save & Skip) --}}
                    <div class="mt-8 flex gap-3 pt-5 border-t border-[var(--color-border-color)] shrink-0">
                        <button type="button" wire:click="skipBulkItem" class="flex-1 h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] font-black rounded-lg hover:brightness-95 transition-all uppercase tracking-widest text-xs">{{ __('messages.skip') ?? 'Skip' }}</button>
                        <button type="submit" class="flex-[2] h-11 bg-[var(--color-primary)] text-[var(--color-primary-text)] font-black rounded-lg shadow-xl hover:brightness-110 active:scale-95 transition-all uppercase tracking-widest text-xs flex items-center justify-center gap-2">
                            <span wire:loading.remove>{{ __('messages.save_next') ?? 'Save & Next' }}</span>
                            <span wire:loading>{{ __('messages.processing') ?? 'Saving...' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif