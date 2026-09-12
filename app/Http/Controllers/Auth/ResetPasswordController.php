<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class ResetPasswordController extends Controller
{
    public function create(Request $request,string $token){
        return view('auth.reset-password',[
            'token' => $token,
          'email' => $request->email,
        ]);
    }


    public function store(Request $request){
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);


       $status = Password::reset(
        $request->only('email','password','password_confirmation','token'),
        function (User $user,string $password){
            $user->forceFill([
                'password' => Hash::make($passowrd)
            ])->setRememberToken(Str::random(60));
        
        $user->save();

        event(new PasswordReset($user));

        }
       );

       return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);


    }









}
