@extends('layouts.app')

@section('title', __('systems.page_title'))

@section('style')
    <link rel="stylesheet" href="{{ asset('css/services/systems.css') }}">
@endsection

@section('content')

<!-- Hero -->
<section class="appdev-hero">
    <div class="appdev-hero-container">

        <div class="appdev-hero-text">
            <h1 class="appdev-hero-title">{{ __('systems.hero_title') }}</h1>
            <p class="appdev-hero-description">{{ __('systems.hero_description') }}</p>

            <div class="appdev-hero-buttons">
                <a href="/contact" class="appdev-btn appdev-btn-primary">
                    {{ __('systems.hero_btn_contact') }}
                    <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
                </a>
            </div>
        </div>

        <div class="appdev-hero-image">
            <img src="{{ asset('images/webdev.png') }}" alt="App Development">
        </div>

    </div>
</section>

<!-- Description -->
<section class="appdev-description">
    <div class="appdev-description-container">
        <h2>{{ __('systems.desc_title') }}</h2>
        <p>{{ __('systems.desc_text') }}</p>
    </div>
</section>


<!-- Types Section -->
<section class="website-types">

    <!-- Corporate -->
    <div class="website-type-card">
        <div class="type-header">
            <div class="type-icon">
                <i class="fa-solid fa-mobile-screen-button"></i>
            </div>

            <div>
                <h2>{{ __('systems.web_title') }}</h2>
                <p>{{ __('systems.web_subtitle') }}</p>
            </div>
        </div>

        <div class="type-content">
            <div class="features-box">
                <h3>{{ __('systems.corporate_features') }}</h3>

                <ul>
                    @foreach(__('systems.web_features') as $feature)
                        <li><i class="fa-solid fa-circle-check"></i> {{ $feature }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="price-box">
                <span>{{ __('systems.price_label') }}</span>
                <h3>{{ __('systems.web_price') }}</h3>
                <p>{{ __('systems.price_note') }}</p>
            </div>
        </div>
    </div>

</section>

<!-- CTA -->
<section class="appdev-cta">
    <div class="appdev-cta-overlay">

        <div class="appdev-cta-container">

            <div class="appdev-cta-left">
                <h2>{{ __('systems.cta_title') }}</h2>
                <h3>{{ __('systems.cta_subtitle') }}</h3>
            </div>

            <div class="appdev-cta-right">
                <a href="/contact" class="appdev-cta-btn">
                    {{ __('systems.cta_btn') }}
                    <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
                </a>
            </div>

        </div>

    </div>
</section>

@endsection

