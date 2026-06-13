@extends('admin.layouts.admin')

@section('title', 'Modifier un service')
@section('page_title', 'Modifier un service')
@section('page_subtitle', 'Mettre à jour les informations de le service')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/public_services/form.css') }}">
@endsection

@section('content')

@include('admin.public_services.form', [
    'service' => $service,
    'buttonText' => 'Mettre à jour le service'
])

@endsection

