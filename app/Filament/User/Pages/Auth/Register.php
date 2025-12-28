<?php

namespace App\Filament\User\Pages\Auth;

use App\Models\User;
use Filament\Pages\Auth\Register as BaseRegister;

class Register extends BaseRegister
{
    protected function handleRegistration(array $data): User
    {
        $user = User::create($data);

        // 🔥 INI KUNCI FIX 403
        $user->assignRole('user');

        return $user;
    }
}
