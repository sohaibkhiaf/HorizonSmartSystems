@extends('layouts.demo')

@section('title', $demo->name)

@section('style')
    <link rel="stylesheet" href="{{ asset('css/customer/demo.css') }}">
@endsection

@section('content')

<section class="demo-iframe-page">
    <iframe
        class="demo-iframe"
        src="{{ $demo->demo_link }}"
        title="{{ $demo->name }}"
        loading="lazy"
        allowfullscreen
        scrolling="yes"
        referrerpolicy="no-referrer-when-downgrade">
    </iframe>
</section>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const header = document.getElementById("main_header");

    function updateHeaderHeight() {
        if (!header) return;

        const headerHeight = Math.ceil(header.getBoundingClientRect().height);

        document.documentElement.style.setProperty(
            "--demo-header-height",
            headerHeight + "px"
        );
    }

    updateHeaderHeight();

    window.addEventListener("load", updateHeaderHeight);
    window.addEventListener("resize", updateHeaderHeight);

    window.addEventListener("orientationchange", () => {
        setTimeout(updateHeaderHeight, 300);
    });
});
</script>

@endsection

