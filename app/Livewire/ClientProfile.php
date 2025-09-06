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

    // ▼ 탈퇴 관련 필드
    public $deleteConfirmText = '';
    public $deletePassword = '';
    public $requiresPassword = true;

    public function mount()
    {
        $user = Auth::user();
        // 소셜 전용 계정이라도 랜덤 패스워드를 갖고 있을 수 있으므로,
        // "소셜 연결 여부"로만 강제하지 않고, 일반적으로는 패스워드 확인을 요구하되
        // 소셜 전용(google/github 중 하나라도 존재)이고 실제로 로그인 세션이 유효하면 패스워드 면제.
        $this->requiresPassword = is_null($user->google_id ?? null) && is_null($user->github_id ?? null);
    }

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
        $tempFileName = 'profile_original_' . uniqid() . '.' . $this->profile_image->getClientOriginalExtension();
        $this->profile_image->storeAs('tmp', $tempFileName, 'public');

        $originalPath = storage_path('app/public/tmp/' . $tempFileName);

        try {
            $img = $manager->read($originalPath);

            // 100x100 저장
            Storage::disk('public')->put("user/profile_image/100/{$filename}", $img->cover(100, 100)->encode($encoder));

            // 400x400 저장
            Storage::disk('public')->put("user/profile_image/400/{$filename}", $img->cover(400, 400)->encode($encoder));

            // 임시 원본 삭제
            Storage::disk('public')->delete('tmp/' . $tempFileName);

            // 기존 이미지 삭제
            if ($user->profile_image) {
                Storage::disk('public')->delete("user/profile_image/100/{$user->profile_image}");
                Storage::disk('public')->delete("user/profile_image/400/{$user->profile_image}");
            }

            $user->profile_image = $filename;
            $user->save();

            $this->profile_image = null;
            session()->flash('success', '프로필 이미지가 업데이트되었습니다.');
        } catch (\Exception $e) {
            logger()->error('Image decode failed: ' . $e->getMessage());
            session()->flash('error', '이미지를 처리할 수 없습니다. JPEG 또는 PNG 형식인지 확인해주세요.');
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

    /**
     * 회원 탈퇴
     * - "탈퇴합니다" 확인 문구 필수
     * - 일반 가입자는 비밀번호 확인
     * - 프로필 이미지 파일 삭제
     * - 사용자 레코드 삭제(SoftDeletes 사용 시 -> delete(), 아니면 forceDelete() 시도)
     * - 로그아웃 및 리다이렉트
     */
    public function deleteAccount()
    {
        $this->validate([
            'deleteConfirmText' => ['required', function ($attr, $value, $fail) {
                if (trim($value) !== '탈퇴합니다') {
                    $fail('확인 문구가 올바르지 않습니다. "탈퇴합니다"를 정확히 입력해 주세요.');
                }
            }],
            'deletePassword'    => $this->requiresPassword ? 'required|min:8' : 'nullable',
        ]);

        $user = Auth::user();

        if ($this->requiresPassword) {
            if (!Hash::check($this->deletePassword, $user->password)) {
                $this->reset('deletePassword');
                session()->flash('error', '비밀번호가 일치하지 않습니다.');
                return;
            }
        }

        DB::beginTransaction();
        try {
            // 파일 정리
            if ($user->profile_image) {
                Storage::disk('public')->delete("user/profile_image/100/{$user->profile_image}");
                Storage::disk('public')->delete("user/profile_image/400/{$user->profile_image}");
            }

            // (선택) 이곳에서 사용자 종속 데이터 정리 로직을 추가하세요.
            // 예: $user->tests()->delete(); $user->domains()->delete(); 등

            // 사용자 삭제 (SoftDeletes 고려)
            try {
                $user->delete(); // SoftDeletes 사용 시
            } catch (\Throwable $e) {
                // SoftDeletes 미사용 시 강제 삭제
                $user->forceDelete();
            }

            DB::commit();

            // 로그아웃 + 세션 정리
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            // 홈으로 리다이렉트
            return redirect('/?account_deleted=1');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            session()->flash('error', '탈퇴 처리 중 오류가 발생했습니다. 잠시 후 다시 시도해 주세요.');
        }
    }

    public function render()
    {
        return view('livewire.client-profile')->layout('layouts.app');
    }
}