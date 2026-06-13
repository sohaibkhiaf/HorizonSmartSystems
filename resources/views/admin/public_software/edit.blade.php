@extends('admin.layouts.admin')

@section('title', 'Modifier un logiciel')
@section('page_title', 'Modifier un logiciel')
@section('page_subtitle', 'Mettre à jour les informations de le logiciel')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/public_software/form.css') }}">
@endsection

@section('content')

@include('admin.public_software.form', [
    'software' => $software,
    'buttonText' => 'Mettre à jour le logiciel'
])

@endsection

