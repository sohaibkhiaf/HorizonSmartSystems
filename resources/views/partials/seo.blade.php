@php
    $computedSeo = \App\Support\Seo::forView($seo ?? [], [
        'title' => $__env->yieldContent('title'),
        'seo_title' => $__env->yieldContent('seo_title'),
        'description' => $__env->yieldContent('description'),
        'meta_description' => $__env->yieldContent('meta_description'),
        'robots' => $__env->yieldContent('robots'),
        'canonical' => $__env->yieldContent('canonical'),
        'og_image' => $__env->yieldContent('og_image'),
        'og_type' => $__env->yieldContent('og_type'),
    ]);
@endphp

<title>{{ $computedSeo['title'] }}</title>
<meta name="description" content="{{ $computedSeo['description'] }}">
<meta name="robots" content="{{ $computedSeo['robots'] }}">
<link rel="canonical" href="{{ $computedSeo['canonical'] }}">

@if($computedSeo['indexable'])
    @foreach($computedSeo['alternates'] as $hreflang => $href)
        <link rel="alternate" hreflang="{{ $hreflang }}" href="{{ $href }}">
    @endforeach

    <meta property="og:title" content="{{ $computedSeo['title'] }}">
    <meta property="og:description" content="{{ $computedSeo['description'] }}">
    <meta property="og:url" content="{{ $computedSeo['canonical'] }}">
    <meta property="og:type" content="{{ $computedSeo['type'] }}">
    <meta property="og:image" content="{{ $computedSeo['image'] }}">
    <meta property="og:locale" content="{{ $computedSeo['locale'] }}">

    @foreach($computedSeo['alternate_og_locales'] as $ogLocale)
        <meta property="og:locale:alternate" content="{{ $ogLocale }}">
    @endforeach

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $computedSeo['title'] }}">
    <meta name="twitter:description" content="{{ $computedSeo['description'] }}">
    <meta name="twitter:image" content="{{ $computedSeo['image'] }}">

    @foreach($computedSeo['json_ld'] as $jsonLd)
        <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}</script>
    @endforeach
@endif
