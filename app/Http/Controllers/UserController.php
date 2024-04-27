<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function create()
    {
        return view('users.register');
    }

    public function store(Request $request)
    {
        $data=$request->validate([
            'name' => ['required', 'min:3'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => 'required|confirmed|min:6'
        ]);

        $data['password']=bcrypt($data['password']);

        $user=User::create($data);
        auth()->login($user);
        return redirect('/')->with('message','User created and logged in');
    }

    public function login()
    {
        return view('users.login');
    }
    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('message','you have been logged out');

    }
    public function authenticate(Request $request)
    {
        $data=$request->validate([
            'email' => ['required', 'email'],
            'password' => 'required'
        ]);

        if(auth()->attempt($data))
        {
            $request->session()->regenerate();
            return redirect('/')->with('message','you are now logged in');
        }
        return back()->withErrors(['email'=>'invalid email credentials'])->onlyInput('email');
    }
}
