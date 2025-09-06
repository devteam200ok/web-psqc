<?php

namespace App\Livewire;

use Livewire\Component;

class HomeTerms extends Component
{
    public function render()
    {
        return view('livewire.home-terms')
            ->layout('layouts.auth');
    }
}
