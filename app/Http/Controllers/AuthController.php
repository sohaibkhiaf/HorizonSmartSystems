<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    public function loginPage()
    {
        // forward
        if (auth()->check() && auth()->user()->role == "ADMIN") {
            return redirect()->route('admin.dashboard.index');
        } else if (auth()->check() && auth()->user()->role == "OPERATOR") {
            return redirect()->route('admin.requests.index');
        } else if (auth()->check() && auth()->user()->role == "CUSTOMER") {
            return redirect()->route('customer.dashboard.index');
        }

        // view
        return view('auth/login');
    }

    public function login(Request $request)
    {
        // validate
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // remember
        $remember = $request->boolean('remember');

        // attempt
        if (!Auth::attempt($credentials, $remember)) {
            return back()
                ->withErrors(['email' => 'E-mail ou mot de passe invalide.'])
                ->onlyInput('email');
        }

        // session
        $request->session()->regenerate();

        // redirect
        if (auth()->user()->isAdmin() ) {
            return redirect()->route('admin.dashboard.index');
        }else if (auth()->user()->isOperator() ) {
            return redirect()->route('requests.index');
        }else if (auth()->user()->isCustomer() ) {
            return redirect()->route('customer.dashboard.index');
        }else{
            // unauthenticated
            Auth::logout();

            return back()->withErrors([
                'email' => 'Ce compte n\'est pas autorisé à accéder au panneau d\'administration.',
            ]);
        }

    }

    public function logout(Request $request)
    {
        // logout
        Auth::logout();

        // session invalidate
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // redirect
        return redirect()->route('login.page');
    }
}
