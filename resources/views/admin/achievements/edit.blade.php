@extends('admin.layouts.app')

@section('title', 'Modifier une réalisation')
@section('page_title', 'Modifier une réalisation')
@section('page_subtitle', 'Mettre à jour les informations de la réalisation')

@section('content')

<div class="content-card">
    <form method="POST" action="{{ route('admin.achievements.update', $achievement) }}" 
        enctype="multipart/form-data">
        
        @csrf
        @method('PUT')

        @include('admin.achievements.form', [
            'achievement' => $achievement,
            'buttonText' => 'Mettre à jour la réalisation'
        ])
    </form>
</div>

@endsection

