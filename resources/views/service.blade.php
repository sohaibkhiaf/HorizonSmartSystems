@extends('layouts.app')

@section('title')
    @if(app()->getLocale() == 'ar')
        {{ $service->ar_name }}
    @elseif(app()->getLocale() == 'fr')
        {{ $service->fr_name }}
    @else
        {{ $service->en_name }}
    @endif
@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('css/service.css') }}">
@endsection

@section('content')

@php
    $serviceName = app()->getLocale() == 'ar' ? $service->ar_name : (app()->getLocale() == 'fr' ? $service->fr_name : $service->en_name);
    $serviceShort = app()->getLocale() == 'ar' ? $service->ar_short_description : (app()->getLocale() == 'fr' ? $service->fr_short_description : $service->en_short_description);
    $serviceDescription = app()->getLocale() == 'ar' ? $service->ar_description : (app()->getLocale() == 'fr' ? $service->fr_description : $service->en_description);
@endphp

<section class="service-hero" id="service_section" data-section="service">
    <div class="service-hero-container">

        <div class="service-hero-content">
            <span class="service-label">
                <i class="{{ $service->fa_icon ?? 'fa-solid fa-circle-info' }}"></i>
                Service
            </span>

            <h1>{{ $serviceName }}</h1>

            <p>{{ $serviceShort }}</p>

            <div class="service-actions">
                <a onclick="scrollToSection('contact_section')" class="btn btn-primary">
                    Demander ce service
                    <i class="fa-solid fa-paper-plane"></i>
                </a>

                <a href="/" class="btn btn-outline-light">
                    Retour à l'accueil
                    <i class="fa-solid fa-house"></i>
                </a>
            </div>
        </div>

        <div class="service-hero-image">
            @if($service->image)
                <img src="{{ asset('uploads/' . $service->image) }}" alt="{{ $serviceName }}">
            @else
                <div class="service-image-placeholder">
                    <i class="{{ $service->fa_icon ?? 'fa-solid fa-image' }}"></i>
                </div>
            @endif
        </div>

    </div>
</section>

<section class="service-details">
    <div class="service-details-container">

        <div class="service-main-card">
            <h2>{{ $serviceName }}</h2>

            <p>
                {!! nl2br(e($serviceDescription)) !!}
            </p>

            <a onclick="scrollToSection('contact_section')" class="service-cta">
                Contactez-nous
                <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
            </a>
        </div>

        <aside class="service-side-card">
            <h3>Autres services</h3>

            @foreach($otherServices as $item)
                @php
                    $itemName = app()->getLocale() == 'ar' ? $item->ar_name : (app()->getLocale() == 'fr' ? $item->fr_name : $item->en_name);
                @endphp

                <a href="{{ route('services.show', $item) }}" class="side-service-link">
                    <i class="{{ $item->fa_icon ?? 'fa-solid fa-circle-info' }}"></i>
                    <span>{{ $itemName }}</span>
                </a>
            @endforeach
        </aside>

    </div>
</section>

<script>
function scrollToSection(sectionId) {
    const element = document.getElementById(sectionId);
    if (!element) return;

    const offset = 80;
    const y = element.getBoundingClientRect().top + window.pageYOffset - offset;

    window.scrollTo({
        top: y,
        behavior: 'smooth'
    });
}
</script>

@endsection

