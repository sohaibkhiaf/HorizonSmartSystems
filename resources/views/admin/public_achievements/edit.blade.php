@extends('admin.layouts.admin')

@section('title', 'Modifier une réalisation')
@section('page_title', 'Modifier une réalisation')
@section('page_subtitle', 'Mettre à jour les informations de la réalisation')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/public_achievements/form.css') }}">
@endsection

@section('content')

@include('admin.public_achievements.form', [
    'achievement' => $achievement,
    'buttonText' => 'Mettre à jour la réalisation'
])

@endsection

