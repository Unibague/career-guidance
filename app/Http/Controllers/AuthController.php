<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    //

    public function handleRoleRedirect()
    {

    }

    public function redirectGoogleLogin()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->user();
        $user = User::where('email', $googleUser->email)->first();
        if (!$user) {
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'password' => 'automatic_generate_password'
            ]);
            Auth::login($user);
            return redirect()->route('newLoggedUser.landing');
        }

        $role = DB::table('roles')->where('id','=',$user->role_id)->first();

        if($role->name === 'admin'){
            return redirect()->route('results.report');
        }
    }
}
