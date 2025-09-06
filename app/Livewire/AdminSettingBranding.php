<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\PngEncoder;

class AdminSettingBranding extends Component
{
    use WithFileUploads;

    public $logo_color_square_svg;
    public $logo_color_svg;
    public $logo_white_square_svg;
    public $logo_white_svg;
    public $logo_color_square_png;
    public $logo_color_png;
    public $logo_white_square_png;
    public $logo_white_png;

    protected function rules()
    {
        return [
            'logo_color_square_svg'   => 'nullable|file|mimes:svg,xml',
            'logo_color_svg'          => 'nullable|file|mimes:svg,xml',
            'logo_white_square_svg'   => 'nullable|file|mimes:svg,xml',
            'logo_white_svg'          => 'nullable|file|mimes:svg,xml',
            'logo_color_square_png'   => 'nullable|image|mimes:png|max:10240',
            'logo_color_png'          => 'nullable|image|mimes:png|max:10240',
            'logo_white_square_png'   => 'nullable|image|mimes:png|max:10240',
            'logo_white_png'          => 'nullable|image|mimes:png|max:10240',
        ];
    }

    public function update()
    {
        $this->validate();

        if ($this->logo_color_square_svg) {
            $this->logo_color_square_svg
                 ->storeAs('branding', 'logo_color_square.svg', 'public');
            $this->logo_color_square_svg = null;
        }
        if ($this->logo_color_svg) {
            $this->logo_color_svg
                 ->storeAs('branding', 'logo_color.svg', 'public');
            $this->logo_color_svg = null;
        }
        if ($this->logo_white_square_svg) {
            $this->logo_white_square_svg
                 ->storeAs('branding', 'logo_white_square.svg', 'public');
            $this->logo_white_square_svg = null;
        }
        if ($this->logo_white_svg) {
            $this->logo_white_svg
                 ->storeAs('branding', 'logo_white.svg', 'public');
            $this->logo_white_svg = null;
        }

        $manager    = new ImageManager(new Driver());
        $pngEncoder = new PngEncoder(80);

        if ($this->logo_color_square_png) {
            $this->logo_color_square_png
                 ->storeAs('branding', 'logo_color_square.png', 'public');

            $original = storage_path('app/public/branding/logo_color_square.png');
            $img      = $manager->read($original);

            foreach ([512, 192, 180, 150, 32, 16] as $size) {
                $resized = (clone $img)
                    ->cover($size, $size)
                    ->encode($pngEncoder);
                Storage::disk('public')
                       ->put("branding/logo_color_{$size}.png", $resized);
            }

            $ico = (clone $img)
                ->cover(32, 32)
                ->encode($pngEncoder);

            file_put_contents(public_path('favicon.ico'), (string) $ico);

            Storage::disk('public')->delete('branding/logo_color_square.png');
            $this->logo_color_square_png = null;
        }

        if ($this->logo_color_png) {
            $this->logo_color_png
                 ->storeAs('branding', 'logo_color.png', 'public');
            $this->logo_color_png = null;
        }

        if ($this->logo_white_square_png) {
            $this->logo_white_square_png
                 ->storeAs('branding', 'logo_white_square.png', 'public');

            $original = storage_path('app/public/branding/logo_white_square.png');
            $img      = $manager->read($original);

            $resized = $img
                ->cover(512, 512)
                ->encode($pngEncoder);
            Storage::disk('public')
                   ->put('branding/logo_white_512.png', $resized);

            Storage::disk('public')->delete('branding/logo_white_square.png');
            $this->logo_white_square_png = null;
        }

        if ($this->logo_white_png) {
            $this->logo_white_png
                 ->storeAs('branding', 'logo_white.png', 'public');
            $this->logo_white_png = null;
        }

        session()->flash('success', 'branding saved successfully.');
    }

    public function render()
    {
        return view('livewire.admin-setting-branding')
             ->layout('layouts.admin');
    }
}
