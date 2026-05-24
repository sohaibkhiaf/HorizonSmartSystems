@extends('admin.layouts.app')

@section('title', 'Edit Realisation')
@section('page_title', 'Edit Realisation')
@section('page_subtitle', 'Update realisation information')

@section('content')

<div class="content-card">
    <form method="POST" action="{{ route('admin.realisations.update', $achievement) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('admin.realisations._form', [
            'realisation' => $achievement,
            'buttonText' => 'Update Realisation'
        ])
    </form>
</div>

@endsection
