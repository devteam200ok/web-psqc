<?php

namespace App\Livewire;

use Livewire\Component;

class HomePrivacy extends Component
{
    public function render()
    {
        return view('livewire.home-privacy')
            ->layout('layouts.auth');
    }
}
