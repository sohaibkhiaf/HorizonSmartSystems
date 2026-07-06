@extends('layouts.store')

@section('title', __('confirmed.title'))

@section('style')
    <link rel="stylesheet" href="{{ asset('css/store/confirmed.css') }}?v={{ config('app.asset_version') }}">
@endsection

@section('content')

<main class="confirmed-page {{ app()->getLocale() === 'ar' ? 'is-rtl' : 'is-ltr' }}">

    <section class="confirm-container">

        <div class="confirm-icon">
            <i class="fa-solid fa-circle-check"></i>
        </div>

        <h1 class="confirm-title">
            {{ __('confirmed.heading') }}
        </h1>

        <p class="confirm-desc">
            {{ __('confirmed.description') }}
        </p>

        <div class="reference-box">
            <span>{{ __('confirmed.order_reference') }}</span>
            <strong>HSS{{ $request->id }}</strong>
        </div>

        <div class="next-steps">
            <h2>{{ __('confirmed.next_steps_title') }}</h2>

            <div class="step">
                <span class="step-icon">1</span>
                <p>{{ __('confirmed.step_1') }}</p>
            </div>

            <div class="step">
                <span class="step-icon">2</span>
                <p>{{ __('confirmed.step_2') }}</p>
            </div>

            <div class="step">
                <span class="step-icon">3</span>
                <p>{{ __('confirmed.step_3') }}</p>
            </div>
        </div>

        <div class="action">
            <a href="{{ route('store.index') }}" class="continue-btn">
                {{ __('confirmed.continue_shopping') }}
                <i class="{{ app()->getLocale() === 'ar' ? 'fa-solid fa-arrow-left' : 'fa-solid fa-arrow-right' }}"></i>
            </a>
        </div>

    </section>

</main>

@endsection

