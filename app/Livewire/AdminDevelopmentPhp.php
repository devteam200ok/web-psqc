<?php

namespace App\Livewire;

use Livewire\Component;

class AdminDevelopmentPhp extends Component
{
    public function render()
    {
        return view('livewire.admin-development-php')
            ->layout('layouts.admin');
    }
}
