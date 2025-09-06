<?php

namespace App\Livewire;

use Livewire\Component;

class HomeCertificate extends Component
{
    public function render()
    {
        return view('livewire.home-certificate')
        ->layout('layouts.app');
    }
}