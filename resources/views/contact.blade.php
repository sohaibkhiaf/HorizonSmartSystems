@extends('layouts.app')

@section('title', __('contact.page_title'))

@section('style')
<link rel="stylesheet" href="{{ asset('css/contact.css') }}">
@endsection


@section('content')

<!-- Hero Banner -->
<section class="hero-about">
    <div class="hero-overlay">
        <div class="hero-content">
            <p>{{ __('contact.banner_label') }}</p>
            <h1>{{ __('contact.banner_title') }}</h1>
            <p>{{ __('contact.banner_desc_1') }}
                <br> {{ __('contact.banner_desc_2') }}
            </p>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="contact-section">
    <div class="contact-container">

        <!-- LEFT: CONTACT INFOS -->
        <div class="contact-info">

            <h2 class="contact-title">{{ __('contact.contact_title') }}</h2>

            <!-- Phone -->
            <div class="contact-item">
                <i class="fas fa-phone"></i>
                <div>
                    <h4>{{ __('contact.phone_label') }}</h4>
                    <p>0561 65 51 00
                        <br> 0782 94 07 60
                        <br> 0540 29 62 00
                    </p>
                </div>
            </div>

            <!-- Email -->
            <div class="contact-item">
                <i class="fas fa-envelope"></i>
                <div>
                    <h4>{{ __('contact.email_label') }}</h4>
                    <p>contact@hss-agency.com</p>
                </div>
            </div>

            <!-- Facebook -->
            <div class="contact-item">
                <i class="fab fa-facebook-f"></i>
                <div>
                    <h4>{{ __('contact.facebook_label') }}</h4>
                    <p>facebook.com/HorizonSmartSystems</p>
                </div>
            </div>

            <!-- Address -->
            <div class="contact-item">
                <i class="fas fa-location-dot"></i>
                <div>
                    <h4>{{ __('contact.address_label') }}</h4>
                    <p>{{ __('contact.address_value') }}</p>
                </div>
            </div>

        </div>

        <!-- RIGHT: CONTACT FORM -->
        <div class="contact-form">

            <h2>{{ __('contact.form_title') }}</h2>

            @if(session('success'))
                <div class="form-alert success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="form-alert danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('contact.store') }}">
                @csrf
                <div class="form-grid">

                    <div class="form-group">
                        <label>{{ __('contact.form_fullname') }}</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="{{ __('contact.form_fullname_ph') }}" required>
                    </div>

                    <div class="form-group">
                        <label>{{ __('contact.form_email') }}</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="{{ __('contact.form_email_ph') }}">
                    </div>

                    <div class="form-group">
                        <label>{{ __('contact.form_phone') }}</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="{{ __('contact.form_phone_ph') }}" required>
                    </div>

                    <div class="form-group">
                        <label>{{ __('contact.form_subject') }}</label>
                        <input type="text" name="subject" value="{{ old('subject') }}" placeholder="{{ __('contact.form_subject_ph') }}" required>
                    </div>

                </div>

                <div class="form-group message-group">
                    <label>{{ __('contact.form_message') }}</label>
                    <textarea name="message" placeholder="{{ __('contact.form_message_ph') }}" required>{{ old('message') }}</textarea>
                </div>

                <div class="form-check">
                    <input type="checkbox" id="consent">
                    <label for="consent">
                        {{ __('contact.form_consent') }}
                    </label>
                </div>

                <button type="submit" class="btn-submit">
                    {{ __('contact.form_submit') }} <i class="fa-regular fa-paper-plane"></i>
                </button>
            </form>

        </div>

    </div>
</section>

<!-- Map & Hours Section -->
<section class="map-hours-section">

    <div class="map-hours-box">

        <!-- MAP -->
        <div class="map-part">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3274.1601452089976!2d5.7204922742544975!3d34.85220317286611!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x12f509002f0dc033%3A0x427675b9fa5b3b87!2sHORIZON%20PC%20INFORMATIQUE!5e0!3m2!1sen!2sdz!4v1779480198528!5m2!1sen!2sdz"
                allowfullscreen=""
                loading="lazy">
            </iframe>
        </div>

        <!-- HOURS -->
        <div class="hours-part">
            <div class="hours-header">
                <i class="fas fa-clock"></i>
                <h3>{{ __('contact.hours_title') }}</h3>
            </div>

            <div class="hours-content">
                <div class="hour-row">
                    <span>{{ __('contact.saturday') }} - {{ __('contact.wednesday') }}</span>
                    <span>08:30 - 16:30</span>
                </div>

                <div class="hour-row closed">
                    <span>{{ __('contact.thursday') }}</span>
                    <span>{{ __('contact.closed') }}</span>
                </div>

                <div class="hour-row closed">
                    <span>{{ __('contact.friday') }}</span>
                    <span>{{ __('contact.closed') }}</span>
                </div>
            </div>
        </div>

    </div>

</section>

@endsection

