<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;

class AdminSettingInformation extends Component
{
    public $company;
    public $company_phone;
    public $company_address;
    public $company_ceo;
    public $company_cpo;
    public $business_number;
    public $footer;
    public $version;

    public function mount()
    {
        $setting = DB::table('settings')->first();
        if(!$setting) {
            $setting = new Setting();
        }

        if ($setting) {
            $this->company = $setting->company;
            $this->company_phone = $setting->company_phone;
            $this->company_address = $setting->company_address;
            $this->company_ceo = $setting->company_ceo;
            $this->company_cpo = $setting->company_cpo;
            $this->business_number = $setting->business_number;
            $this->footer = $setting->footer;
            $this->version = $setting->version;
        }
    }

    public function update()
    {
        $validatedData = $this->validate([
            // Validate your input fields as necessary
            'company' => 'nullable|string|max:255',
            'company_phone' => 'nullable|string|max:255',
            'company_address' => 'nullable|string|max:255',
            'company_ceo' => 'nullable|string|max:255',
            'company_cpo' => 'nullable|string|max:255',
            'business_number' => 'nullable|string|max:255',
            'footer' => 'nullable|string|max:255',
            'version' => 'nullable|string|max:255',
        ]);

        $setting = DB::table('settings')->first();
        if($setting) {
            DB::table('settings')->where('id', $setting->id)->update($validatedData);
        } else {
            DB::table('settings')->insert($validatedData);
        }

        session()->flash('message', 'Settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.admin-setting-information')
             ->layout('layouts.admin');
    }
}
