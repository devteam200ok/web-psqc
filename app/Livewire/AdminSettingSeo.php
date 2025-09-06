<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class AdminSettingSeo extends Component
{
    use WithFileUploads;

    public $seo_title;
    public $seo_description;
    public $seo_keywords;
    public $seo_author;
    public $og_url;
    public $og_type;
    public $og_title;
    public $og_description;
    public $og_image;
    public $new_og_image;

    public function mount()
    {
        $setting = Setting::first();

        if ($setting) {
            $this->seo_title       = $setting->seo_title;
            $this->seo_description = $setting->seo_description;
            $this->seo_keywords    = $setting->seo_keywords;
            $this->seo_author      = $setting->seo_author;
            $this->og_url          = $setting->og_url;
            $this->og_type         = $setting->og_type;
            $this->og_title        = $setting->og_title;
            $this->og_description  = $setting->og_description;
            $this->og_image        = $setting->og_image;
        }
    }

    public function update()
    {
        $setting = Setting::first();
        if ($this->new_og_image) {
            Storage::disk('public')
                ->putFileAs('setting', $this->new_og_image, 'og.png');

            $setting->og_image = 'setting/og.png';
        }

        $setting->seo_title       = $this->seo_title;
        $setting->seo_description = $this->seo_description;
        $setting->seo_keywords    = $this->seo_keywords;
        $setting->seo_author      = $this->seo_author;
        $setting->og_url          = $this->og_url;
        $setting->og_type         = $this->og_type;
        $setting->og_title        = $this->og_title;
        $setting->og_description  = $this->og_description;
        $setting->save();

        session()->flash('message', 'Settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.admin-setting-seo')
        ->layout('layouts.admin');
    }
}