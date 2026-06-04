@extends('admin.layouts.app-admin')

@section('page_title', 'Ajouter un produit')
@section('page_subtitle', 'Créer un nouveau produit de vente')

@section('content')

@include('admin.products.form', [
    'action' => route('admin.products.store'),
    'method' => 'POST',
    'product' => null,
    'button' => 'Créer'
])

@endsection

