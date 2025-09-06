<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminAccountPassword extends Component
{
    public $current_password = '';
    public $new_password = '';
    public $new_password_confirmation = '';

    public function updatePassword()
    {
        $user = Auth::user();

        if (!Hash::check($this->current_password, $user->password)) {
            session()->flash('error', '현재 비밀번호가 올바르지 않습니다.');
            return;
        }

        $this->validate([
            'new_password' => [
                'required',
                'confirmed',
                'min:8',
                'max:15',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])/',
            ],
        ]);

        $user->password = Hash::make($this->new_password);
        $user->save();

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        session()->flash('success', '비밀번호가 성공적으로 업데이트되었습니다.');
    }

    public function render()
    {
        return view('livewire.admin-account-password')
            ->layout('layouts.admin');
    }
}