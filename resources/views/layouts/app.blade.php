<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <link rel="icon" href="{{asset('images/hss_logo_icon.png')}}"/>

    <title>@yield('title', 'Horizon Smart Systems')</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/footer.css') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <!-- Swiper -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
    @yield('style')
</head>
<body>

    {{-- Header --}}
    <x-header :page="$page" />

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <x-footer />

    <script type="module" src="{{ asset('js/app.js') }}" ></script>
    <script type="module" src="{{ asset('js/components/header.js') }}" ></script>

    @yield('script')
</body>
</html>

