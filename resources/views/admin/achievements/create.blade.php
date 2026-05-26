@extends('admin.layouts.app')

@section('title', 'Créer une réalisation')
@section('page_title', 'Créer une réalisation')
@section('page_subtitle', 'Ajouter un nouveau projet à votre site')

@section('content')

<div class="content-card">
    <form method="POST" action="{{ route('admin.achievements.store') }}" enctype="multipart/form-data">
        @csrf

        @include('admin.achievements.form', [
            'realisation' => null,
            'buttonText' => 'Créer une réalisation'
        ])
    </form>
</div>

@endsection

