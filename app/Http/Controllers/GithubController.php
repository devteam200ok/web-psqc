<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail; // ← 추가

class GithubController extends Controller
{
    public function loginWithGithub()
    {
        return Socialite::driver('github')->redirect();
    }

    public function callbackFromGithub()
    {
        try {
            $github_user = Socialite::driver('github')->stateless()->user();

            $is_user = User::where('email', $github_user->email)->first();
            if(!$is_user){
                $user = new User();
                $user->role = 'client'; // Default role
                $user->email = $github_user->email; // 일부 계정은 이메일이 null일 수 있음
                $user->email_verified_at = now();
                $randomPassword = Str::random(16);
                $user->password = Hash::make($randomPassword);
                $user->github_id = $github_user->id;
                $user->save();

                // ✅ 신규 가입 알림 (SES)
                try {
                    $joinedEmail = $github_user->email ?: '(이메일 비공개)';
                    Mail::raw("새로 가입한 고객 이메일: {$joinedEmail}", function ($message) use ($joinedEmail) {
                        $message->from('info@dweb-psqc.com', 'Web PSQC');   // SES 검증된 발신자
                        $message->to('devteam.200.ok@gmail.com', 'DevTeam Admin');  // 너한테 알림
                        $message->subject('[Web PSQC]🎉 새로운 고객이 GitHub로 가입했어요! 축하해 🎈');
                    });
                } catch (\Throwable $e) {
                    report($e); // 메일 실패해도 흐름 막지 않음
                }
            } else {
                $user = $is_user;
                $user->github_id = $github_user->id;
                $user->email_verified_at = now();
                $user->save();
            }

            Auth::login($user);
            return redirect('/');
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}