@extends('admin.layouts.admin')

@section('title', 'Modifier une démo')
@section('page_title', 'Modifier une démo')
@section('page_subtitle', 'Mettre à jour les informations de la démo')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/software_demos/form.css') }}">
@endsection

@section('content')

@include('admin.software_demos.form', [
    'demo' => $demo,
    'publicSoftware' => $publicSoftware,
    'buttonText' => 'Mettre à jour la démo'
])

@endsection
