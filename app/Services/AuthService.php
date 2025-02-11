<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function login(string $email, string $password)
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            abort(401, 'Incorrect credentials');
        }

        return $user->createToken('auth_token')->plainTextToken;
    }
}