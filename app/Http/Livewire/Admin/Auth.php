<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class Auth extends Component
{

    public $users;
    public $firstname;
    public $lastname;
    public $email;
    public $mobile_number;
    public $register_form = false;

    public function render()
    {
        return view('livewire.admin.auth');
    }

    private function resetInputFields(){
        $this->firstname = '';
        $this->lastname = '';
        $this->email = '';
        $this->mobile_number = '';
    }

    public function login()
    {
        $validatedDate = $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if(\Auth::attempt([
            'email' => $this->email,
            'password' => $this->password
        ])){
            session()->flash('message', "You have been successfully login.");
        }else{
            session()->flash('error', 'email and password are wrong.');
        }
    }

    public function register()
    {
        $this->register_form = !$this->register_form;
    }

    public function registerStore()
    {
        $v = $this->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $this->password = Hash::make($this->password);

        $data = [
            'name' => $this->name,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'mobile_number' => $this->mobile_number,
         ];

        User::create($data);

        session()->flash('message', 'You have been successfully registered.');

        $this->resetInputFields();

    }
}
