@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['detail']['title']])
<div class="order-wrapper">
    <div class="row">
        <div class="col-lg-8">
            <div class="ibox">
                <div class="ibox-title">
                    <div class="uk-flex uk-flex-middle uk-flex-space-between">
                        <div class="ibox-title-left">
                            <span>Chi tiết đơn hàng</span>
                            <span class="badge">
                                <div class="badge__tip">
                                </div>
                                <div class="badge-text">Chưa giao</div>
                            </span>
                        </div>
                        <div class="ibox-title-right">
                            Nguồn: Website
                        </div>
                    </div>
                </div>
                <div class="ibox-content">
                    <table class="table-order">
                        <tbody>
                            @foreach ($order->products as $key => $val)
                                @php
                                    $name = $val->pivot->name;
                                    $qty = $val->pivot->qty;
                                    $price = convert_price($val->pivot->price, true);
                                    $priceOriginal = convert_price($val->pivot->priceOriginal, true);
                                    $subtotal = convert_price($val->pivot->price * $qty, true);
                                    $image = $val->image;
                                @endphp
                                <tr class="order-item">
                                    <td>
                                        <div class="image">
                                            <span class="image img-scaledown">
                                                <img src="{{ asset($image) }}" alt="">
                                            </span>
                                        </div>
                                    </td>
                                    <td style="width: 285px;">
                                        <div class="order-item-name" title="{{ $name }}">{{ $name }}
                                        </div>
                                        <div class="order-item-voucher">Mã giảm giá: Không có</div>
                                    </td>
                                    <td>
                                        <div class="order-item-price">{{ $price }}đ</div>
                                    </td>
                                    <td>
                                        <div class="order-item-times">x</div>
                                    </td>
                                    <td>
                                        <div class="order-item-qty">{{ $qty }}</div>
                                    </td>
                                    <td>
                                        <div class="order-item-subtotal">
                                            {{ $subtotal }} đ
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="5" class="text-right">Tổng tạm</td>
                                <td class="text-right">
                                    {{ convert_price($order->promotion['discount'] + $order->cart['cartTotal'], true) }}
                                    đ</td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-right">Giảm giá</td>
                                <td class="text-right">- {{ convert_price($order->promotion['discount'], true) }} đ</td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-right">Vận chuyển</td>
                                <td class="text-right">0 đ</td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-right"><strong>Tổng cuối</strong></td>
                                <td class="text-right"><strong>{{ convert_price($order->cart['cartTotal'], true) }}
                                        đ</strong></td>
                            </tr>
                        </tbody>

                    </table>
                </div>
                <div class="payment-confirm">
                    <div class="uk-flex uk-flex-middle uk-flex-space-between">
                        <div class="uk-flex uk-flex-middle">
                            <span class="icon"><img src="{{ asset('backend/img/warning.png') }}"
                                    alt=""></span>
                            <div class="payment-title">
                                <div class="text_1">
                                    <span class="isConfirm">ĐANG CHỜ XÁC NHẬN ĐƠN HÀNG</span>
                                    20.000.000 đ
                                </div>
                                <div class="text_2">
                                    Thanh toán khi nhận hàng (COD)
                                </div>
                            </div>
                        </div>
                        <div class="cancle-block">
                            <button class="button">Hủy đơn</button>
                        </div>
                    </div>
                </div>
                <div class="payment-confirm">
                    <div class="uk-flex uk-flex-middle uk-flex-space-between">
                        <div class="uk-flex uk-flex-middle">
                            <span class="icon"><i class="fa fa-truck"></i></span>
                            <div class="payment-title">
                                <div class="text_1">
                                    Xác nhận đơn hàng
                                </div>
                            </div>
                        </div>
                        <div class="cancle-block">
                            <button class="button confirm">Xác nhận</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 order-aside">
            <div class="ibox">
                <div class="ibox-title">
                    <div class="uk-flex uk-flex-middle uk-flex-space-between">
                        <span>Ghi chú</span>
                        <div class="edit span">Sửa</div>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="description">
                        {{ $order->description }}
                    </div>
                </div>
            </div>
            <div class="ibox">
                <div class="ibox-title">
                    <div class="uk-flex uk-flex-middle uk-flex-space-between">
                        <h5>Thông tin khách hàng</h5>
                        <div class="edit span">Sửa</div>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="customer-line">
                        <strong>N:</strong>
                        {{ $order->fullname }}
                    </div>
                    <div class="customer-line">
                        <strong>E:</strong>
                        {{ $order->email }}
                    </div>
                    <div class="customer-line">
                        <strong>P:</strong>
                        {{ $order->phone }}
                    </div>
                    <div class="customer-line">
                        <strong>A:</strong>
                        {{ $order->address }}

                    </div>
                    <div class="customer-line">
                        <strong>P:</strong>
                        {{ $order->ward_name }}
                    </div>
                    <div class="customer-line">
                        <strong>Q:</strong>
                        {{ $order->district_name }}
                    </div>
                    <div class="customer-line">
                        <strong>T:</strong>
                        {{ $order->province_name }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
