<?php

namespace App\Livewire;

use Livewire\Component;

class AdminDashboard extends Component
{
    public function mount()
    {

    }

    public function render()
    {
        return view('livewire.admin-dashboard')
            ->layout('layouts.admin');
    }
}
