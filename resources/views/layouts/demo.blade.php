<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8"/>
    
    <link rel="icon" href="{{asset('images/hss_logo_icon.png')}}"/>

    <title>@yield('title', 'Horizon Smart Systems')</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ config('app.asset_version') }}">
    <link rel="stylesheet" href="{{ asset('css/components/header.css') }}?v={{ config('app.asset_version') }}">
    <link rel="stylesheet" href="{{ asset('css/components/footer.css') }}?v={{ config('app.asset_version') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
    
    <!-- Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    @yield('style')
</head>
<body>

    {{-- Header --}}
    <x-header page="CUSTOMER" :services="$services" />

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

</body>
</html>

