<?php

namespace App\Livewire;

use Livewire\Component;

class HomeRanking extends Component
{
    public function render()
    {
        return view('livewire.home-ranking')
        ->layout('layouts.app');
    }
}