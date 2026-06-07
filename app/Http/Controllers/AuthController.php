<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    public function loginPage()
    {
        if (auth()->check() && auth()->user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin/auth/login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            return back()
                ->withErrors(['email' => 'E-mail ou mot de passe invalide.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        if (!auth()->user()->isAdmin()) {
            Auth::logout();

            return back()->withErrors([
                'email' => 'Ce compte n\'est pas autorisé à accéder au panneau d\'administration.',
            ]);
        }

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.page');
    }
}
