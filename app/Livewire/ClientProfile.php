<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ClientProfile extends Component
{
    use WithFileUploads;

    public $profile_image;

    // ▼ Account deletion related fields
    public $deleteConfirmText = '';
    public $deletePassword = '';
    public $requiresPassword = true;

    public function mount()
    {
        $user = Auth::user();
        // Even social-only accounts might have random passwords,
        // so we don't just force based on "social connection status". Generally require password confirmation,
        // but exempt password if it's social-only (either google/github exists) and login session is valid.
        $this->requiresPassword = is_null($user->google_id ?? null) && is_null($user->github_id ?? null);
    }

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
        $tempFileName = 'profile_original_' . uniqid() . '.' . $this->profile_image->getClientOriginalExtension();
        $this->profile_image->storeAs('tmp', $tempFileName, 'public');

        $originalPath = storage_path('app/public/tmp/' . $tempFileName);

        try {
            $img = $manager->read($originalPath);

            // Save 100x100
            Storage::disk('public')->put("user/profile_image/100/{$filename}", $img->cover(100, 100)->encode($encoder));

            // Save 400x400
            Storage::disk('public')->put("user/profile_image/400/{$filename}", $img->cover(400, 400)->encode($encoder));

            // Delete temporary original
            Storage::disk('public')->delete('tmp/' . $tempFileName);

            // Delete existing image
            if ($user->profile_image) {
                Storage::disk('public')->delete("user/profile_image/100/{$user->profile_image}");
                Storage::disk('public')->delete("user/profile_image/400/{$user->profile_image}");
            }

            $user->profile_image = $filename;
            $user->save();

            $this->profile_image = null;
            session()->flash('success', 'Profile image has been updated.');
        } catch (\Exception $e) {
            logger()->error('Image decode failed: ' . $e->getMessage());
            session()->flash('error', 'Unable to process image. Please check if it is in JPEG or PNG format.');
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
            session()->flash('error', 'No profile image to delete.');
        }
    }

    /**
     * Delete Account
     * - "DELETE ACCOUNT" confirmation text required
     * - Regular users need password confirmation
     * - Delete profile image files
     * - Delete user record (if using SoftDeletes -> delete(), otherwise try forceDelete())
     * - Logout and redirect
     */
    public function deleteAccount()
    {
        $this->validate([
            'deleteConfirmText' => ['required', function ($attr, $value, $fail) {
                if (trim($value) !== 'DELETE ACCOUNT') {
                    $fail('Confirmation text is incorrect. Please type exactly "DELETE ACCOUNT".');
                }
            }],
            'deletePassword'    => $this->requiresPassword ? 'required|min:8' : 'nullable',
        ]);

        $user = Auth::user();

        if ($this->requiresPassword) {
            if (!Hash::check($this->deletePassword, $user->password)) {
                $this->reset('deletePassword');
                session()->flash('error', 'Password does not match.');
                return;
            }
        }

        DB::beginTransaction();
        try {
            // File cleanup
            if ($user->profile_image) {
                Storage::disk('public')->delete("user/profile_image/100/{$user->profile_image}");
                Storage::disk('public')->delete("user/profile_image/400/{$user->profile_image}");
            }

            // (Optional) Add user-dependent data cleanup logic here.
            // e.g.: $user->tests()->delete(); $user->domains()->delete(); etc.

            // Delete user (considering SoftDeletes)
            try {
                $user->delete(); // When using SoftDeletes
            } catch (\Throwable $e) {
                // Force delete when not using SoftDeletes
                $user->forceDelete();
            }

            DB::commit();

            // Logout + session cleanup
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            // Redirect to home
            return redirect('/?account_deleted=1');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            session()->flash('error', 'An error occurred during account deletion. Please try again later.');
        }
    }

    public function render()
    {
        return view('livewire.client-profile')->layout('layouts.app');
    }
}