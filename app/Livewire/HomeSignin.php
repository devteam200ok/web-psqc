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
            session()->flash('error', 'Invalid email or password.');
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

        // ✅ Send notification email to admin via SES (new customer registration)
        try {
            Mail::raw("New customer email: {$this->email}", function ($message) {
                $message->from('info@web-psqc.com', 'Web-PSQC');   // SES verified sender
                $message->to('devteam.200.ok@gmail.com', 'Web-PSQC Admin');  // Recipient
                $message->subject('🎉 New Customer Registration! 🎈');
                // (Optional) Add replyTo if you want to reply directly to the new user
                // $message->replyTo($this->email, $this->name);
            });
        } catch (\Throwable $e) {
            report($e); // Don't block registration flow even if email fails
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
            session()->flash('error', 'Email not found.');
            return;
        }

        // Generate and save OTP
        $user->otp = random_int(100000, 999999);
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        // Render Blade view to HTML
        $mailContent = view('emails.password_reset', [
            'otp' => $user->otp,
        ])->render();

        try {
            // ✅ Send via SES (using Laravel Mail)
            Mail::html($mailContent, function ($message) use ($user) {
                $message->from('info@web-psqc.com', 'Web-PSQC'); // Sender (SES verified domain)
                $message->to($user->email);
                $message->subject('Password Reset Code');
                // Optional: Text alternative
                // $message->text('Your verification code: '.$user->otp);
            });

            $this->resetField = true;
            session()->flash('success', 'Password reset code has been sent to your email. Please enter it within 10 minutes.');
        } catch (\Throwable $e) {
            report($e);
            session()->flash('error', 'Failed to send email. Please try again later.');
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
            session()->flash('success', 'Code verified successfully.');
        } else {
            session()->flash('error', 'Invalid or expired code.');
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
            session()->flash('success', 'Password has been reset successfully.');
            return redirect('/');
        } else {
            session()->flash('error', 'Failed to reset password.');
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
