@extends('frontend.homepage.layout')
@section('content')
    <div class="cart-container">
        <div class="uk-container uk-container-center">
            <form action="uk-form form" method="post">
                @csrf
                <div class="cart-wrapper">
                    <div class="uk-grid uk-grid-medium">
                        <div class="uk-width-large-3-5">
                            <div class="panel-cart cart-left">
                                <div class="panel-head">
                                    <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                        <h2 class="cart-heading">
                                            <span>Thông tin đặt hàng</span>
                                        </h2>
                                        <span class="has-account">Bạn đã có tài khoản <a href=""
                                                title="Đăng nhập ngay">Đăng nhập
                                                ngay</a></span>
                                    </div>
                                </div>
                                <div class="panel-body mb30">
                                    <div class="cart-infomation">
                                        <div class="uk-grid uk-grid-medium mb20">
                                            <div class="uk-width-large-1-2">
                                                <div class="form-row">
                                                    <input type="text" name="fullname" value=""
                                                        placeholder="Nhập vào họ tên" class="input-text">
                                                </div>
                                            </div>
                                            <div class="uk-width-large-1-2">
                                                <div class="form-row">
                                                    <input type="text" name="phone" value=""
                                                        placeholder="Nhập vào số điện thoại" class="input-text">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-row mb20">
                                            <input type="email" name="email" value="" placeholder="Nhập vào email"
                                                class="input-text">
                                        </div>
                                        <div class="uk-grid uk-grid-medium mb20">
                                            <div class="uk-width-large-1-3">
                                                <select name="province_id" id=""
                                                    class="province location setupSelect2" data-target="districts">
                                                    <option value="0">[Chọn Thành Phố]</option>
                                                    @foreach ($provinces as $key => $val)
                                                        <option value="{{ $val->code }}">{{ $val->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="uk-width-large-1-3">
                                                <select name="district_id" id=""
                                                    class="setupSelect2 districts location" data-target="wards">
                                                    <option value="0">Chọn Quận Huyện</option>
                                                </select>
                                            </div>
                                            <div class="uk-width-large-1-3">
                                                <select name="ward_id" id="" class="setupSelect2 wards">
                                                    <option value="0">Chọn Phường Xã</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-row mb20">
                                            <input type="text" name="address" value=""
                                                placeholder="Nhập vào địa chỉ" class="input-text">
                                        </div>
                                        <div class="form-row">
                                            <input type="text" name="description" value=""
                                                placeholder="Ghi chú thêm (VD: Giao hàng vào lúc 15h)" class="input-text">
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-foot">
                                    <h2 class="cart-heading"><span>Hình thức thanh toán</span></h2>
                                    <div class="cart-method mb30">
                                        @foreach (__('payment.method') as $key => $val)
                                            <label for="{{ $val['name'] }}" class="uk-flex uk-flex-middle method-item">
                                                <input type="radio" name="method" value="{{ $val['name'] }}"
                                                    {{ $key == 0 ? 'checked' : '' }} id="{{ $val['name'] }}">
                                                <span class="image"><img src="{{ asset($val['image']) }}"
                                                        alt=""></span>
                                                <span class="title">
                                                    {{ $val['title'] }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <div class="cart-return mb10">
                                        <span>{!! __('payment.return') !!}</span>
                                    </div>
                                    <button type="submit" class="cart-checkout" name="create" value="create">Thanh toán
                                        đơn
                                        hàng</button>
                                </div>
                            </div>
                        </div>
                        <div class="uk-width-large-2-5">
                            <div class="panel-cart">
                                <div class="panel-head">
                                    <h2 class="cart-heading"><span>Giỏ hàng</span></h2>
                                </div>
                                <div class="panel-body">

                                    @if (count($carts) && !is_null($carts))
                                        <div class="cart-list">
                                            @php
                                                $total = 0;
                                            @endphp
                                            @foreach ($carts as $keyCart => $cart)
                                                @php
                                                    $total = $total + $cart->price * $cart->qty;
                                                @endphp
                                                <div class="cart-item">
                                                    <div class="uk-grid uk-grid-medium">
                                                        <div class="uk-width-small-1-1 uk-width-medium-1-5">
                                                            <div class="cart-item-image">
                                                                <span class="image img-scaledown"><img
                                                                        src="{{ asset($cart->image) }}"
                                                                        alt=""></span>
                                                                <span class="cart-item-number">{{ $cart->qty }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="uk-width-small-1-1 uk-width-medium-4-5">
                                                            <div class="cart-item-info">
                                                                <h3 class="title"><span>{{ $cart->name }}</span></h3>
                                                                <div
                                                                    class="cart-item-action uk-flex uk-flex-middle uk-flex-space-between">
                                                                    <div class="cart-item-qty">
                                                                        <button type="button"
                                                                            class="btn-qty minus">-</button>
                                                                        <input type="text" class="input-qty"
                                                                            value="{{ $cart->qty }}" type="">
                                                                        <button type="button"
                                                                            class="btn-qty plus">+</button>
                                                                    </div>
                                                                    <div class="cart-item-price">
                                                                        <div class="uk-flex uk-flex-bottom">
                                                                            {{-- @if ($cart->price != $cart->priceOriginal)
                                                                                <span
                                                                                    class="cart-price-old">{{ convert_price($cart->priceOriginal, true) }}đ</span>
                                                                            @endif --}}
                                                                            <span
                                                                                class="cart-price-sale ml10">{{ convert_price($cart->price * $cart->qty, true) }}đ</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="cart-item-remove">
                                                                        <span>X</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="panel-voucher uk-hidden">
                                    <div class="voucher-list">
                                        @for ($i = 0; $i < 5; $i++)
                                            <div class="voucher-item {{ $i == 0 ? 'active' : '' }}">
                                                <div class="voucher-left"></div>
                                                <div class="voucher-right">
                                                    <div class="voucher-title">FREESHIP <span>(Còn 20)</span></div>
                                                    <div class="voucher-description">
                                                        <p>Khuyến mãi giảm giá đến 100% phí vận chuyển</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                    <div class="voucher-form">
                                        <input type="text" placeholder="Chọn mã giảm giá" name="voucher"
                                            value="" readonly>
                                        <a href="" class="apply-voucher">Áp dụng</a>
                                    </div>
                                </div>
                                <div class="panel-foot mt30">
                                    <div class="cart-summary-item">
                                        <div class="cart-summary">
                                            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                                <span class="summary-title">Giảm giá:</span>
                                                <div class="summary-value">-0đ</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="cart-summary-item">
                                        <div class="cart-summary">
                                            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                                <span class="summary-title">Phí giao hàng:</span>
                                                <div class="summary-value">Miễn phí</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="cart-summary-item">
                                        <div class="cart-summary">
                                            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                                <span class="summary-title bold">Tổng tiền:</span>
                                                <div class="summary-value cart-total">
                                                    {{ convert_price($total, true) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
