@extends('layouts.login')

@section('title', __('login.title'))

@section('style')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')

<section class="login-section">
    <div class="login-container">

        {{-- Left Content --}}
        <div class="login-visual">

            <h1>
                {{ __('login.heading') }}
            </h1>

            <p>
                {{ __('login.description') }}
            </p>

            <div class="login-features">

                <div class="feature-item">
                    <i class="fa-solid fa-display"></i>
                    <span>{{ __('login.feature_demo') }}</span>
                </div>

                <div class="feature-item">
                    <i class="fa-solid fa-comments"></i>
                    <span>{{ __('login.feature_chat') }}</span>
                </div>

                <div class="feature-item">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>{{ __('login.feature_security') }}</span>
                </div>

            </div>
        </div>

        {{-- Login Card --}}
        <div class="login-card">

            <div class="login-icon">
                <i class="fa-solid fa-user-lock"></i>
            </div>

            <h2>{{ __('login.login_title') }}</h2>

            <p class="login-subtitle">
                {{ __('login.login_subtitle') }}
            </p>

            @if($errors->any())
                <div class="alert danger">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="alert success">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}">
                @csrf

                <div class="form-group">
                    <label for="email">
                        {{ __('login.email') }}
                    </label>

                    <div class="input-wrapper">
                        <i class="fa-solid fa-envelope"></i>

                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="{{ __('login.email_placeholder') }}"
                            required
                            autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">
                        {{ __('login.password') }}
                    </label>

                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock"></i>

                        <input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="{{ __('login.password_placeholder') }}"
                            required>
                    </div>
                </div>

                <label class="checkbox-row">
                    <input type="checkbox" name="remember">
                    <span>{{ __('login.remember') }}</span>
                </label>

                <button type="submit" class="login-btn">
                    {{ __('login.login_button') }}
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                </button>
            </form>

        </div>

    </div>
</section>

@endsection
