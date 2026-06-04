@extends('admin.layouts.app-admin')

@section('title', 'Modifier un utilisateur')
@section('page_title', 'Modifier un utilisateur')
@section('page_subtitle', 'Mettre à jour les informations de l\'utilisateur')

@section('content')

<div class="content-card">
    <form method="POST" action="{{ route('admin.users.update', $user) }}" 
        enctype="multipart/form-data">
        
        @csrf
        @method('PUT')

        @include('admin.users.form', [
            'user' => $user,
            'buttonText' => 'Mettre à jour l\'utilisateur'
        ])
    </form>
</div>

@endsection

