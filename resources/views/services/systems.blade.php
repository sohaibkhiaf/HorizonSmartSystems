@extends('layouts.app')

@section('title', __('systems.page_title'))

@section('style')
    <link rel="stylesheet" href="{{ asset('css/service.css') }}">
@endsection

@section('content')

<!-- Hero  -->
<section class="hero">
    <div class="hero-container">

        <div class="hero-text">
            <h1 class="hero-title">{{ __('systems.hero_title') }}</h1>
            <p class="hero-description">
                {{ __('systems.hero_description') }}
            </p>

            <div class="hero-buttons">
                <a href="/contact" class="btn btn-primary">
                    {{ __('systems.hero_btn_contact') }}
                    <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
                </a>
            </div>
        </div>

        <div class="hero-image">
            <img src="{{ asset('images/erp.png') }}" alt="{{ __('systems.hero_title') }}">
        </div>

    </div>
</section>

<!-- Description -->
<section class="description">
    <div class="description-container">
        <h2>{{ __('systems.desc_title') }}</h2>
        <p>{{ __('systems.desc_text') }}</p>
    </div>
</section>


<!-- Service Types  -->
<section class="service-types">

    <div class="service-type-card">
        <div class="type-header">
            <div class="type-icon">
                <i class="fa-solid fa-gears"></i>
            </div>

            <div>
                <h2>{{ __('systems.systems_title') }}</h2>
                <p>{{ __('systems.systems_subtitle') }}</p>
            </div>
        </div>

        <div class="type-content">
            <div class="features-box">
                <h3>{{ __('systems.core_features') }}</h3>

                <ul>
                    @foreach(__('systems.systems_features') as $feature)
                        <li><i class="fa-solid fa-circle-check"></i> {{ $feature }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="price-box">
                <span>{{ __('systems.price_label') }}</span>
                <h3>{{ __('systems.system_price') }}</h3>
                <p>{{ __('systems.price_note') }}</p>
            </div>
        </div>
    </div>

</section>

<!-- CTA  -->
<section class="cta">
    <div class="cta-overlay">

        <div class="cta-container">

            <div class="cta-left">
                <h2>{{ __('systems.cta_title') }}</h2>
                <h3>{{ __('systems.cta_subtitle') }}</h3>
            </div>

            <div class="cta-right">
                <a href="/contact" class="cta-btn">
                    {{ __('systems.cta_btn') }}
                    <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
                </a>
            </div>

        </div>

    </div>
</section>

@endsection

