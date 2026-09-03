@if($isModalOpen)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 backdrop-blur-xl bg-black/40 dark:bg-black/60 animate-in fade-in duration-300">
        <div class="bg-[var(--color-card-bg)] w-full max-w-3xl rounded-xl shadow-2xl border border-[var(--color-border-color)] overflow-hidden transition-colors flex flex-col max-h-[90vh]">
            <div class="p-5 border-b border-[var(--color-border-color)] flex justify-between items-center bg-[var(--color-background)]/50 transition-colors shrink-0">
                <h3 class="text-lg font-bold text-[var(--color-text-main)]">{{ $itemId ? __('messages.edit') ?? 'Edit' : __('messages.add_new') ?? 'Add New' }} {{ __('messages.shop-info') ?? 'ShopInfo' }}</h3>
                <button type="button" wire:click="$set('isModalOpen', false)" class="text-[var(--color-text-muted)] hover:text-red-500 font-bold text-2xl transition-colors">&times;</button>
            </div>
            
            <form wire:submit.prevent="saveItem" class="flex-1 flex flex-col overflow-hidden">
                <div class="p-6 overflow-y-auto">
                    {{-- ✅ ចាក់បញ្ចូល Layout ដែលបានរៀបចំជា Horizontal (Flexbox) ពីខាងម៉ាស៊ីន Generator --}}
                                        <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.site_name') ?? 'Site Name' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="text" wire:model="site_name" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors" placeholder="Enter Site Name...">
                            @error('site_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.site_tagline') ?? 'Site Tagline' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="text" wire:model="site_tagline" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors" placeholder="Enter Site Tagline...">
                            @error('site_tagline') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 p-4 bg-[var(--color-background)]/30 rounded-xl border border-[var(--color-border-color)] border-dashed mb-4 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.logo') ?? 'Logo' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="file" wire:model="logo" id="f-logo"  accept="image/*" class="hidden">
                            <label for="f-logo" class="px-4 py-2 bg-[var(--color-primary)] text-white text-xs font-bold rounded-lg cursor-pointer inline-block">Upload Logo</label>
                            @if ($logo) 
                                <div class="mt-3 flex flex-wrap gap-2"> 
                                    @if(is_array($logo) || is_iterable($logo)) 
                                        @foreach($logo as $i => $f) 
                                            <img src="{{ is_string($f) ? (str_starts_with($f, 'http') ? $f : asset('storage/'.$f)) : $f->temporaryUrl() }}" class="h-20 w-20 rounded-lg object-cover"> 
                                        @endforeach 
                                    @else 
                                        <img src="{{ is_string($logo) ? (str_starts_with($logo, 'http') ? $logo : asset('storage/'.$logo)) : $logo->temporaryUrl() }}" class="h-20 w-20 rounded-lg object-cover"> 
                                    @endif 
                                </div> 
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 p-4 bg-[var(--color-background)]/30 rounded-xl border border-[var(--color-border-color)] border-dashed mb-4 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.favicon') ?? 'Favicon' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="file" wire:model="favicon" id="f-favicon"  accept="image/*" class="hidden">
                            <label for="f-favicon" class="px-4 py-2 bg-[var(--color-primary)] text-white text-xs font-bold rounded-lg cursor-pointer inline-block">Upload Favicon</label>
                            @if ($favicon) 
                                <div class="mt-3 flex flex-wrap gap-2"> 
                                    @if(is_array($favicon) || is_iterable($favicon)) 
                                        @foreach($favicon as $i => $f) 
                                            <img src="{{ is_string($f) ? (str_starts_with($f, 'http') ? $f : asset('storage/'.$f)) : $f->temporaryUrl() }}" class="h-20 w-20 rounded-lg object-cover"> 
                                        @endforeach 
                                    @else 
                                        <img src="{{ is_string($favicon) ? (str_starts_with($favicon, 'http') ? $favicon : asset('storage/'.$favicon)) : $favicon->temporaryUrl() }}" class="h-20 w-20 rounded-lg object-cover"> 
                                    @endif 
                                </div> 
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.phone') ?? 'Phone' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="text" wire:model="phone" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors" placeholder="Enter Phone...">
                            @error('phone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.email') ?? 'Email' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="text" wire:model="email" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors" placeholder="Enter Email...">
                            @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider md:pt-3">{{ __('messages.address') ?? 'Address' }}</label>
                        <div class="w-full md:w-3/4" wire:ignore>
                            <div x-data="{ value: @entangle('address'), init() { let self = this; let loadDeps = function() { if (typeof jQuery === 'undefined') { let jq = document.createElement('script'); jq.src = 'https://code.jquery.com/jquery-3.6.0.min.js'; document.head.appendChild(jq); jq.onload = loadSN; } else { loadSN(); } }; let loadSN = function() { if (typeof $.fn.summernote === 'undefined') { let css = document.createElement('link'); css.rel = 'stylesheet'; css.href = 'https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css'; document.head.appendChild(css); let sn = document.createElement('script'); sn.src = 'https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js'; document.head.appendChild(sn); sn.onload = self.initSN.bind(self); } else { self.initSN(); } }; loadDeps(); }, initSN() { let self = this; let editor = $(this.$refs.editor); editor.summernote({ height: 200, dialogsInBody: true, callbacks: { onChange: function(contents) { self.value = contents; } } }); if(this.value) editor.summernote('code', this.value); this.$watch('value', function(nv) { if (nv !== editor.summernote('code')) editor.summernote('code', nv || ''); }); } }">
                                <textarea x-ref="editor" class="hidden"></textarea>
                            </div>
                            @error('address') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.facebook_url') ?? 'Facebook Url' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="text" wire:model="facebook_url" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors" placeholder="Enter Facebook Url...">
                            @error('facebook_url') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.youtube_url') ?? 'Youtube Url' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="text" wire:model="youtube_url" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors" placeholder="Enter Youtube Url...">
                            @error('youtube_url') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.twitter_url') ?? 'Twitter Url' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="text" wire:model="twitter_url" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors" placeholder="Enter Twitter Url...">
                            @error('twitter_url') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
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
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 p-4 bg-[var(--color-background)]/30 rounded-xl border border-[var(--color-border-color)] border-dashed mb-4 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.og_image') ?? 'Og Image' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="file" wire:model="og_image" id="f-og_image"  accept="image/*" class="hidden">
                            <label for="f-og_image" class="px-4 py-2 bg-[var(--color-primary)] text-white text-xs font-bold rounded-lg cursor-pointer inline-block">Upload Og Image</label>
                            @if ($og_image) 
                                <div class="mt-3 flex flex-wrap gap-2"> 
                                    @if(is_array($og_image) || is_iterable($og_image)) 
                                        @foreach($og_image as $i => $f) 
                                            <img src="{{ is_string($f) ? (str_starts_with($f, 'http') ? $f : asset('storage/'.$f)) : $f->temporaryUrl() }}" class="h-20 w-20 rounded-lg object-cover"> 
                                        @endforeach 
                                    @else 
                                        <img src="{{ is_string($og_image) ? (str_starts_with($og_image, 'http') ? $og_image : asset('storage/'.$og_image)) : $og_image->temporaryUrl() }}" class="h-20 w-20 rounded-lg object-cover"> 
                                    @endif 
                                </div> 
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.google_site_verification') ?? 'Google Site Verification' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="text" wire:model="google_site_verification" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors" placeholder="Enter Google Site Verification...">
                            @error('google_site_verification') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider md:pt-3">{{ __('messages.google_analytics') ?? 'Google Analytics' }}</label>
                        <div class="w-full md:w-3/4" wire:ignore>
                            <div x-data="{ value: @entangle('google_analytics'), init() { let self = this; let loadDeps = function() { if (typeof jQuery === 'undefined') { let jq = document.createElement('script'); jq.src = 'https://code.jquery.com/jquery-3.6.0.min.js'; document.head.appendChild(jq); jq.onload = loadSN; } else { loadSN(); } }; let loadSN = function() { if (typeof $.fn.summernote === 'undefined') { let css = document.createElement('link'); css.rel = 'stylesheet'; css.href = 'https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css'; document.head.appendChild(css); let sn = document.createElement('script'); sn.src = 'https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js'; document.head.appendChild(sn); sn.onload = self.initSN.bind(self); } else { self.initSN(); } }; loadDeps(); }, initSN() { let self = this; let editor = $(this.$refs.editor); editor.summernote({ height: 200, dialogsInBody: true, callbacks: { onChange: function(contents) { self.value = contents; } } }); if(this.value) editor.summernote('code', this.value); this.$watch('value', function(nv) { if (nv !== editor.summernote('code')) editor.summernote('code', nv || ''); }); } }">
                                <textarea x-ref="editor" class="hidden"></textarea>
                            </div>
                            @error('google_analytics') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider md:pt-3">{{ __('messages.adsense_script') ?? 'Adsense Script' }}</label>
                        <div class="w-full md:w-3/4" wire:ignore>
                            <div x-data="{ value: @entangle('adsense_script'), init() { let self = this; let loadDeps = function() { if (typeof jQuery === 'undefined') { let jq = document.createElement('script'); jq.src = 'https://code.jquery.com/jquery-3.6.0.min.js'; document.head.appendChild(jq); jq.onload = loadSN; } else { loadSN(); } }; let loadSN = function() { if (typeof $.fn.summernote === 'undefined') { let css = document.createElement('link'); css.rel = 'stylesheet'; css.href = 'https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css'; document.head.appendChild(css); let sn = document.createElement('script'); sn.src = 'https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js'; document.head.appendChild(sn); sn.onload = self.initSN.bind(self); } else { self.initSN(); } }; loadDeps(); }, initSN() { let self = this; let editor = $(this.$refs.editor); editor.summernote({ height: 200, dialogsInBody: true, callbacks: { onChange: function(contents) { self.value = contents; } } }); if(this.value) editor.summernote('code', this.value); this.$watch('value', function(nv) { if (nv !== editor.summernote('code')) editor.summernote('code', nv || ''); }); } }">
                                <textarea x-ref="editor" class="hidden"></textarea>
                            </div>
                            @error('adsense_script') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider md:pt-3">{{ __('messages.adsterra_script') ?? 'Adsterra Script' }}</label>
                        <div class="w-full md:w-3/4" wire:ignore>
                            <div x-data="{ value: @entangle('adsterra_script'), init() { let self = this; let loadDeps = function() { if (typeof jQuery === 'undefined') { let jq = document.createElement('script'); jq.src = 'https://code.jquery.com/jquery-3.6.0.min.js'; document.head.appendChild(jq); jq.onload = loadSN; } else { loadSN(); } }; let loadSN = function() { if (typeof $.fn.summernote === 'undefined') { let css = document.createElement('link'); css.rel = 'stylesheet'; css.href = 'https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css'; document.head.appendChild(css); let sn = document.createElement('script'); sn.src = 'https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js'; document.head.appendChild(sn); sn.onload = self.initSN.bind(self); } else { self.initSN(); } }; loadDeps(); }, initSN() { let self = this; let editor = $(this.$refs.editor); editor.summernote({ height: 200, dialogsInBody: true, callbacks: { onChange: function(contents) { self.value = contents; } } }); if(this.value) editor.summernote('code', this.value); this.$watch('value', function(nv) { if (nv !== editor.summernote('code')) editor.summernote('code', nv || ''); }); } }">
                                <textarea x-ref="editor" class="hidden"></textarea>
                            </div>
                            @error('adsterra_script') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 p-4 bg-[var(--color-background)]/30 rounded-xl border border-[var(--color-border-color)] border-dashed mb-4 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.ad_top_banner') ?? 'Ad Top Banner' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="file" wire:model="ad_top_banner" id="f-ad_top_banner"  accept="image/*" class="hidden">
                            <label for="f-ad_top_banner" class="px-4 py-2 bg-[var(--color-primary)] text-white text-xs font-bold rounded-lg cursor-pointer inline-block">Upload Ad Top Banner</label>
                            @if ($ad_top_banner) 
                                <div class="mt-3 flex flex-wrap gap-2"> 
                                    @if(is_array($ad_top_banner) || is_iterable($ad_top_banner)) 
                                        @foreach($ad_top_banner as $i => $f) 
                                            <img src="{{ is_string($f) ? (str_starts_with($f, 'http') ? $f : asset('storage/'.$f)) : $f->temporaryUrl() }}" class="h-20 w-20 rounded-lg object-cover"> 
                                        @endforeach 
                                    @else 
                                        <img src="{{ is_string($ad_top_banner) ? (str_starts_with($ad_top_banner, 'http') ? $ad_top_banner : asset('storage/'.$ad_top_banner)) : $ad_top_banner->temporaryUrl() }}" class="h-20 w-20 rounded-lg object-cover"> 
                                    @endif 
                                </div> 
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 p-4 bg-[var(--color-background)]/30 rounded-xl border border-[var(--color-border-color)] border-dashed mb-4 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.ad_sidebar_banner') ?? 'Ad Sidebar Banner' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="file" wire:model="ad_sidebar_banner" id="f-ad_sidebar_banner"  accept="image/*" class="hidden">
                            <label for="f-ad_sidebar_banner" class="px-4 py-2 bg-[var(--color-primary)] text-white text-xs font-bold rounded-lg cursor-pointer inline-block">Upload Ad Sidebar Banner</label>
                            @if ($ad_sidebar_banner) 
                                <div class="mt-3 flex flex-wrap gap-2"> 
                                    @if(is_array($ad_sidebar_banner) || is_iterable($ad_sidebar_banner)) 
                                        @foreach($ad_sidebar_banner as $i => $f) 
                                            <img src="{{ is_string($f) ? (str_starts_with($f, 'http') ? $f : asset('storage/'.$f)) : $f->temporaryUrl() }}" class="h-20 w-20 rounded-lg object-cover"> 
                                        @endforeach 
                                    @else 
                                        <img src="{{ is_string($ad_sidebar_banner) ? (str_starts_with($ad_sidebar_banner, 'http') ? $ad_sidebar_banner : asset('storage/'.$ad_sidebar_banner)) : $ad_sidebar_banner->temporaryUrl() }}" class="h-20 w-20 rounded-lg object-cover"> 
                                    @endif 
                                </div> 
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 p-4 bg-[var(--color-background)]/30 rounded-xl border border-[var(--color-border-color)] border-dashed mb-4 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.ad_in_article_banner') ?? 'Ad In Article Banner' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="file" wire:model="ad_in_article_banner" id="f-ad_in_article_banner"  accept="image/*" class="hidden">
                            <label for="f-ad_in_article_banner" class="px-4 py-2 bg-[var(--color-primary)] text-white text-xs font-bold rounded-lg cursor-pointer inline-block">Upload Ad In Article Banner</label>
                            @if ($ad_in_article_banner) 
                                <div class="mt-3 flex flex-wrap gap-2"> 
                                    @if(is_array($ad_in_article_banner) || is_iterable($ad_in_article_banner)) 
                                        @foreach($ad_in_article_banner as $i => $f) 
                                            <img src="{{ is_string($f) ? (str_starts_with($f, 'http') ? $f : asset('storage/'.$f)) : $f->temporaryUrl() }}" class="h-20 w-20 rounded-lg object-cover"> 
                                        @endforeach 
                                    @else 
                                        <img src="{{ is_string($ad_in_article_banner) ? (str_starts_with($ad_in_article_banner, 'http') ? $ad_in_article_banner : asset('storage/'.$ad_in_article_banner)) : $ad_in_article_banner->temporaryUrl() }}" class="h-20 w-20 rounded-lg object-cover"> 
                                    @endif 
                                </div> 
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider md:pt-3">{{ __('messages.adskeeper_widget') ?? 'Adskeeper Widget' }}</label>
                        <div class="w-full md:w-3/4" wire:ignore>
                            <div x-data="{ value: @entangle('adskeeper_widget'), init() { let self = this; let loadDeps = function() { if (typeof jQuery === 'undefined') { let jq = document.createElement('script'); jq.src = 'https://code.jquery.com/jquery-3.6.0.min.js'; document.head.appendChild(jq); jq.onload = loadSN; } else { loadSN(); } }; let loadSN = function() { if (typeof $.fn.summernote === 'undefined') { let css = document.createElement('link'); css.rel = 'stylesheet'; css.href = 'https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css'; document.head.appendChild(css); let sn = document.createElement('script'); sn.src = 'https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js'; document.head.appendChild(sn); sn.onload = self.initSN.bind(self); } else { self.initSN(); } }; loadDeps(); }, initSN() { let self = this; let editor = $(this.$refs.editor); editor.summernote({ height: 200, dialogsInBody: true, callbacks: { onChange: function(contents) { self.value = contents; } } }); if(this.value) editor.summernote('code', this.value); this.$watch('value', function(nv) { if (nv !== editor.summernote('code')) editor.summernote('code', nv || ''); }); } }">
                                <textarea x-ref="editor" class="hidden"></textarea>
                            </div>
                            @error('adskeeper_widget') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
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