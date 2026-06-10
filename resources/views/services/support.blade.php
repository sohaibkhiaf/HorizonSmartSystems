@extends('layouts.service')

@section('title', __('support.page_title'))

@section('style')
    <link rel="stylesheet" href="{{ asset('css/service.css') }}">
@endsection

@section('content')

<!-- Description ------------------------------------------------------------- -->
<section class="description" >
    <div class="description-container">
        <h2>{{ __('support.desc_title') }}</h2>
        <p>{{ __('support.desc_text') }}</p>
    </div>
</section>


<!-- Types ------------------------------------------------------------------- -->
<section class="service-types" >

    <!-- Corporate -->
    <div class="service-type-card">
        <div class="type-header">
            <div class="type-icon">
                <i class="fa-solid fa-wrench"></i>
            </div>

            <div>
                <h2>{{ __('support.support_title') }}</h2>
                <p>{{ __('support.support_subtitle') }}</p>
            </div>
        </div>

        <div class="type-content">
            <div class="features-box">
                <h3>{{ __('support.core_features') }}</h3>

                <ul>
                    @foreach(__('support.support_features') as $feature)
                        <li><i class="fa-solid fa-circle-check"></i> {{ $feature }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="price-box">
                <span>{{ __('support.price_label') }}</span>
                <h3>{{ __('support.support_price') }}</h3>
                <p>{{ __('support.price_note') }}</p>
            </div>
        </div>
    </div>

</section>

<!-- Contact ------------------------------------------------------------------- -->
<section class="contact-section" id="contactSection" data-section="contact">

    <div class="contact-heading">
        <h2>{{ __('home.contact_title') }}</h2>
    </div>

    <div class="contact-container">

        <!-- LEFT: CONTACT INFOS -->
        <div class="contact-info">

            <div class="contact-item">
                <i class="fas fa-phone"></i>
                <div>
                    <h4>{{ __('home.phone_label') }}</h4>
                    <p>
                        0561 65 51 00
                        <br>0782 94 07 60
                        <br>0540 29 62 00
                    </p>
                </div>
            </div>

            <div class="contact-item">
                <i class="fas fa-envelope"></i>
                <div>
                    <h4>{{ __('home.email_label') }}</h4>
                    <p>contact@hss-agency.com</p>
                </div>
            </div>

            <div class="contact-item">
                <i class="fab fa-facebook-f"></i>
                <div>
                    <h4>{{ __('home.facebook_label') }}</h4>
                    <p>facebook.com/HorizonSmartSystems</p>
                </div>
            </div>

            <div class="contact-item">
                <i class="fas fa-location-dot"></i>
                <div>
                    <h4>{{ __('home.address_label') }}</h4>
                    <p>{{ __('home.address_value') }}</p>
                </div>
            </div>

            <div class="contact-item">
                <i class="fas fa-clock"></i>
                <div>
                    <h4>{{ __('home.hours_title') }}</h4>

                    <div class="contact-hours">
                        <div class="hour-row">
                            <span>{{ __('home.saturday') }} - {{ __('home.wednesday') }}</span>
                            <span>08:30 - 16:30</span>
                        </div>

                        <div class="hour-row closed">
                            <span>{{ __('home.thursday') }}</span>
                            <span>{{ __('home.closed') }}</span>
                        </div>

                        <div class="hour-row closed">
                            <span>{{ __('home.friday') }}</span>
                            <span>{{ __('home.closed') }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- RIGHT: CONTACT FORM -->
        <div class="contact-form">

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
                        <label>{{ __('home.form_fullname') }}</label>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="{{ __('home.form_fullname_ph') }}"
                            required>
                    </div>

                    <div class="form-group">
                        <label>{{ __('home.form_email') }}</label>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="{{ __('home.form_email_ph') }}">
                    </div>

                    <div class="form-group">
                        <label>{{ __('home.form_phone') }}</label>
                        <input
                            type="tel"
                            name="phone"
                            value="{{ old('phone') }}"
                            placeholder="{{ __('home.form_phone_ph') }}"
                            required>
                    </div>

                    <div class="form-group">
                        <label>{{ __('home.form_subject') }}</label>
                        <input
                            type="text"
                            name="subject"
                            value="{{ old('subject') }}"
                            placeholder="{{ __('home.form_subject_ph') }}"
                            required>
                    </div>
                </div>

                <div class="form-group message-group">
                    <label>{{ __('home.form_message') }}</label>
                    <textarea
                        name="message"
                        placeholder="{{ __('home.form_message_ph') }}"
                        required>{{ old('message') }}</textarea>
                </div>

                <div class="form-check">
                    <input type="checkbox" id="consent">
                    <label for="consent">
                        {{ __('home.form_consent') }}
                    </label>
                </div>

                <button type="submit" class="btn-submit">
                    {{ __('home.form_submit') }}
                    <i class="fa-regular fa-paper-plane"></i>
                </button>
            </form>

        </div>

    </div>
</section>


@endsection



