@extends('admin.layouts.admin')

@section('title', 'Modifier un utilisateur')
@section('page_title', 'Modifier un utilisateur')
@section('page_subtitle', 'Mettre à jour les informations de l\'utilisateur')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/users/form.css') }}?v={{ config('app.asset_version') }}">
@endsection

@section('content')

@include('admin.users.form', [
    'user' => $user,
    'buttonText' => 'Mettre à jour l\'utilisateur'
])

@endsection

