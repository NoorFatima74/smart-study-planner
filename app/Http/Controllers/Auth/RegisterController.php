<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;


class RegisterController extends Controller
{
    public function create(){
        return view('auth.register');
    }

    public function store(Request $request){

      $validated = $request->validate([
         'name' => ['required','string','max:255'],
         'email' => ['required','string','email','max:255','unique:users,email'],
         'password'=> ['required','confirmed',Password::min(8)],
      ]);

      $user = User::create([
        'name' => $validated['name'],
        'email'=>$validated['email'],
        'password' =>Hash::make( $validated['password']),
      ]);

      Auth::login($user);

       return redirect()->route('dashboard');

    }
}
