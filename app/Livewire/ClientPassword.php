<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ClientPassword extends Component
{
    public $current_password = '';
    public $new_password = '';
    public $new_password_confirmation = '';

    public function updatePassword()
    {
        $user = Auth::user();

        if (!Hash::check($this->current_password, $user->password)) {
            session()->flash('error', 'Current password is incorrect.');
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
        session()->flash('success', 'Password has been successfully updated.');
    }

    public function render()
    {
        return view('livewire.client-password')
            ->layout('layouts.app');
    }
}