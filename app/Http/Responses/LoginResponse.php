<?php

namespace App\Http\Responses;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = auth()->user();

        if ($user->role === 'petugas_sampah') {
            return redirect()->route('petugas.dashboard');
        }

        if (is_null($user->employee_id)) {
            return redirect()->route('dashboard');
        }

        return redirect()->route('pegawai.homepage');
    }
}
