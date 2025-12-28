<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
{
    $googleUser = Socialite::driver('google')
        ->stateless()
        ->user();

    $user = User::updateOrCreate(
        ['email' => $googleUser->getEmail()],
        [
            'name' => $googleUser->getName(),
            'google_id' => $googleUser->getId(),
        ]
    );

    // Default role untuk user baru
    if (! $user->hasAnyRole(['admin', 'user'])) {
        $user->assignRole('user');
    }

    Auth::login($user);

    //  Redirect sesuai role
    if ($user->hasRole('admin')) {
        return redirect('/admin');
    }

    return redirect('/app');
}

}
