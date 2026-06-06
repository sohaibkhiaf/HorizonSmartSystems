@extends('admin.layouts.app-admin')

@section('title', 'Modifier un utilisateur')
@section('page_title', 'Modifier un utilisateur')
@section('page_subtitle', 'Mettre à jour les informations de l\'utilisateur')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/users/form.css') }}">
@endsection

@section('content')

@include('admin.users.form', [
    'user' => $user,
    'buttonText' => 'Mettre à jour l\'utilisateur'
])

@endsection

