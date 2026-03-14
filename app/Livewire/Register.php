<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User; // ហៅ Model User មកប្រើ
use Illuminate\Support\Facades\Hash; // សម្រាប់បំប្លែង Password
use Illuminate\Support\Facades\Auth; // សម្រាប់ Login ដោយស្វ័យប្រវត្តិ

class Register extends Component
{
    // អថេរសម្រាប់ចាប់ទិន្នន័យពី Form
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = ''; // សម្រាប់ផ្ទៀងផ្ទាត់លេខសម្ងាត់ម្តងទៀត

    public function register()
    {
        // ១. ត្រួតពិនិត្យទិន្នន័យ (Validation) មុននឹងរក្សាទុក
        $this->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email', // ត្រូវប្រាកដថា email មិនជាន់គ្នា
            'password' => 'required|min:6|confirmed', // confirmed គឺវាទាមទារ $password_confirmation ឱ្យដូចគ្នា
        ]);

        // ២. បង្កើតគណនីថ្មីចូលទៅក្នុង Database
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password), // បំប្លែងលេខសម្ងាត់ឱ្យមានសុវត្ថិភាព
        ]);

        // ៣. ឱ្យ User នោះ Login ដោយស្វ័យប្រវត្តិបន្ទាប់ពីចុះឈ្មោះរួច
        Auth::login($user);

        // ៤. បញ្ជូនទៅកាន់ទំព័រ Dashboard (ដោយមិនបាច់ Refresh Page)
        return $this->redirect('/dashboard', navigate: true);
    }

    public function render()
    {
        return view('livewire.register');
    }
}