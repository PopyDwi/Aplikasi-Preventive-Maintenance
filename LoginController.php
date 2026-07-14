<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'role' => 'required|string',
        ]);

        $user = User::where('username', $request->username)
            ->whereRaw('lower(role) = ?', [strtolower($request->role)])
            ->first();

        if ($user) {
            $password = $user->password;
            $isBcrypted = is_string($password) && preg_match('/^\$2[ayb]\$.{56}$/', $password);

            if ($isBcrypted && Hash::check($request->password, $password)) {
                if (strtolower($user->role) === 'admin') {
                    return redirect('/dashboard');
                }

                if (strtolower($user->role) === 'teknisi') {
                    return redirect('/dashboardteknisi');
                }
            }

            if (! $isBcrypted && $password === $request->password) {
                $user->password = Hash::make($request->password);
                $user->save();

                if (strtolower($user->role) === 'admin') {
                    return redirect('/dashboard');
                }

                if (strtolower($user->role) === 'teknisi') {
                    return redirect('/dashboardteknisi');
                }
            }
        }

        return redirect('/')
            ->with('error', 'Username atau Password Salah');
    }
}