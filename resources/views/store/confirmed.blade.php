@extends('layouts.store')

@section('title', 'Order created')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/store/confirmed.css') }}">
@endsection

@section('content')

<div class="confirm-container">

    <!-- Icon -->
    <div class="confirm-icon">
        <i class="fa-solid fa-circle-check"></i>
    </div>

    <!-- Title -->
    <h1 class="confirm-title">Order confirmed!</h1>

    <!-- Description -->
    <p class="confirm-desc">
        We've received your order and sent a confirmation. Our team will contact you shortly to arrange delivery.
    </p>

    <!-- Order Reference Box -->
    <div class="reference-box">
        <h3>Order reference</h3>
        <p class="reference-id">HSS{{ $request->id }}</p>
    </div>

    <!-- Next Steps -->
    <div class="next-steps">
        <h2>What happens next</h2>

        <div class="step">
            <span class="step-icon"><i class="fa-solid fa-1"></i></span>
            <p>We email & message your confirmation</p>
        </div>

        <div class="step">
            <span class="step-icon"><i class="fa-solid fa-2"></i></span>
            <p>An agent verifies and prepares your key</p>
        </div>

        <div class="step">
            <span class="step-icon"><i class="fa-solid fa-3"></i></span>
            <p>You pay on delivery and receive your code</p>
        </div>
    </div>

    <!-- Button -->
    <div class="action">
        <a href="{{ route('store.index') }}" class="continue-btn">
            Continue shopping <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>

</div>

@endsection

