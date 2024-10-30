@extends('frontend.homepage.layout')
@section('content')
    <div class="cart-container">
        <div class="uk-container uk-container-center">
            @if ($errors->any())
                <div class="uk-alert uk-alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('cart.store') }}" class="uk-form form" method="post">
                @csrf
                <div class="cart-wrapper">
                    <div class="uk-grid uk-grid-medium">
                        <div class="uk-width-large-3-5">
                            <div class="panel-cart cart-left">
                                @include('frontend.cart.component.information')
                                @include('frontend.cart.component.method')
                                <button type="submit" class="cart-checkout" name="create" value="create">Thanh toán
                                    đơn
                                    hàng</button>
                            </div>
                        </div>
                        <div class="uk-width-large-2-5">
                            <div class="panel-cart">
                                <div class="panel-head">
                                    <h2 class="cart-heading"><span>Giỏ hàng</span></h2>
                                </div>
                                @include('frontend.cart.component.item')
                                @include('frontend.cart.component.voucher')
                                @include('frontend.cart.component.summary')
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        var province_id = '{{ isset($order->province_id) ? $order->province_id : old('province_id') }}'
        var district_id = '{{ isset($order->district_id) ? $order->district_id : old('district_id') }}'
        var ward_id = '{{ isset($order->ward_id) ? $order->ward_id : old('ward_id') }}'
        var getLocation = "{{ url('ajax/location/getLocation') }}";
    </script>
@endsection
