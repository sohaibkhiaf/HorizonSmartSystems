@extends('admin.layouts.app-admin')

@section('page_title', 'Ajouter un produit')
@section('page_subtitle', 'Créer un nouveau produit de vente')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/products/form.css') }}">
@endsection

@section('content')

@include('admin.products.form', [
    'action' => route('admin.products.store'),
    'method' => 'POST',
    'product' => null,
    'button' => 'Créer'
])

@endsection

