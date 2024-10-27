@extends('frontend.homepage.layout')
@section('content')
    <div class="product-container">
        <div class="uk-container uk-container-center">
            @include('frontend.component.breadcrumb', [
                'model' => $productCatalogue,
                'breadcrumb' => $breadcrumb,
            ])
            <div class="panel-head">
                @include('frontend.component.product-detail', [
                    'product' => $product,
                    'productCatalogue' => $productCatalogue,
                ])
            </div>
        </div>
    </div>
@endsection
