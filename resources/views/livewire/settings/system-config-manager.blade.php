<div class="w-full max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 md:py-8 space-y-4 sm:space-y-6">
    
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-[var(--color-border-color)] pb-4">
        <h2 class="text-xl sm:text-2xl font-black text-[var(--color-text-main)] flex items-center gap-3">
            <span class="p-2 bg-[var(--color-primary)]/10 rounded-lg md:rounded-xl text-[var(--color-primary)] text-xl md:text-2xl">⚙️</span> 
            {{ __('messages.system_configs_title') }}
        </h2>
        
        @can('manage_system_configs')
        <button wire:click="saveSettings" class="w-full sm:w-auto justify-center px-6 py-2.5 sm:py-3 bg-[var(--color-primary)] text-white font-bold rounded-lg hover:brightness-110 shadow-sm transition-all flex items-center gap-2">
            <svg wire:loading wire:target="saveSettings" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            <span>💾 {{ __('messages.save') }}</span>
        </button>
        @endcan
    </div>

    <div class="flex flex-col md:flex-row gap-4 sm:gap-6">
        
        {{-- Sidebar Tabs (Groups) - Responsive: Horizontal scroll on Mobile, Vertical on Desktop --}}
        <div class="w-full md:w-64 shrink-0 flex flex-col gap-2 sm:gap-4">
            <div class="flex md:flex-col overflow-x-auto gap-2 pb-2 md:pb-0 scrollbar-hide" style="-ms-overflow-style: none; scrollbar-width: none;">
                @foreach($groups as $group)
                    <button wire:click="$set('activeTab', '{{ $group }}')" 
                        class="whitespace-nowrap md:whitespace-normal text-left px-4 py-2.5 md:py-3 rounded-lg font-bold text-sm md:text-base transition-all {{ $activeTab === $group ? 'bg-[var(--color-primary)]/10 text-[var(--color-primary)] border border-[var(--color-primary)] shadow-sm' : 'bg-[var(--color-card-bg)] text-[var(--color-text-muted)] hover:bg-[var(--color-background)] border border-[var(--color-border-color)]' }}">
                        📁 {{ ucfirst($group) }}
                    </button>
                @endforeach
            </div>

            {{-- Button Add New Config --}}
            @can('manage_system_configs')
            <button wire:click="$set('showBuilder', true)" class="w-full text-center px-4 py-2.5 md:py-3 border-2 border-dashed border-[var(--color-primary)] text-[var(--color-primary)] rounded-lg font-bold text-sm md:text-base hover:bg-[var(--color-primary)]/5 transition-all">
                + {{ __('messages.add_new_builder') }}
            </button>
            @endcan
        </div>

        {{-- Main Form Content (Dynamic Input) --}}
        <div class="flex-1 bg-[var(--color-card-bg)] rounded-xl border border-[var(--color-border-color)] shadow-sm p-4 sm:p-6">
            <h3 class="text-base sm:text-lg font-bold mb-4 sm:mb-6 text-[var(--color-text-main)] capitalize border-b border-[var(--color-border-color)] pb-2">{{ __('messages.settings_for') }} {{ $activeTab }}</h3>
            
            <div class="space-y-6">
                @forelse($configs as $config)
                    @php $safeKey = str_replace('.', '_', $config->key); @endphp
                    
                    <div wire:key="config-item-{{ $config->key }}" class="flex flex-col sm:flex-row sm:items-start gap-2 sm:gap-6 border-b border-[var(--color-border-color)] pb-5 last:border-0 last:pb-0">
                        
                        {{-- Label, Key និងប៊ូតុងលុប --}}
                        <div class="sm:w-1/3 flex items-start justify-between sm:pr-2">
                            <div class="flex-1 pr-2">
                                <label class="block text-sm font-bold text-[var(--color-text-main)] mb-1 sm:mb-0">{{ $config->name }}</label>
                                <span class="inline-block text-[10px] sm:text-xs bg-[var(--color-background)] px-2 py-0.5 rounded text-[var(--color-text-muted)] font-mono break-all">{{ $config->key }}</span>
                            </div>
                            
                            @can('manage_system_configs')
                            <button wire:click="confirmDelete('{{ $config->key }}', '{{ $config->name }}')" 
                                    class="shrink-0 text-red-400 hover:text-red-600 bg-red-50 hover:bg-red-100 p-1.5 sm:p-2 rounded-md transition-all" title="{{ __('messages.delete') }}">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                            @endcan
                        </div>
                        
                        {{-- Dynamic Inputs ឆ្លាតវៃ --}}
                        <div class="sm:w-2/3 mt-1 sm:mt-0">
                            @switch($config->type)
                                @case('string')
                                    <input type="text" wire:model="formValues.{{ $safeKey }}" class="w-full px-3 py-2 text-sm rounded-lg border border-[var(--color-border-color)] bg-[var(--color-background)] focus:ring-2 focus:ring-[var(--color-primary)]/50 focus:outline-none text-[var(--color-text-main)]" {{ auth()->user()->cannot('manage_system_configs') ? 'disabled' : '' }}>
                                    @break
                                    
                                @case('text')
                                    <textarea wire:model="formValues.{{ $safeKey }}" rows="3" class="w-full px-3 py-2 text-sm rounded-lg border border-[var(--color-border-color)] bg-[var(--color-background)] focus:ring-2 focus:ring-[var(--color-primary)]/50 focus:outline-none text-[var(--color-text-main)]" {{ auth()->user()->cannot('manage_system_configs') ? 'disabled' : '' }}></textarea>
                                    @break
                                    
                                @case('boolean')
                                    <select wire:model="formValues.{{ $safeKey }}" class="w-full sm:w-auto px-3 py-2 text-sm rounded-lg border border-[var(--color-border-color)] bg-[var(--color-background)] text-[var(--color-text-main)]" {{ auth()->user()->cannot('manage_system_configs') ? 'disabled' : '' }}>
                                        <option value="1">{{ __('messages.enable') }}</option>
                                        <option value="0">{{ __('messages.disable') }}</option>
                                    </select>
                                    @break
                                    
                                @case('number')
                                    <input type="number" wire:model="formValues.{{ $safeKey }}" class="w-full px-3 py-2 text-sm rounded-lg border border-[var(--color-border-color)] bg-[var(--color-background)] focus:ring-2 focus:ring-[var(--color-primary)]/50 focus:outline-none text-[var(--color-text-main)]" {{ auth()->user()->cannot('manage_system_configs') ? 'disabled' : '' }}>
                                    @break
                                    
                                @case('color')
                                    <div class="flex items-center gap-3">
                                        <input type="color" wire:model="formValues.{{ $safeKey }}" class="w-10 h-10 sm:w-12 sm:h-10 rounded cursor-pointer border-0 p-0" {{ auth()->user()->cannot('manage_system_configs') ? 'disabled' : '' }}>
                                        <span class="text-sm text-[var(--color-text-muted)] font-mono bg-[var(--color-background)] px-2 py-1 rounded">{{ $formValues[$safeKey] ?? '#000000' }}</span>
                                    </div>
                                    @break
                                    
                                @case('image')
                                    <div class="flex flex-row items-start sm:items-center gap-3 sm:gap-4 w-full">
                                        @can('manage_system_configs')
                                        <div class="flex-1 min-w-0 flex flex-col gap-2">
                                            
                                            {{-- Input សម្រាប់ Link URL --}}
                                            @if(!isset($formValues[$safeKey]) || !is_object($formValues[$safeKey]))
                                                <input type="text" wire:model="formValues.{{ $safeKey }}" placeholder="{{ __('messages.image_url_placeholder') }}" class="w-full px-3 py-2 text-sm rounded-lg border border-[var(--color-border-color)] bg-[var(--color-background)] focus:ring-2 focus:ring-[var(--color-primary)]/50 focus:outline-none text-[var(--color-text-main)]">
                                            @else
                                                <div class="px-3 py-2 text-sm rounded-lg border border-green-500 bg-green-50 dark:bg-green-500/10 text-green-700 dark:text-green-400 flex items-center gap-2">
                                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> 
                                                    <span class="truncate">{{ __('messages.file_ready_to_save') }}</span>
                                                </div>
                                            @endif

                                            {{-- Input សម្រាប់ Upload File --}}
                                            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2">
                                                <span class="text-[10px] font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.or_upload_file') }}</span>
                                                <input type="file" wire:model="formValues.{{ $safeKey }}" class="w-full text-xs sm:text-sm file:mr-2 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:font-semibold file:bg-[var(--color-primary)]/10 file:text-[var(--color-primary)] hover:file:bg-[var(--color-primary)]/20 text-[var(--color-text-muted)] cursor-pointer">
                                            </div>
                                        </div>
                                        @endcan
                                        
                                        {{-- ផ្នែកបង្ហាញរូបភាព (Preview Image) --}}
                                        <div class="shrink-0 pt-1 sm:pt-0">
                                            @php
                                                $previewUrl = '';
                                                if (isset($formValues[$safeKey])) {
                                                    if (is_object($formValues[$safeKey])) {
                                                        try { $previewUrl = $formValues[$safeKey]->temporaryUrl(); } catch(\Exception $e) {}
                                                    } elseif (is_string($formValues[$safeKey]) && $formValues[$safeKey] !== '') {
                                                        $previewUrl = str_starts_with($formValues[$safeKey], 'http') ? $formValues[$safeKey] : asset('storage/' . $formValues[$safeKey]);
                                                    }
                                                } elseif (!empty($config->value) && is_string($config->value)) {
                                                    $previewUrl = str_starts_with($config->value, 'http') ? $config->value : asset('storage/' . $config->value);
                                                }
                                            @endphp
                                            
                                            @if($previewUrl)
                                                <img src="{{ $previewUrl }}" class="h-16 w-16 md:h-20 md:w-20 object-cover rounded-lg border border-[var(--color-border-color)] shadow-sm bg-[var(--color-background)]" alt="Preview">
                                            @else
                                                <div class="h-16 w-16 md:h-20 md:w-20 rounded-lg border border-dashed border-[var(--color-border-color)] bg-[var(--color-background)] flex items-center justify-center text-[var(--color-text-muted)] opacity-50">
                                                    <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    @break
                                    
                                @case('select')
                                    <select wire:model="formValues.{{ $safeKey }}" class="w-full px-3 py-2 text-sm rounded-lg border border-[var(--color-border-color)] bg-[var(--color-background)] focus:ring-2 focus:ring-[var(--color-primary)]/50 focus:outline-none text-[var(--color-text-main)]" {{ auth()->user()->cannot('manage_system_configs') ? 'disabled' : '' }}>
                                        <option value="">-- {{ __('messages.please_select') }} --</option>
                                        @if(is_array($config->options))
                                            @foreach($config->options as $val => $label)
                                                <option value="{{ $val }}">{{ $label }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @break
                            @endswitch
                        </div>
                    </div>
                @empty
                    <div class="text-center text-[var(--color-text-muted)] py-8 sm:py-12 flex flex-col items-center justify-center bg-[var(--color-background)] rounded-lg border border-dashed border-[var(--color-border-color)]">
                        <svg class="w-10 h-10 sm:w-12 sm:h-12 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        <p class="text-sm sm:text-base">{{ __('messages.no_configs_in_group') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Modal Builder (Alpine JS & Livewire) --}}
    @can('manage_system_configs')
    <div x-data="{ open: @entangle('showBuilder') }" x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4" x-cloak>
        <div @click.away="open = false" class="bg-[var(--color-card-bg)] w-full max-w-lg p-5 sm:p-6 rounded-2xl shadow-xl border border-[var(--color-border-color)] max-h-[90vh] overflow-y-auto">
            <h3 class="text-lg sm:text-xl font-bold mb-4 text-[var(--color-text-main)] flex items-center gap-2">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-[var(--color-primary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                {{ __('messages.create_new_config') }}
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs sm:text-sm font-bold mb-1 text-[var(--color-text-main)]">{{ __('messages.group_category') }}</label>
                    <input type="text" wire:model="newConfig.group" placeholder="{{ __('messages.eg_group') }}" class="w-full px-3 py-2 text-sm rounded border border-[var(--color-border-color)] bg-[var(--color-background)] text-[var(--color-text-main)] focus:ring-1 focus:ring-[var(--color-primary)] focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs sm:text-sm font-bold mb-1 text-[var(--color-text-main)]">{{ __('messages.name_display') }}</label>
                    <input type="text" wire:model="newConfig.name" placeholder="{{ __('messages.eg_name') }}" class="w-full px-3 py-2 text-sm rounded border border-[var(--color-border-color)] bg-[var(--color-background)] text-[var(--color-text-main)] focus:ring-1 focus:ring-[var(--color-primary)] focus:outline-none">
                    @error('newConfig.name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs sm:text-sm font-bold mb-1 text-[var(--color-text-main)]">{{ __('messages.key_identifier') }}</label>
                    <input type="text" wire:model="newConfig.key" placeholder="{{ __('messages.eg_key') }}" class="w-full px-3 py-2 text-sm rounded border border-[var(--color-border-color)] bg-[var(--color-background)] text-[var(--color-text-main)] focus:ring-1 focus:ring-[var(--color-primary)] focus:outline-none">
                    @error('newConfig.key') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs sm:text-sm font-bold mb-1 text-[var(--color-text-main)]">{{ __('messages.input_type') }}</label>
                    <select wire:model.live="newConfig.type" class="w-full px-3 py-2 text-sm rounded border border-[var(--color-border-color)] bg-[var(--color-background)] text-[var(--color-text-main)] focus:ring-1 focus:ring-[var(--color-primary)] focus:outline-none">
                        <option value="string">{{ __('messages.type_string') }}</option>
                        <option value="text">{{ __('messages.type_text') }}</option>
                        <option value="number">{{ __('messages.type_number') }}</option>
                        <option value="boolean">{{ __('messages.type_boolean') }}</option>
                        <option value="select">{{ __('messages.type_select') }}</option>
                        <option value="color">{{ __('messages.type_color') }}</option>
                        <option value="image">{{ __('messages.type_image') }}</option>
                    </select>
                </div>
                
                @if($newConfig['type'] === 'select')
                    <div class="bg-[var(--color-primary)]/5 p-3 rounded-lg border border-[var(--color-primary)]/20">
                        <label class="block text-xs sm:text-sm font-bold mb-1 text-[var(--color-primary)]">{{ __('messages.options_format') }}</label>
                        <input type="text" wire:model="newConfig.options" placeholder="{{ __('messages.eg_options') }}" class="w-full px-3 py-2 text-sm rounded border border-[var(--color-border-color)] bg-white focus:outline-none">
                        <p class="text-[10px] text-gray-500 mt-1 leading-tight">{{ __('messages.options_hint') }}</p>
                    </div>
                @endif
            </div>

            <div class="mt-6 flex flex-col-reverse sm:flex-row justify-end gap-2 sm:gap-3 border-t border-[var(--color-border-color)] pt-4">
                <button @click="open = false" class="w-full sm:w-auto px-5 py-2.5 sm:py-2 text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">{{ __('messages.cancel') }}</button>
                <button wire:click="createConfig" class="w-full sm:w-auto justify-center px-5 py-2.5 sm:py-2 text-sm font-bold text-white bg-[var(--color-primary)] hover:brightness-110 rounded-lg transition-all flex items-center">
                    <svg wire:loading wire:target="createConfig" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    {{ __('messages.create_new') }}
                </button>
            </div>
        </div>
    </div>
    @endcan

    {{-- ការហៅប្រើ Component Modal លុប របស់អ្នក --}}
    <x-modals.delete 
        :isOpen="$showDeleteModal" 
        onClose="cancelDelete" 
        onConfirm="executeDelete" 
        title="{{ __('messages.confirm_delete') ?? 'Confirm Delete' }}" 
        message="{!! __('messages.delete_confirm', ['name' => $configNameToDelete]) !!}" 
    />

</div>