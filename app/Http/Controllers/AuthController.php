<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\PublicHero;
use App\Models\PublicService;
use App\Models\User;
use App\Support\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    public function loginPage()
    {
        // forward
        if (auth()->check() && auth()->user()->role == "admin") {
            return redirect()->route('admin.dashboard.index');
        } else if (auth()->check() && auth()->user()->role == "operator") {
            return redirect()->route('admin.store_requests.index');
        } else if (auth()->check() && auth()->user()->role == "customer") {
            return redirect()->route('customer.portal.index');
        }

        $services = PublicService::all();
        $hero = PublicHero::findOrFail(1);
        $seo = Seo::privatePage(__('login.title'));

        // view
        return view('auth/login', compact('services', 'hero', 'seo'));
    }

    public function login(Request $request)
    {
        // validate
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        // remember
        $remember = $request->boolean('remember');

        // attempt
        if (!Auth::attempt($credentials, $remember)) {
            return back()
                ->withErrors(['username' => 'Username ou mot de passe invalide.'])
                ->onlyInput('username');
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
                'username' => 'Votre compte n’est pas autorisé à accéder à l’espace client.',
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
