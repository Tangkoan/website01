<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    // អថេរសម្រាប់ចាប់ទិន្នន័យពី Form
    public $email = '';
    public $password = '';
    public $errorMessage = '';

    // មុខងារនេះដំណើរការពេលគេចុចប៊ូតុង Login
    public function login()
    {
        // ១. ត្រួតពិនិត្យទិន្នន័យ (លុបច្បាប់ 'email' ចេញ ដើម្បីកុំឱ្យវាលោត Error ពេលគេវាយឈ្មោះធម្មតា)
        $this->validate([
            'email' => 'required', 
            'password' => 'required',
        ]);

        // ២. ឆែកមើលថាតើទិន្នន័យដែលគេបញ្ចូលជា Email ឬ Name
        // បើវាមានទម្រង់ជាអ៊ីមែលត្រឹមត្រូវ វាប្រើ column 'email', តែបើមិនមែនទេ វាប្រើ column 'name'
        $fieldType = filter_var($this->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        // ៣. សាកល្បង Login ជាមួយ Database
        if (Auth::attempt([$fieldType => $this->email, 'password' => $this->password])) {
            // បើជោគជ័យ ឱ្យវាលោតទៅទំព័រ dashboard 
            // (ចំណាំ៖ ខ្ញុំបានដក navigate: true ចេញ ដើម្បីការពារកុំឱ្យវាគាំងដូចបញ្ហា Logout ដែលយើងជួបមុននេះ)
            // return $this->redirect('/dashboard'); 

            // បើជោគជ័យ ឱ្យវាលោតទៅទំព័រ dashboard ដោយមិន Refresh page
            return $this->redirect('/dashboard', navigate: true);
        } else {
            // បើខុស បង្ហាញសារកំហុស
            $this->errorMessage = 'Name/Email or password is wrong!';
        }
    }

    public function render()
    {
        return view('livewire.login');
    }
}