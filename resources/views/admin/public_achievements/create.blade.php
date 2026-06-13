@extends('admin.layouts.admin')

@section('title', 'Créer une réalisation')
@section('page_title', 'Créer une réalisation')
@section('page_subtitle', 'Ajouter un nouveau projet à votre site')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/achievements/form.css') }}">
@endsection

@section('content')

@include('admin.achievements.form', [
    'realisation' => null,
    'buttonText' => 'Créer une réalisation'
])

@endsection

