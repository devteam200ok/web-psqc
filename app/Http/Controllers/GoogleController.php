<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail; // ← 추가

class GoogleController extends Controller
{
    public function loginWithGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callbackFromGoogle()
    {
        try {
            $google_user = Socialite::driver('google')->stateless()->user();

            $is_user = User::where('email', $google_user->email)->first();
            if (!$is_user) {
                $user = new User();
                $user->role = 'client'; // Default role
                $user->email = $google_user->email;
                $user->email_verified_at = now();
                $randomPassword = Str::random(16);
                $user->password = Hash::make($randomPassword);
                $user->google_id = $google_user->id;
                $user->save();

                // ✅ 신규 가입 알림 (SES)
                try {
                    Mail::raw("새로 가입한 고객 이메일: {$google_user->email}", function ($message) use ($google_user) {
                        $message->from('info@devteam-test.com', 'DevTeam Test');   // SES 검증된 발신자
                        $message->to('devteam.200.ok@gmail.com', 'DevTeam Admin');  // 너한테 알림
                        $message->subject('🎉 새로운 고객이 Google로 가입했어요! 축하해 🎈');
                        // $message->replyTo($google_user->email); // (선택) 바로 답장하고 싶으면
                    });
                } catch (\Throwable $e) {
                    report($e); // 메일 실패해도 흐름 막지 않음
                }
            } else {
                $user = $is_user;
                $user->google_id = $google_user->id;
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