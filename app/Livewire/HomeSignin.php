<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Api;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class HomeSignin extends Component
{
    public $passwordType = 'password';
    public $page = 'Signin';
    public $email;
    public $name;
    public $password;
    public $resetField = false;
    public $resetCode = '';
    public $codeMatch = false;

    public function togglePasswordType()
    {
        $this->passwordType = $this->passwordType === 'password' ? 'text' : 'password';
    }

    public function setPage($page)
    {
        $this->page = $page;
    }

    public function signin()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required|min:8|max:255',
        ]);

        $credentials = [
            'email' => $this->email,
            'password' => $this->password,
        ];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            return redirect('/');
        } else {
            session()->flash('error', '이메일 또는 비밀번호가 올바르지 않습니다.');
        }
    }

    public function signup()
    {
        $this->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|max:255',
            'name' => 'required|string|max:255',
        ]);

        $user = User::create([
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'name' => $this->name,
            'role' => 'client',
        ]);

        // ✅ SES로 관리자(devteam.200.ok@gmail.com)에게 알림 메일 발송 (새로운 고객 가입)
        try {
            Mail::raw("새로 가입한 고객 이메일: {$this->email}", function ($message) {
                $message->from('info@devteam-test.com', 'DevTeam Test');   // SES 검증 발신자
                $message->to('devteam.200.ok@gmail.com', 'DevTeam Admin');  // 수신자(너)
                $message->subject('🎉 새로운 고객이 가입했어요! 축하해 🎈');
                // (선택) 가입자에게 바로 답장하고 싶으면 replyTo 추가
                // $message->replyTo($this->email, $this->name);
            });
        } catch (\Throwable $e) {
            report($e); // 실패해도 가입 흐름은 막지 않음
        }

        Auth::login($user);

        $user = Auth::user();
        return redirect('/');
    }

    public function sendResetCode()
    {
        $this->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $this->email)->first();

        if (!$user) {
            session()->flash('error', '이메일을 찾을 수 없습니다.');
            return;
        }

        // OTP 생성/저장
        $user->otp = random_int(100000, 999999);
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        // Blade 뷰를 HTML로 렌더링
        $mailContent = view('emails.password_reset', [
            'otp' => $user->otp,
        ])->render();

        try {
            // ✅ SES로 발송 (Laravel Mail 사용)
            Mail::html($mailContent, function ($message) use ($user) {
                $message->from('info@devteam-test.com', 'DevTeam Test'); // 발신자(SES에 인증된 도메인)
                $message->to($user->email);
                $message->subject('Password Reset Code');
                // 선택: 텍스트 대체 본문
                // $message->text('Your verification code: '.$user->otp);
            });

            $this->resetField = true;
            session()->flash('success', '비밀번호 재설정 코드가 이메일로 전송되었습니다. 10분 이내에 입력해 주세요.');
        } catch (\Throwable $e) {
            report($e);
            session()->flash('error', '메일 전송에 실패했습니다. 잠시 후 다시 시도해 주세요.');
        }
    }

    public function verifyResetCode()
    {
        $this->validate([
            'resetCode' => 'required|numeric',
        ]);

        $user = User::where('email', $this->email)->first();

        if ($user && $user->otp == $this->resetCode && now()->lessThanOrEqualTo($user->otp_expires_at)) {
            $this->codeMatch = true;
            session()->flash('success', '코드가 성공적으로 확인되었습니다.');
        } else {
            session()->flash('error', '유효하지 않거나 만료된 코드입니다.');
        }
    }

    public function resetPassword()
    {
        $this->validate([
            'password' => 'required|min:8|max:255',
        ]);

        $user = User::where('email', $this->email)->first();

        if ($user && $this->codeMatch) {
            $user->password = Hash::make($this->password);
            $user->otp = null; // Clear the OTP after successful reset
            $user->otp_expires_at = null; // Clear the expiration time
            $user->email_verified_at = now(); // Mark email as verified
            $user->save();

            Auth::login($user);
            session()->flash('success', '비밀번호가 성공적으로 재설정되었습니다.');
            return redirect('/');
        } else {
            session()->flash('error', '비밀번호 재설정에 실패했습니다.');
        }
    }

    public function quickLogin($role)
    {
        $user = User::where('role', $role)->first();
        Auth::login($user);
        return redirect('/');

    }

    public function render()
    {
        return view('livewire.home-signin')
            ->layout('layouts.auth');
    }
}
