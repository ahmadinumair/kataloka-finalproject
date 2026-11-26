<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Daftar Akun Mahasiswa - Kataloka')]
class Register extends Component
{
    #[Rule('required|min:3|max:100')]
    public string $name = '';

    #[Rule('required|email|unique:users,email')]
    public string $email = '';

    #[Rule('required|min:8|same:password_confirmation')]
    public string $password = '';

    public string $password_confirmation = '';

    public function register()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => 'student',
        ]);

        Auth::login($user);

        session()->flash('success', 'Akun berhasil dibuat! Selamat datang di Kataloka.');

        return $this->redirect(route('feedbacks.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
