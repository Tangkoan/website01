<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Storage;

#[Title('My Profile')]
class Profile extends Component
{
    use WithFileUploads; 

    public $name;
    public $email;
    public $photo; 
    public $existing_photo; 

    public function mount()
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->existing_photo = $user->image; 
    }

    public function updateProfile()
    {
        $this->validate([
            'name' => 'required|min:2|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'photo' => 'nullable|image|max:2048', 
        ]);

        try {
            $user = auth()->user();
            
            // កំណត់ទិន្នន័យផ្ទាល់ ដើម្បីចៀសវាងបញ្ហា Mass Assignment
            $user->name = $this->name;
            $user->email = $this->email;

            if ($this->photo) {
                if ($user->image) {
                    Storage::disk('public')->delete($user->image);
                }
                
                $path = $this->photo->store('profile-photos', 'public');
                $user->image = $path;
                $this->existing_photo = $path; 
            }

            // ប្រើ save() ដើម្បីបញ្ជូនទិន្នន័យចូល Database
            $user->save();

            $this->dispatch('profile-updated');

            // បាញ់ Toast Message ដោយប្រើភាសា (Localization)
            $this->dispatch('notify', 
                type: 'success', 
                message: __('messages.profile_updated_success'),
                duration: 4000
            );

        } catch (\Exception $e) {
            // បាញ់ Toast Error ប្រសិនបើមានបញ្ហា
            $this->dispatch('notify', 
                type: 'error', 
                message: __('messages.profile_update_error'),
                duration: 4000
            );
        }
    }

    public function render()
    {
        return view('livewire.settings.profile')->title(__('messages.profile'));
    }
}