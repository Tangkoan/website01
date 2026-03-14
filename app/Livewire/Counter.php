<?php

namespace App\Livewire;

use Livewire\Component;

class Counter extends Component
{
    public $count = 0; // កំណត់តម្លៃចាប់ផ្តើមស្មើ ០

    public function increment()
    {
        $this->count++; // បូកបញ្ជូល ១ ពេលគេហៅមុខងារនេះ
    }

    public function render()
    {
        return view('livewire.counter');
    }
}