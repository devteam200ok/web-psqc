<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUser extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $role = 'all';
    public $userCreate = 'close';
    public $userEdit = 'close';

    public $createUserEmail = '';
    public $createUserEmailCheckResult = false;
    public $createUserName = '';
    public $createUserPassword = '';
    public $createUserRole = 'client';

    public $editUserId = '';
    public $editUserEmail = '';
    public $editUserName = '';
    public $editUserPassword = '';
    public $editUserRole = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function changeRole($role)
    {   
        $this->role = $role;
        $this->resetPage();
    }

    public function openCreate()
    {
        if($this->userCreate == 'open'){
            $this->userCreate = 'close';
        } else {
            $this->userCreate = 'open';
            $this->userEdit = 'close';
        }
    }
    
    public function openEdit($user_id)
    {
        $user = User::find($user_id);
        $this->editUserId = $user->id;
        $this->editUserEmail = $user->email;
        $this->editUserName = $user->name;
        $this->editUserRole = $user->role;
        $this->editUserPassword = '';
    
        $this->userCreate = 'close';
        $this->userEdit = 'open';
    }

    public function closeEdit(){
        $this->editUserId = '';
        $this->editUserEmail = '';
        $this->editUserName = '';
        $this->editUserPassword = '';
        $this->editUserRole = '';
        $this->userEdit = 'close';
    }

    public function updatedCreateUserEmail()
    {
        $this->createUserEmailCheckResult = false;
    }

    public function createUserEmailCheck()
    {
        try {
            $this->validate([
                'createUserEmail' => 'required|email|unique:users,email',
            ]);
            $this->createUserEmailCheckResult = true;
            session()->flash('success', 'This email is available.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            session()->flash('error', 'The email is invalid or already in use.');
        }
    }

    public function newUserStore()
    {
        if(!$this->createUserEmailCheckResult){
            session()->flash('error', 'The email is invalid or already in use.');
            return;
        }
        
        try {
            $this->validate([
                'createUserName' => 'required|string|max:255',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            session()->flash('error', 'Name is required.');
            return;
        }

        if(!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,15}$/', $this->createUserPassword)){
            session()->flash('error', 'Password must be 8–15 characters and include at least one of the following types: lowercase, uppercase, number, special character.');
            return;
        }

        $user = new User;
        $user->email = $this->createUserEmail;
        $user->password = Hash::make($this->createUserPassword);
        $user->email_verified_at = time();
        $user->name = $this->createUserName;
        $user->role = $this->createUserRole;
        $user->save();

        $this->userCreate = 'close';
        $this->createUserUsername = '';
        $this->createUserEmail = '';
        $this->createUserName = '';
        $this->createUserEmailCheckResult = false;
        $this->createUserPassword = '';
        $this->createUserRole = 'client';

        session()->flash('success', 'Account has been created successfully.');
    }

    public function userUpdate()
    {
        try {
            $this->validate([
                'editUserName' => 'required|string|max:255',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            session()->flash('error', 'name is required.');
            return;
        }

        if($this->editUserPassword != ''){
            if(!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,15}$/', $this->editUserPassword)){
                session()->flash('error', 'Password must be 8–15 characters and include at least one of the following types: lowercase, uppercase, number, special character.');
                return;
            }
        }

        $user = User::find($this->editUserId);
        if($this->editUserPassword != ''){
            $user->password = Hash::make($this->editUserPassword);
        }
        $user->name = $this->editUserName;
        $user->role = $this->editUserRole;
        $user->save();

        $this->editUserId = '';
        $this->editUserName = '';
        $this->editUserEmail = '';
        $this->editUserPassword = '';
        $this->editUserRole = '';
        $this->userEdit = 'close';

        session()->flash('success', 'Account has been updated successfully.');
    }

    public function loginUser($user_id){
        $user = User::find($user_id);
        Auth::login($user);
        return redirect()->route('home');
    }

    public function deleteUser($user_id) {
        $user = User::find($user_id);
        $user->delete();

        session()->flash('success', 'Account has been deleted successfully.');
    }

    public function render()
    {
        $query = DB::table('users');

        if($this->search != '') {
            $query->Where('email', 'like', '%'.$this->search.'%')
                ->orWhere('name', 'like', '%'.$this->search.'%')
                ->orWhere('id', 'like', '%'.$this->search.'%');
        }

        if($this->role != 'all') {
            $query->where('role', $this->role);
        }

        $users = $query->orderBy('id','desc')->paginate(10);

        return view('livewire.admin-user')
            ->with('users', $users)
            ->layout('layouts.admin');
    }
}