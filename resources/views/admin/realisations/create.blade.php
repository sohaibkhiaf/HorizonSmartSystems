@extends('admin.layouts.app')

@section('title', 'Create Realisation')
@section('page_title', 'Create Realisation')
@section('page_subtitle', 'Add a new project to your website')

@section('content')

<div class="content-card">
    <form method="POST" action="{{ route('admin.realisations.store') }}" enctype="multipart/form-data">
        @csrf

        @include('admin.realisations._form', [
            'realisation' => null,
            'buttonText' => 'Create Realisation'
        ])
    </form>
</div>

@endsection

