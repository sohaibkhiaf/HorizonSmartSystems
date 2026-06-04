@extends('admin.layouts.app-admin')

@section('title', 'Créer un utilisateur')
@section('page_title', 'Créer un utilisateur')
@section('page_subtitle', 'Ajouter un nouveau utilisateur')

@section('content')

<div class="content-card">
    <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
        @csrf

        @include('admin.users.form', [
            'user' => null,
            'buttonText' => 'Créer un utilisateur'
        ])
    </form>
</div>

@endsection

