@extends('admin.layouts.admin')

@section('title', 'Créer une démo')
@section('page_title', 'Créer une démo')
@section('page_subtitle', 'Ajouter une nouvelle démo liée à un logiciel')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/software_demos/form.css') }}?v={{ config('app.asset_version') }}">
@endsection

@section('content')

@include('admin.software_demos.form', [
    'demo' => null,
    'publicSoftware' => $publicSoftware,
    'buttonText' => 'Créer la démo'
])

@endsection
