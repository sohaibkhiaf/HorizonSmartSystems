@extends('admin.layouts.app-admin')

@section('page_title', 'Modifier produit')
@section('page_subtitle', 'Mettre à jour un produit')

@section('content')

@include('admin.products.form', [
    'action' => route('admin.products.update', $product->id),
    'method' => 'PUT',
    'product' => $product,
    'button' => 'Mettre à jour'
])

@endsection

