<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            return redirect()->intended('/admin');
        }

        if ($user->role === 'business') {
            return redirect()->intended('/business/transactions');
        }

        if ($user->role === 'member') {
            return redirect()->intended('/dashboard');
        }

        return redirect()->intended('/dashboard');
    }
}
