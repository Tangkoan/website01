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
                        @php $itemName = \App\Models\Brand::find($id)?->name ?? 'Unknown'; @endphp
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
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.parent_id') ?? 'Parent' }}</label>
                        <div class="w-full md:w-3/4">
                            <select wire:model="bulkItem_parent_id" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors cursor-pointer">
                                <option value="">Select Parent...</option>
                                {{-- ✅ កែប្រែ៖ ប្រើ limit(100) ដើម្បីការពារគាំង Server ហើយដក select('name') ចេញដើម្បីការពារ Error Column not found --}}
                                @php 
                                    $opts = class_exists('\App\Models\Brand') 
                                        ? \App\Models\Brand::limit(100)->get() 
                                        : collect(); 
                                @endphp
                                @foreach($opts as $opt) 
                                    <option value="{{ $opt->id }}">{{ $opt->name ?? $opt->title ?? 'ID: ' . $opt->id }}</option> 
                                @endforeach
                            </select>
                            @error('bulkItem_parent_id') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.name') ?? 'Name' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="text" wire:model="bulkItem_name" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors" placeholder="Enter Name...">
                            @error('bulkItem_name') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 p-4 bg-[var(--color-background)]/30 rounded-xl border border-[var(--color-border-color)] mb-4 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.status') ?? 'Status' }}</label>
                        <div class="w-full md:w-3/4 flex items-center gap-3">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="bulkItem_status" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-[var(--color-primary)] after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                            </label>
                            <span class="text-sm font-bold text-[var(--color-text-main)] uppercase">{{ $bulkItem_status ? __('messages.active') : __('messages.inactive') }}</span>
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 p-4 bg-[var(--color-background)]/30 rounded-xl border border-[var(--color-border-color)] border-dashed mb-4 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider md:pt-2">{{ __('messages.image') ?? 'Image' }}</label>
                        <div class="w-full md:w-3/4">
                            <div class="flex items-center gap-3">
                                <input type="file" wire:model="bulkItem_image" id="f-bulkItem_image"  accept="image/*" class="hidden">
                                <label for="f-bulkItem_image" class="px-4 py-2 bg-[var(--color-primary)]/10 text-[var(--color-primary)] hover:bg-[var(--color-primary)] hover:text-white text-xs font-bold rounded-lg cursor-pointer transition-colors flex items-center gap-2 shadow-sm">Upload Image</label>
                            </div>
                            @if ($bulkItem_image) <div class="mt-3 flex flex-wrap gap-3"> @if(is_array($bulkItem_image) || is_iterable($bulkItem_image)) @foreach($bulkItem_image as $i => $f) <div class="relative inline-block"><img src="{{ is_string($f) ? asset('storage/'.$f) : $f->temporaryUrl() }}" class="h-20 w-24 rounded-lg border border-[var(--color-border-color)] object-cover"><button type="button" wire:click.stop="removeFile('bulkItem_image', {{ $i }})" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div> @endforeach @else <div class="relative inline-block"><img src="{{ is_string($bulkItem_image) ? asset('storage/'.$bulkItem_image) : $bulkItem_image->temporaryUrl() }}" class="h-24 w-32 rounded-lg border border-[var(--color-border-color)] object-cover"><button type="button" wire:click.stop="$set('bulkItem_image', null)" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div> @endif </div> @endif
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 p-4 bg-[var(--color-background)]/30 rounded-xl border border-[var(--color-border-color)] border-dashed mb-4 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider md:pt-2">{{ __('messages.images') ?? 'Images' }}</label>
                        <div class="w-full md:w-3/4">
                            <div class="flex items-center gap-3">
                                <input type="file" wire:model="bulkItem_images" id="f-bulkItem_images" multiple accept="image/*" class="hidden">
                                <label for="f-bulkItem_images" class="px-4 py-2 bg-[var(--color-primary)]/10 text-[var(--color-primary)] hover:bg-[var(--color-primary)] hover:text-white text-xs font-bold rounded-lg cursor-pointer transition-colors flex items-center gap-2 shadow-sm">Upload Images</label>
                            </div>
                            @if ($bulkItem_images) <div class="mt-3 flex flex-wrap gap-3"> @if(is_array($bulkItem_images) || is_iterable($bulkItem_images)) @foreach($bulkItem_images as $i => $f) <div class="relative inline-block"><img src="{{ is_string($f) ? asset('storage/'.$f) : $f->temporaryUrl() }}" class="h-20 w-24 rounded-lg border border-[var(--color-border-color)] object-cover"><button type="button" wire:click.stop="removeFile('bulkItem_images', {{ $i }})" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div> @endforeach @else <div class="relative inline-block"><img src="{{ is_string($bulkItem_images) ? asset('storage/'.$bulkItem_images) : $bulkItem_images->temporaryUrl() }}" class="h-24 w-32 rounded-lg border border-[var(--color-border-color)] object-cover"><button type="button" wire:click.stop="$set('bulkItem_images', null)" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div> @endif </div> @endif
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