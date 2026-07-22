<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\PublicHero;
use App\Models\PublicService;
use App\Models\User;
use App\Support\LocalizedUrl;
use App\Support\Seo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Display the login page.
     */
    public function loginPage(): View|RedirectResponse
    {
        if (
            auth()->check()
            && auth()->user()->role === 'admin'
        ) {
            return redirect()->route(
                'admin.dashboard.index'
            );
        }

        if (
            auth()->check()
            && auth()->user()->role === 'operator'
        ) {
            return redirect()->route(
                'admin.store_requests.index'
            );
        }

        if (
            auth()->check()
            && auth()->user()->role === 'customer'
        ) {
            return redirect()->route(
                'customer.portal.index'
            );
        }

        $services = PublicService::query()
            ->where('is_published', true)
            ->get();

        $hero = PublicHero::findOrFail(1);

        $seo = Seo::privatePage(
            __('login.title')
        );

        return view(
            'auth.login',
            compact(
                'services',
                'hero',
                'seo'
            )
        );
    }

    /**
     * Authenticate the user.
     */
    public function login(
        Request $request
    ): RedirectResponse {
        $credentials = $request->validate([
            'username' => [
                'required',
                'string',
            ],

            'password' => [
                'required',
            ],
        ]);

        $remember = $request->boolean(
            'remember'
        );

        if (
            !Auth::attempt(
                $credentials,
                $remember
            )
        ) {
            return back()
                ->withErrors([
                    'username' =>
                        'Username ou mot de passe invalide.',
                ])
                ->onlyInput('username');
        }

        $request->session()->regenerate();

        $user = auth()->user();

        if (
            $user->isAdmin()
            || $user->isOperator()
            || $user->isCustomer()
        ) {
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'login',
                'model_type' => User::class,
                'model_id' => $user->id,
            ]);
        }

        if ($user->isAdmin()) {
            return redirect()->route(
                'admin.dashboard.index'
            );
        }

        if ($user->isOperator()) {
            return redirect()->route(
                'admin.store_requests.index'
            );
        }

        if ($user->isCustomer()) {
            return redirect()->route(
                'customer.portal.index'
            );
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return back()->withErrors([
            'username' =>
                'Votre compte n’est pas autorisé à accéder à l’espace client.',
        ]);
    }

    /**
     * Log the user out.
     */
    public function logout(
        Request $request
    ): RedirectResponse {
        $user = auth()->user();

        if ($user !== null) {
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'logout',
                'model_type' => User::class,
                'model_id' => $user->id,
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to(
            LocalizedUrl::route(
                'login.page',
                [],
                app()->getLocale(),
                [],
                false
            )
        );
    }
}

