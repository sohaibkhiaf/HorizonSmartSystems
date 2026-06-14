@extends('admin.layouts.admin')

@section('page_title', 'Modifier abonnement')
@section('page_subtitle', 'Mettre à jour un abonnement')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/store_subscriptions/form.css') }}">
@endsection

@section('content')

@include('admin.store_subscriptions.form', [
    'action' => route('admin.store_subs.update', $subscription->id),
    'method' => 'PUT',
    'subscription' => $subscription,
    'button' => 'Mettre à jour'
])

@endsection

