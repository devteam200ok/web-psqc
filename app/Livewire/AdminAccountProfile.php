<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Illuminate\Support\Facades\Auth;

class AdminAccountProfile extends Component
{
    use WithFileUploads;

    public $profile_image;

    public function updatedProfileImage()
    {
        if ($this->profile_image && $this->profile_image->getSize() > 2 * 1024 * 1024) {
            $this->reset('profile_image');
            session()->flash('error', 'Image size cannot exceed 2MB.');
        }
    }

    public function saveProfileImage()
    {
        $this->validate([
            'profile_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = Auth::user();
        $manager = new ImageManager(new Driver());
        $encoder = new WebpEncoder(quality: 85);

        $filename = 'user_' . $user->id . '_' . time() . '.webp';

        // 1) Store the uploaded file in the public directory
        $tempFileName = 'profile_original_' . uniqid() . '.' . $this->profile_image->getClientOriginalExtension();
        $this->profile_image->storeAs('tmp', $tempFileName, 'public');

        $originalPath = storage_path('app/public/tmp/' . $tempFileName);

        // 2) Generate 100x100 and 400x400 WebP images using Intervention
        try {
            $img = $manager->read($originalPath);

            // Save 100x100
            $resized100 = $img->cover(100, 100)->encode($encoder);
            Storage::disk('public')->put("user/profile_image/100/{$filename}", $resized100);

            // Save 400x400
            $resized400 = $img->cover(400, 400)->encode($encoder);
            Storage::disk('public')->put("user/profile_image/400/{$filename}", $resized400);

            // Delete temporary original
            Storage::disk('public')->delete('tmp/' . $tempFileName);

            // Delete existing images
            if ($user->profile_image) {
                Storage::disk('public')->delete("user/profile_image/100/{$user->profile_image}");
                Storage::disk('public')->delete("user/profile_image/400/{$user->profile_image}");
            }

            // Save to DB
            $user->profile_image = $filename;
            $user->save();

            $this->profile_image = null;
            session()->flash('success', 'Profile image updated successfully.');

        } catch (\Exception $e) {
            logger()->error('Image decode failed: ' . $e->getMessage());
            session()->flash('error', 'Unable to process the image. Please ensure it is a JPEG or PNG file.');
        }
    }

    public function deleteProfileImage()
    {
        $user = Auth::user();

        if ($user->profile_image) {
            Storage::disk('public')->delete("user/profile_image/100/{$user->profile_image}");
            Storage::disk('public')->delete("user/profile_image/400/{$user->profile_image}");

            $user->profile_image = null;
            $user->save();

            session()->flash('success', 'Profile image has been deleted.');
        } else {
            session()->flash('error', 'There is no profile image to delete.');
        }
    }

    public function render()
    {
        return view('livewire.admin-account-profile')
            ->layout('layouts.admin');
    }
}