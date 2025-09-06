<?php

namespace App\Livewire;

use Livewire\Component;

class HomePricing extends Component
{
    public function render()
    {
        return view('livewire.home-pricing')
        ->layout('layouts.app');
    }
}