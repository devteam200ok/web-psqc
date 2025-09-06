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
            session()->flash('error', '이미지 크기는 2MB를 초과할 수 없습니다.');
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

        // ✅ 1. 업로드 파일을 public 디렉터리에 저장
        $tempFileName = 'profile_original_' . uniqid() . '.' . $this->profile_image->getClientOriginalExtension();
        $this->profile_image->storeAs('tmp', $tempFileName, 'public');

        $originalPath = storage_path('app/public/tmp/' . $tempFileName);

        // ✅ 2. Intervention으로 100x100, 400x400 webp 이미지 생성
        try {
            $img = $manager->read($originalPath);

            // 100x100 저장
            $resized100 = $img->cover(100, 100)->encode($encoder);
            Storage::disk('public')->put("user/profile_image/100/{$filename}", $resized100);

            // 400x400 저장
            $resized400 = $img->cover(400, 400)->encode($encoder);
            Storage::disk('public')->put("user/profile_image/400/{$filename}", $resized400);

            // ✅ 임시 원본 삭제
            Storage::disk('public')->delete('tmp/' . $tempFileName);

            // ✅ 기존 이미지 삭제
            if ($user->profile_image) {
                Storage::disk('public')->delete("user/profile_image/100/{$user->profile_image}");
                Storage::disk('public')->delete("user/profile_image/400/{$user->profile_image}");
            }

            // ✅ DB 저장
            $user->profile_image = $filename;
            $user->save();

            $this->profile_image = null;
            session()->flash('success', '프로필 이미지가 성공적으로 업데이트되었습니다.');

        } catch (\Exception $e) {
            logger()->error('Image decode failed: ' . $e->getMessage());
            session()->flash('error', '이미지를 처리할 수 없습니다. JPEG 또는 PNG 파일인지 확인하세요.');
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

            session()->flash('success', '프로필 이미지가 삭제되었습니다.');
        } else {
            session()->flash('error', '삭제할 프로필 이미지가 없습니다.');
        }
    }

    public function render()
    {
        return view('livewire.admin-account-profile')
            ->layout('layouts.admin');
    }
}