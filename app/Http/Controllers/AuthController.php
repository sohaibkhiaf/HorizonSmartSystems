<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\PublicService;
use App\Models\User;
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
            return redirect()->route('admin.store_requests.index');
        } else if (auth()->check() && auth()->user()->role == "CUSTOMER") {
            return redirect()->route('customer.portal.index');
        }

        $services = PublicService::all();

        // view
        return view('auth/login', compact('services'));
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
            ActivityLog::create([
                'user_id'    => auth()->id(),
                'action'     => 'login',
                'model_type' => User::class,
                'model_id'   => auth()->user()->id,
            ]);
            return redirect()->route('admin.dashboard.index');
        }else if (auth()->user()->isOperator() ) {
            ActivityLog::create([
                'user_id'    => auth()->id(),
                'action'     => 'login',
                'model_type' => User::class,
                'model_id'   => auth()->user()->id,
            ]);
            return redirect()->route('admin.store_requests.index');
        }else if (auth()->user()->isCustomer() ) {
            ActivityLog::create([
                'user_id'    => auth()->id(),
                'action'     => 'login',
                'model_type' => User::class,
                'model_id'   => auth()->user()->id,
            ]);
            return redirect()->route('customer.portal.index');
        }else{
            Auth::logout();

            return back()->withErrors([
                'email' => 'Votre compte n’est pas autorisé à accéder à l’espace client.',
            ]);
        }

    }

    public function logout(Request $request)
    {
        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'logout',
            'model_type' => User::class,
            'model_id'   => auth()->user()->id,
        ]);

        // logout
        Auth::logout();

        // session invalidate
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // redirect
        return redirect()->route('login.page');
    }
}
