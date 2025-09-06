<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;

class AdminSettingTerms extends Component
{
    public $terms;

    protected $listeners = [
        'update' => 'update',
    ];

    public function mount()
    {
        $setting = Setting::first();
        if(!$setting) {
            $setting = new Setting();
        }

        if ($setting) {
            $this->terms = $setting->terms;
        }
    }

    public function update($terms)
    {
        $setting = Setting::first();
        $setting->terms = $terms;
        $setting->save();

        session()->flash('message', 'Terms and Conditions updated successfully.');
    }

    public function render()
    {
        return view('livewire.admin-setting-terms')
        ->layout('layouts.admin');
    }
}
