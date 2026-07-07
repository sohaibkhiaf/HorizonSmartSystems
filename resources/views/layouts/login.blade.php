<!DOCTYPE html>
<html lang="{{ \App\Support\Seo::htmlLang() }}" dir="{{ \App\Support\Seo::htmlDir() }}">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="icon" href="{{asset('images/hss_logo_icon.png')}}"/>

    @include('partials.seo', ['seo' => $seo ?? []])

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
    <x-header page="AUTH" :services="$services" />

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <x-footer page="AUTH" :services="$services" :hero="$hero" />

</body>
</html>
