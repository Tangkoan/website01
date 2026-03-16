<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Title;

#[Title('Change Password')]
class ChangePassword extends Component
{
    public $current_password;
    public $password;
    public $password_confirmation;

    public function updatePassword()
    {
        // 1. ត្រួតពិនិត្យទិន្នន័យ (ប្រើ Localization សម្រាប់ Custom Messages)
        $this->validate([
            'current_password' => ['required', 'current_password'], 
            'password' => ['required', 'min:8', 'confirmed'],      
        ], [
            'current_password.required' => __('messages.current_password_req'),
            'current_password.current_password' => __('messages.current_password_err'),
            'password.required' => __('messages.new_password_req'),
            'password.min' => __('messages.new_password_min'),
            'password.confirmed' => __('messages.password_unmatched'),
        ]);

        try {
            // 2. ធ្វើបច្ចុប្បន្នភាពលេខសម្ងាត់ចូល Database
            auth()->user()->update([
                'password' => Hash::make($this->password),
            ]);

            // 3. សម្អាតប្រអប់ Input វិញក្រោយពេល Save ជោគជ័យ
            $this->reset(['current_password', 'password', 'password_confirmation']);

            // 4. បង្ហាញ Toast Message (Success)
            $this->dispatch('notify', 
                type: 'success', 
                message: __('messages.password_updated_success'),
                duration: 4000
            );
        } catch (\Exception $e) {
            // 5. បង្ហាញ Toast Message (Error) ក្រែងមានបញ្ហាបច្ចេកទេស
            $this->dispatch('notify', 
                type: 'error', 
                message: __('messages.password_update_error'),
                duration: 4000
            );
        }
    }

    public function render()
    {
        return view('livewire.settings.change-password');
    }
}