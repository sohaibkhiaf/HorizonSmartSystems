@extends('admin.layouts.app-admin')

@section('title', 'Créer un service')
@section('page_title', 'Créer un service')
@section('page_subtitle', 'Ajouter un nouveau service à votre site')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/public_services/form.css') }}">
@endsection

@section('content')

@include('admin.public_services.form', [
    'service' => null,
    'buttonText' => 'Créer un service'
])

@endsection

