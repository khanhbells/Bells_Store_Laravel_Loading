@extends('frontend.homepage.layout')
@section('content')
    <div class="product-catalogue page-wrapper">
        <div class="uk-container uk-container-center">
            @include('frontend.component.breadcrumb', [
                'model' => $productCatalogue,
                'breadcrumb' => $breadcrumb,
            ])
            <div class="panel-body">
                @include('frontend.product.catalogue.component.filter')
                @include('frontend.product.catalogue.component.filterContent')
                @if (!is_null($products))
                    <div class="product-list">
                        <div class="uk-grid uk-grid-medium">
                            @foreach ($products as $product)
                                <div class="uk-width-1-2 uk-width-small-1-2 uk-width-medium-1-3 uk-width-large-1-5 mb20">
                                    @include('frontend.component.product-item', [
                                        'product' => $product,
                                    ])
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="uk-flex uk-flex-center">
                        @include('frontend.component.pagination', ['model' => $products])
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
