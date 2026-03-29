<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    public $email = '';
    public $password = '';
    public $errorMessage = '';

    public function login()
    {
        $this->validate([
            'email' => 'required', 
            'password' => 'required',
        ]);

        $fieldType = filter_var($this->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        if (Auth::attempt([$fieldType => $this->email, 'password' => $this->password])) {
            $user = Auth::user();

            // ១. ឆែកមើលថាតើគណនីនេះស្ថិតក្នុង Trash ដែរឬទេ (deleted_at មានទិន្នន័យ)
            if ($user->deleted_at !== null) {
                Auth::logout(); 
                $this->errorMessage = __('messages.account_deleted'); // ប្រើភាសា
                return; 
            }

            // ២. ឆែកមើលថាតើគណនីនេះត្រូវបានបិទដែរឬទេ (status == 0)
            if ($user->status == 0) {
                Auth::logout(); 
                $this->errorMessage = __('messages.account_disabled'); // ប្រើភាសា
                return; 
            }

            // បើគណនីធម្មតា គ្មានបញ្ហា ឱ្យចូល Dashboard
            return $this->redirect('/dashboard', navigate: true);
        } else {
            // បើខុសឈ្មោះ ឬ លេខសម្ងាត់
            $this->errorMessage = __('messages.invalid_credentials');
        }
    }

    public function render()
    {
        return view('livewire.login');
    }
}