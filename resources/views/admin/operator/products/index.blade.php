@extends('admin.layouts.app-operator')

@section('page_title', 'Produits')
@section('page_subtitle', 'Sélection des produits disponibles')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/operator/products/index.css') }}">
@endsection

@section('content')

<div class="container">

    <table class="table">

        <thead>
            <tr>
                <th>Produit</th>
                <th>Description</th>
                <th style="text-align: right;">Prix</th>
            </tr>
        </thead>

        <tbody>
            @forelse($products as $product)
                <tr>
                    <td style="font-weight: 500;">
                        {{ $product->name }}
                    </td>

                    <td style="color: #777;">
                        {{ $product->description }}
                    </td>

                    <td style="font-weight: 500;text-align: right;">
                        {{ $product->price }} DA
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="empty">
                        Aucun produit trouvé
                    </td>
                </tr>
            @endforelse
        </tbody>

    </table>

</div>

@endsection

