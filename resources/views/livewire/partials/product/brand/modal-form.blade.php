@if($isModalOpen)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 backdrop-blur-xl bg-black/40 dark:bg-black/60 animate-in fade-in duration-300">
        <div class="bg-[var(--color-card-bg)] w-full max-w-3xl rounded-xl shadow-2xl border border-[var(--color-border-color)] overflow-hidden transition-colors flex flex-col max-h-[90vh]">
            <div class="p-5 border-b border-[var(--color-border-color)] flex justify-between items-center bg-[var(--color-background)]/50 transition-colors shrink-0">
                <h3 class="text-lg font-bold text-[var(--color-text-main)]">{{ $itemId ? __('messages.edit') ?? 'Edit' : __('messages.add_new') ?? 'Add New' }} {{ __('messages.brand') ?? 'Brand' }}</h3>
                <button type="button" wire:click="$set('isModalOpen', false)" class="text-[var(--color-text-muted)] hover:text-red-500 font-bold text-2xl transition-colors">&times;</button>
            </div>
            
            <form wire:submit.prevent="saveItem" class="flex-1 flex flex-col overflow-hidden">
                <div class="p-6 overflow-y-auto">
                    {{-- ✅ ចាក់បញ្ចូល Layout ដែលបានរៀបចំជា Horizontal (Flexbox) ពីខាងម៉ាស៊ីន Generator --}}
                                        <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.parent_id') ?? 'Parent' }}</label>
                        <div class="w-full md:w-3/4">
                            <select wire:model="parent_id" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors cursor-pointer">
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
                            @error('parent_id') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.name') ?? 'Name' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="text" wire:model="name" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors" placeholder="Enter Name...">
                            @error('name') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
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
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 p-4 bg-[var(--color-background)]/30 rounded-xl border border-[var(--color-border-color)] border-dashed mb-4 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider md:pt-2">{{ __('messages.image') ?? 'Image' }}</label>
                        <div class="w-full md:w-3/4">
                            <div class="flex items-center gap-3">
                                <input type="file" wire:model="image" id="f-image"  accept="image/*" class="hidden">
                                <label for="f-image" class="px-4 py-2 bg-[var(--color-primary)]/10 text-[var(--color-primary)] hover:bg-[var(--color-primary)] hover:text-white text-xs font-bold rounded-lg cursor-pointer transition-colors flex items-center gap-2 shadow-sm">Upload Image</label>
                            </div>
                            @if ($image) <div class="mt-3 flex flex-wrap gap-3"> @if(is_array($image) || is_iterable($image)) @foreach($image as $i => $f) <div class="relative inline-block"><img src="{{ is_string($f) ? asset('storage/'.$f) : $f->temporaryUrl() }}" class="h-20 w-24 rounded-lg border border-[var(--color-border-color)] object-cover"><button type="button" wire:click.stop="removeFile('image', {{ $i }})" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div> @endforeach @else <div class="relative inline-block"><img src="{{ is_string($image) ? asset('storage/'.$image) : $image->temporaryUrl() }}" class="h-24 w-32 rounded-lg border border-[var(--color-border-color)] object-cover"><button type="button" wire:click.stop="$set('image', null)" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div> @endif </div> @endif
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 p-4 bg-[var(--color-background)]/30 rounded-xl border border-[var(--color-border-color)] border-dashed mb-4 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider md:pt-2">{{ __('messages.images') ?? 'Images' }}</label>
                        <div class="w-full md:w-3/4">
                            <div class="flex items-center gap-3">
                                <input type="file" wire:model="images" id="f-images" multiple accept="image/*" class="hidden">
                                <label for="f-images" class="px-4 py-2 bg-[var(--color-primary)]/10 text-[var(--color-primary)] hover:bg-[var(--color-primary)] hover:text-white text-xs font-bold rounded-lg cursor-pointer transition-colors flex items-center gap-2 shadow-sm">Upload Images</label>
                            </div>
                            @if ($images) <div class="mt-3 flex flex-wrap gap-3"> @if(is_array($images) || is_iterable($images)) @foreach($images as $i => $f) <div class="relative inline-block"><img src="{{ is_string($f) ? asset('storage/'.$f) : $f->temporaryUrl() }}" class="h-20 w-24 rounded-lg border border-[var(--color-border-color)] object-cover"><button type="button" wire:click.stop="removeFile('images', {{ $i }})" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div> @endforeach @else <div class="relative inline-block"><img src="{{ is_string($images) ? asset('storage/'.$images) : $images->temporaryUrl() }}" class="h-24 w-32 rounded-lg border border-[var(--color-border-color)] object-cover"><button type="button" wire:click.stop="$set('images', null)" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div> @endif </div> @endif
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