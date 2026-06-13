@extends('admin.layouts.admin')

@section('page_title', 'Ajouter un produit')
@section('page_subtitle', 'Créer un nouveau produit de vente')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/products/form.css') }}">
@endsection

@section('content')

@include('admin.store_subscriptions.form', [
    'action' => route('admin.store_subs.store'),
    'method' => 'POST',
    'subscription' => null,
    'button' => 'Créer'
])

@endsection

