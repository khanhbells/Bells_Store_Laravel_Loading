@extends('frontend.homepage.layout')
@section('content')
    <div class="product-container">
        <div class="uk-container uk-container-center">
            @include('frontend.component.breadcrumb', [
                'model' => $productCatalogue,
                'breadcrumb' => $breadcrumb,
            ])
            <div class="panel-body">
                @include('frontend.product.product.component.detail', [
                    'product' => $product,
                    'productCatalogue' => $productCatalogue,
                ])
            </div>
        </div>
    </div>
@endsection
