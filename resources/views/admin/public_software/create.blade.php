@extends('admin.layouts.admin')

@section('title', 'Créer un logiciel')
@section('page_title', 'Créer un logiciel')
@section('page_subtitle', 'Ajouter un nouveau logiciel à votre site')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/public_software/form.css') }}">
@endsection

@section('content')

@include('admin.public_software.form', [
    'software' => null,
    'buttonText' => 'Créer un logiciel'
])

@endsection

