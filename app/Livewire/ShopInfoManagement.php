<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\ShopInfoService;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

use App\Models\ShopInfo;
use Illuminate\Support\Facades\Gate;

class ShopInfoManagement extends Component
{
    use WithPagination;
    use WithFileUploads;


    public $itemId;
    
    // Single Form Auto-generated fields
    public $site_name;
    public $site_tagline;
    public $logo;
    public $favicon;
    public $phone;
    public $email;
    public $address;
    public $facebook_url;
    public $youtube_url;
    public $twitter_url;
    public $meta_title;
    public $meta_description;
    public $og_image;
    public $google_site_verification;
    public $google_analytics;
    public $adsense_script;
    public $adsterra_script;
    public $ad_top_banner;
    public $ad_sidebar_banner;
    public $ad_in_article_banner;
    public $adskeeper_widget;
    
    // Bulk Form Auto-generated fields
    public $bulkItem_site_name;
    public $bulkItem_site_tagline;
    public $bulkItem_logo;
    public $bulkItem_favicon;
    public $bulkItem_phone;
    public $bulkItem_email;
    public $bulkItem_address;
    public $bulkItem_facebook_url;
    public $bulkItem_youtube_url;
    public $bulkItem_twitter_url;
    public $bulkItem_meta_title;
    public $bulkItem_meta_description;
    public $bulkItem_og_image;
    public $bulkItem_google_site_verification;
    public $bulkItem_google_analytics;
    public $bulkItem_adsense_script;
    public $bulkItem_adsterra_script;
    public $bulkItem_ad_top_banner;
    public $bulkItem_ad_sidebar_banner;
    public $bulkItem_ad_in_article_banner;
    public $bulkItem_adskeeper_widget;
    
    public $isModalOpen = false, $searchTerm = '', $perPage = 10;
    public $sortField = 'id', $sortDirection = 'desc';
    
    public $availableColumns = ['site_name' => 'Site Name', 'site_tagline' => 'Site Tagline', 'logo' => 'Logo', 'favicon' => 'Favicon', 'phone' => 'Phone', 'email' => 'Email', 'address' => 'Address', 'facebook_url' => 'Facebook Url', 'youtube_url' => 'Youtube Url', 'twitter_url' => 'Twitter Url', 'meta_title' => 'Meta Title', 'meta_description' => 'Meta Description', 'og_image' => 'Og Image', 'google_site_verification' => 'Google Site Verification', 'google_analytics' => 'Google Analytics', 'adsense_script' => 'Adsense Script', 'adsterra_script' => 'Adsterra Script', 'ad_top_banner' => 'Ad Top Banner', 'ad_sidebar_banner' => 'Ad Sidebar Banner', 'ad_in_article_banner' => 'Ad In Article Banner', 'adskeeper_widget' => 'Adskeeper Widget'];
    public $selectedColumns = ['site_name', 'site_tagline', 'logo', 'favicon', 'phone', 'email', 'address', 'facebook_url', 'youtube_url', 'twitter_url', 'meta_title', 'meta_description', 'og_image', 'google_site_verification', 'google_analytics', 'adsense_script', 'adsterra_script', 'ad_top_banner', 'ad_sidebar_banner', 'ad_in_article_banner', 'adskeeper_widget']; 
    
    public $selectedItems = [], $selectAll = false;
    public $isDeleteModalOpen = false, $deleteId = null;

    public $isBulkEditModalOpen = false;
    public $selectedItemsQueue = []; 
    public $currentBulkIndex = 0;
    public $bulkItemId;

    protected $queryString = ['searchTerm', 'perPage'];

    protected function service() { return app(ShopInfoService::class); }

    public function updatedSelectAll($value) {
        if ($value) {
            $this->selectedItems = $this->service()->getItems($this->searchTerm, 'all', $this->sortField, $this->sortDirection)->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    public function sortBy($field) {
        $this->sortDirection = ($this->sortField === $field && $this->sortDirection === 'asc') ? 'desc' : 'asc';
        $this->sortField = $field;
    }

    // --- Bulk Edit ---
    public function bulkEdit() {
        abort_if(Gate::denies('edit-shop-info'), 403);
        if (empty($this->selectedItems)) return;
        $this->selectedItemsQueue = array_values($this->selectedItems);
        $this->currentBulkIndex = 0;
        $this->loadBulkItemData($this->currentBulkIndex);
        $this->isBulkEditModalOpen = true;
    }

    private function loadBulkItemData($index) {
        if (!isset($this->selectedItemsQueue[$index])) return;
        $item = ShopInfo::find($this->selectedItemsQueue[$index]);
        if ($item) {
            $this->bulkItemId = $item->id;
            $this->bulkItem_site_name = $item->site_name;
            $this->bulkItem_site_tagline = $item->site_tagline;
            $this->bulkItem_logo = is_string($item->logo) ? json_decode($item->logo, true) ?? $item->logo : $item->logo;
            $this->bulkItem_favicon = is_string($item->favicon) ? json_decode($item->favicon, true) ?? $item->favicon : $item->favicon;
            $this->bulkItem_phone = $item->phone;
            $this->bulkItem_email = $item->email;
            $this->bulkItem_address = $item->address;
            $this->bulkItem_facebook_url = $item->facebook_url;
            $this->bulkItem_youtube_url = $item->youtube_url;
            $this->bulkItem_twitter_url = $item->twitter_url;
            $this->bulkItem_meta_title = $item->meta_title;
            $this->bulkItem_meta_description = $item->meta_description;
            $this->bulkItem_og_image = is_string($item->og_image) ? json_decode($item->og_image, true) ?? $item->og_image : $item->og_image;
            $this->bulkItem_google_site_verification = $item->google_site_verification;
            $this->bulkItem_google_analytics = $item->google_analytics;
            $this->bulkItem_adsense_script = $item->adsense_script;
            $this->bulkItem_adsterra_script = $item->adsterra_script;
            $this->bulkItem_ad_top_banner = is_string($item->ad_top_banner) ? json_decode($item->ad_top_banner, true) ?? $item->ad_top_banner : $item->ad_top_banner;
            $this->bulkItem_ad_sidebar_banner = is_string($item->ad_sidebar_banner) ? json_decode($item->ad_sidebar_banner, true) ?? $item->ad_sidebar_banner : $item->ad_sidebar_banner;
            $this->bulkItem_ad_in_article_banner = is_string($item->ad_in_article_banner) ? json_decode($item->ad_in_article_banner, true) ?? $item->ad_in_article_banner : $item->ad_in_article_banner;
            $this->bulkItem_adskeeper_widget = $item->adskeeper_widget;
        }
    }

    public function jumpToBulkItem($index) {
        $this->currentBulkIndex = $index;
        $this->loadBulkItemData($index);
        $this->resetErrorBag();
    }

    public function skipBulkItem() { $this->moveToNextBulkItem(); }

    public function saveAndNextBulkItem() {
        abort_if(Gate::denies('edit-shop-info'), 403);
        $this->validate([
            'bulkItem_site_name' => 'nullable|string|max:255',
            'bulkItem_site_tagline' => 'nullable|string|max:255',
            'bulkItem_logo' => 'nullable',
            'bulkItem_favicon' => 'nullable',
            'bulkItem_phone' => 'nullable|string|max:255',
            'bulkItem_email' => 'nullable|string|max:255',
            'bulkItem_address' => 'nullable|string',
            'bulkItem_facebook_url' => 'nullable|string|max:255',
            'bulkItem_youtube_url' => 'nullable|string|max:255',
            'bulkItem_twitter_url' => 'nullable|string|max:255',
            'bulkItem_meta_title' => 'nullable|string|max:255',
            'bulkItem_meta_description' => 'nullable|string',
            'bulkItem_og_image' => 'nullable',
            'bulkItem_google_site_verification' => 'nullable|string|max:255',
            'bulkItem_google_analytics' => 'nullable|string',
            'bulkItem_adsense_script' => 'nullable|string',
            'bulkItem_adsterra_script' => 'nullable|string',
            'bulkItem_ad_top_banner' => 'nullable',
            'bulkItem_ad_sidebar_banner' => 'nullable',
            'bulkItem_ad_in_article_banner' => 'nullable',
            'bulkItem_adskeeper_widget' => 'nullable|string',
        ]);
        $this->service()->saveItem([
            'site_name' => $this->bulkItem_site_name,
            'site_tagline' => $this->bulkItem_site_tagline,
            'logo' => empty($this->bulkItem_logo) ? null : (is_string($this->bulkItem_logo) ? $this->bulkItem_logo : $this->bulkItem_logo->store('uploads/{{modelNameLower}}', 'public')),
            'favicon' => empty($this->bulkItem_favicon) ? null : (is_string($this->bulkItem_favicon) ? $this->bulkItem_favicon : $this->bulkItem_favicon->store('uploads/{{modelNameLower}}', 'public')),
            'phone' => $this->bulkItem_phone,
            'email' => $this->bulkItem_email,
            'address' => $this->bulkItem_address,
            'facebook_url' => $this->bulkItem_facebook_url,
            'youtube_url' => $this->bulkItem_youtube_url,
            'twitter_url' => $this->bulkItem_twitter_url,
            'meta_title' => $this->bulkItem_meta_title,
            'meta_description' => $this->bulkItem_meta_description,
            'og_image' => empty($this->bulkItem_og_image) ? null : (is_string($this->bulkItem_og_image) ? $this->bulkItem_og_image : $this->bulkItem_og_image->store('uploads/{{modelNameLower}}', 'public')),
            'google_site_verification' => $this->bulkItem_google_site_verification,
            'google_analytics' => $this->bulkItem_google_analytics,
            'adsense_script' => $this->bulkItem_adsense_script,
            'adsterra_script' => $this->bulkItem_adsterra_script,
            'ad_top_banner' => empty($this->bulkItem_ad_top_banner) ? null : (is_string($this->bulkItem_ad_top_banner) ? $this->bulkItem_ad_top_banner : $this->bulkItem_ad_top_banner->store('uploads/{{modelNameLower}}', 'public')),
            'ad_sidebar_banner' => empty($this->bulkItem_ad_sidebar_banner) ? null : (is_string($this->bulkItem_ad_sidebar_banner) ? $this->bulkItem_ad_sidebar_banner : $this->bulkItem_ad_sidebar_banner->store('uploads/{{modelNameLower}}', 'public')),
            'ad_in_article_banner' => empty($this->bulkItem_ad_in_article_banner) ? null : (is_string($this->bulkItem_ad_in_article_banner) ? $this->bulkItem_ad_in_article_banner : $this->bulkItem_ad_in_article_banner->store('uploads/{{modelNameLower}}', 'public')),
            'adskeeper_widget' => $this->bulkItem_adskeeper_widget,
        ], $this->bulkItemId);
        $this->moveToNextBulkItem();
    }

    private function moveToNextBulkItem() {
        $this->resetErrorBag();
        if ($this->currentBulkIndex < count($this->selectedItemsQueue) - 1) {
            $this->currentBulkIndex++;
            $this->loadBulkItemData($this->currentBulkIndex);
        } else {
            $this->closeBulkEdit();
            $this->dispatch('notify', type: 'success', message: __('messages.bulk_edit_completed') ?? 'Bulk edit completed.');
        }
    }

    public function closeBulkEdit() {
        $this->isBulkEditModalOpen = false;
        $this->reset(['selectedItemsQueue', 'currentBulkIndex', 'bulkItemId', 'selectedItems', 'selectAll', 'bulkItem_site_name', 'bulkItem_site_tagline', 'bulkItem_logo', 'bulkItem_favicon', 'bulkItem_phone', 'bulkItem_email', 'bulkItem_address', 'bulkItem_facebook_url', 'bulkItem_youtube_url', 'bulkItem_twitter_url', 'bulkItem_meta_title', 'bulkItem_meta_description', 'bulkItem_og_image', 'bulkItem_google_site_verification', 'bulkItem_google_analytics', 'bulkItem_adsense_script', 'bulkItem_adsterra_script', 'bulkItem_ad_top_banner', 'bulkItem_ad_sidebar_banner', 'bulkItem_ad_in_article_banner', 'bulkItem_adskeeper_widget']);
        $this->resetErrorBag();
    }

    // --- Image Handling ---
    public function removeFile($field, $index) {
        if (is_array($this->$field) && isset($this->$field[$index])) {
            $files = $this->$field;
            unset($files[$index]);
            $this->$field = array_values($files); 
        }
    }

    // --- Single Actions ---
    public function openModal() {
        abort_if(Gate::denies('create-shop-info'), 403);
        $this->reset(['itemId']);
        
        $this->reset([
            'site_name', 'site_tagline', 'logo', 'favicon', 'phone', 'email', 'address', 'facebook_url', 'youtube_url', 'twitter_url', 'meta_title', 'meta_description', 'og_image', 'google_site_verification', 'google_analytics', 'adsense_script', 'adsterra_script', 'ad_top_banner', 'ad_sidebar_banner', 'ad_in_article_banner', 'adskeeper_widget'
        ]);

        $this->resetErrorBag();
        $this->isModalOpen = true;
    }

    public function editItem($id) {
        abort_if(Gate::denies('edit-shop-info'), 403);
        $this->resetErrorBag();
        $item = ShopInfo::findOrFail($id);
        
        $this->itemId = $item->id;
        
        $this->site_name = $item->site_name;
        $this->site_tagline = $item->site_tagline;
        $this->logo = is_string($item->logo) ? json_decode($item->logo, true) ?? $item->logo : $item->logo;
        $this->favicon = is_string($item->favicon) ? json_decode($item->favicon, true) ?? $item->favicon : $item->favicon;
        $this->phone = $item->phone;
        $this->email = $item->email;
        $this->address = $item->address;
        $this->facebook_url = $item->facebook_url;
        $this->youtube_url = $item->youtube_url;
        $this->twitter_url = $item->twitter_url;
        $this->meta_title = $item->meta_title;
        $this->meta_description = $item->meta_description;
        $this->og_image = is_string($item->og_image) ? json_decode($item->og_image, true) ?? $item->og_image : $item->og_image;
        $this->google_site_verification = $item->google_site_verification;
        $this->google_analytics = $item->google_analytics;
        $this->adsense_script = $item->adsense_script;
        $this->adsterra_script = $item->adsterra_script;
        $this->ad_top_banner = is_string($item->ad_top_banner) ? json_decode($item->ad_top_banner, true) ?? $item->ad_top_banner : $item->ad_top_banner;
        $this->ad_sidebar_banner = is_string($item->ad_sidebar_banner) ? json_decode($item->ad_sidebar_banner, true) ?? $item->ad_sidebar_banner : $item->ad_sidebar_banner;
        $this->ad_in_article_banner = is_string($item->ad_in_article_banner) ? json_decode($item->ad_in_article_banner, true) ?? $item->ad_in_article_banner : $item->ad_in_article_banner;
        $this->adskeeper_widget = $item->adskeeper_widget;
        
        $this->isModalOpen = true;
    }

    /**
     * ✅ កែប្រែទៅជា toggleField ដើម្បីឱ្យ Smart ជាងមុន
     * អាចបិទបើកបានគ្រប់ Field ដែលជាប្រភេទ Boolean
     */
    public function toggleField($id, $field) {
        if (Gate::denies('edit-shop-info')) {
            $this->dispatch('notify', type: 'error', message: __('messages.no_permission') ?? 'No permission.');
            return; 
        }
        $item = ShopInfo::findOrFail($id);
        
        // ប្តូរតម្លៃ (Toggle logic)
        $item->$field = !$item->$field;
        $item->save();
        
        $this->dispatch('notify', 
            type: 'success', 
            message: $item->$field ? (__('messages.activated') ?? 'Activated') : (__('messages.deactivated') ?? 'Deactivated')
        );
    }

    public function saveItem() {
        if ($this->itemId) abort_if(\Illuminate\Support\Facades\Gate::denies('edit-shop-info'), 403);
        else abort_if(\Illuminate\Support\Facades\Gate::denies('create-shop-info'), 403);

        $this->validate([
            'site_name' => 'nullable|string|max:255',
            'site_tagline' => 'nullable|string|max:255',
            'logo' => 'nullable',
            'favicon' => 'nullable',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'facebook_url' => 'nullable|string|max:255',
            'youtube_url' => 'nullable|string|max:255',
            'twitter_url' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'og_image' => 'nullable',
            'google_site_verification' => 'nullable|string|max:255',
            'google_analytics' => 'nullable|string',
            'adsense_script' => 'nullable|string',
            'adsterra_script' => 'nullable|string',
            'ad_top_banner' => 'nullable',
            'ad_sidebar_banner' => 'nullable',
            'ad_in_article_banner' => 'nullable',
            'adskeeper_widget' => 'nullable|string',
        ]);

        try {
            $this->service()->saveItem([
                'site_name' => $this->site_name,
            'site_tagline' => $this->site_tagline,
            'logo' => empty($this->logo) ? null : (is_string($this->logo) ? $this->logo : $this->logo->store('uploads/{{modelNameLower}}', 'public')),
            'favicon' => empty($this->favicon) ? null : (is_string($this->favicon) ? $this->favicon : $this->favicon->store('uploads/{{modelNameLower}}', 'public')),
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'facebook_url' => $this->facebook_url,
            'youtube_url' => $this->youtube_url,
            'twitter_url' => $this->twitter_url,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'og_image' => empty($this->og_image) ? null : (is_string($this->og_image) ? $this->og_image : $this->og_image->store('uploads/{{modelNameLower}}', 'public')),
            'google_site_verification' => $this->google_site_verification,
            'google_analytics' => $this->google_analytics,
            'adsense_script' => $this->adsense_script,
            'adsterra_script' => $this->adsterra_script,
            'ad_top_banner' => empty($this->ad_top_banner) ? null : (is_string($this->ad_top_banner) ? $this->ad_top_banner : $this->ad_top_banner->store('uploads/{{modelNameLower}}', 'public')),
            'ad_sidebar_banner' => empty($this->ad_sidebar_banner) ? null : (is_string($this->ad_sidebar_banner) ? $this->ad_sidebar_banner : $this->ad_sidebar_banner->store('uploads/{{modelNameLower}}', 'public')),
            'ad_in_article_banner' => empty($this->ad_in_article_banner) ? null : (is_string($this->ad_in_article_banner) ? $this->ad_in_article_banner : $this->ad_in_article_banner->store('uploads/{{modelNameLower}}', 'public')),
            'adskeeper_widget' => $this->adskeeper_widget,
            ], $this->itemId);

            $this->isModalOpen = false;
            $this->dispatch('notify', type: 'success', message: __('messages.saved_successfully') ?? 'Data saved successfully.');

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '22001' || \Illuminate\Support\Str::contains($e->getMessage(), '1406')) {
                
                preg_match("/column '([^']+)'/", $e->getMessage(), $matches);
                $columnName = $matches[1] ?? '';

                $fieldLabel = __("messages.$columnName");
                if ($fieldLabel == "messages.$columnName") {
                    $fieldLabel = \Illuminate\Support\Str::headline($columnName);
                }

                $this->dispatch('notify', 
                    type: 'error', 
                    message: __('messages.field_data_too_large', ['field' => $fieldLabel]) 
                             ?? "The data in field [$fieldLabel] is too large."
                );
                
                if ($columnName) {
                    $this->addError($columnName, __('messages.data_too_large'));
                }
                
                return;
            }

            throw $e;
        }
    }

    public function confirmDelete($id = null) {
        abort_if(Gate::denies('delete-shop-info'), 403);
        $this->deleteId = $id;
        $this->isDeleteModalOpen = true;
    }

    public function executeDelete() {
        abort_if(Gate::denies('delete-shop-info'), 403);
        $ids = $this->deleteId ?: $this->selectedItems;
        $this->service()->deleteItems($ids);
        $this->reset(['selectedItems', 'selectAll', 'deleteId', 'isDeleteModalOpen']);
        $this->dispatch('notify', type: 'success', message: __('messages.deleted_successfully') ?? 'Deleted successfully.');
    }

    public function reloadData() {
        $this->reset(['searchTerm', 'selectedItems', 'selectAll']);
        $this->resetPage(); 
        $this->dispatch('notify', type: 'success', message: __('messages.data_reloaded') ?? 'Data reloaded.');
    }

    public function render() {
        return view('livewire.shop-info.shop-info-management', [
            'items' => $this->service()->getItems($this->searchTerm, $this->perPage, $this->sortField, $this->sortDirection),
        ])->title(__('messages.shop-info_management') ?? 'ShopInfos Management');
    }
}