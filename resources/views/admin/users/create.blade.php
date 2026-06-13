@extends('admin.layouts.admin')

@section('title', 'Créer un utilisateur')
@section('page_title', 'Créer un utilisateur')
@section('page_subtitle', 'Ajouter un nouveau utilisateur')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/users/form.css') }}">
@endsection

@section('content')

@include('admin.users.form', [
    'user' => null,
    'buttonText' => 'Créer un utilisateur'
])

@endsection

