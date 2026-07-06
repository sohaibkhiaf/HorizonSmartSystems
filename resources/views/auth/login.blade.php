@extends('layouts.login')

@section('title', __('login.title'))

@section('style')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}?v={{ config('app.asset_version') }}">
@endsection

@section('content')

<section class="login-section">
    <div class="login-card">

        <h2>{{ __('login.login_title') }}</h2>

        <p class="login-subtitle">
            {{ __('login.login_subtitle') }}
        </p>

        <p class="login-account-help">
            {{ __('login.customer_account_notice') }}
            <a href="/?section=contact">
                {{ __('login.contact_us') }}
            </a>
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
                <label for="username">{{ __('login.username') }}</label>

                <div class="input-wrapper">
                    <i class="fa-solid fa-envelope"></i>

                    <input
                        id="username"
                        type="text"
                        name="username"
                        value="{{ old('username') }}"
                        placeholder="{{ __('login.username_placeholder') }}"
                        required
                        autofocus>
                </div>
            </div>

            <div class="form-group">
                <label for="password">{{ __('login.password') }}</label>

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
</section>

@endsection
