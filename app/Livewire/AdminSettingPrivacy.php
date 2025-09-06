<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;

class AdminSettingPrivacy extends Component
{
    public $privacy;

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
            $this->privacy = $setting->privacy;
        }
    }

    public function update($privacy)
    {
        $setting = Setting::first();
        $setting->privacy = $privacy;
        $setting->save();

        session()->flash('message', 'Privacy Policy updated successfully.');
    }

    public function render()
    {
        return view('livewire.admin-setting-privacy')
        ->layout('layouts.admin');
    }
}
