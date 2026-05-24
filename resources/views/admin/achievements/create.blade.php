@extends('admin.layouts.app')

@section('title', 'Create Achievement')
@section('page_title', 'Create Achievement')
@section('page_subtitle', 'Add a new project to your website')

@section('content')

<div class="content-card">
    <form method="POST" action="{{ route('admin.achievements.store') }}" enctype="multipart/form-data">
        @csrf

        @include('admin.achievements.form', [
            'realisation' => null,
            'buttonText' => 'Create Achievement'
        ])
    </form>
</div>

@endsection

