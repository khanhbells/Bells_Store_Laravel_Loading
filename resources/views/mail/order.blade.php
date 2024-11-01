<!DOCTYPE html>
<html>

<head>
    <style>
        .cart-success {
            padding: 30px 10px;
        }

        @media (min-width: 1220px) {
            .cart-success {
                width: 800px;
                margin: 0 auto;
            }
        }

        .cart-success .cart-heading {
            text-align: center;
            margin-bottom: 30px;
        }

        .cart-success .cart-heading>span {
            text-transform: uppercase;
            font-weight: 700;
        }

        .discover-text>* {
            display: inline-block;
            padding: 10px 25px;
            background: var(--primary-color);
            border-radius: 16px;
            cursor: pointer;
            color: #fff;
        }

        .discover-text {
            text-align: center;
        }

        .checkout-box {
            border: 1px solid #000;
            padding: 15px 20px;
            border-radius: 16px;
        }

        .cart-success .panel-body {
            margin-bottom: 40px;
        }

        .checkout-box-head {
            margin-bottom: 30px;
        }

        .checkout-box-head .order-title {
            border: 1px solid #000;
            border-radius: 16px;
            padding: 10px 15px;
            font-weight: 700;
            font-size: 16px;
        }

        .checkout-box-head .order-date {
            display: flex;
            align-items: center;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
        }

        .cart-success .table {
            width: 100%;
            border-spacing: 0;
            background: #d9d9d9;
            border-collapse: inherit;
        }

        .cart-success .table thead>tr th {
            color: #fff;
            background: var(--primary-color);
            font-weight: 500;
            font-size: 14px;
            vertical-align: middle;
            border-bottom: 2px solid #dee2e6;
            text-align: center;
            border: none;
            padding: 12px 15px;
        }

        .cart-success tbody tr td {
            padding: 12px 15px;
            vertical-align: middle;
            font-size: 14px;
            color: #000;
            border-bottom: 1px solid #ccc;
        }

        .cart-success tfoot {
            background: #fff;
        }

        .cart-success tfoot tr td {
            padding: 8px;
        }

        .cart-success .table td:last-child {
            text-align: right;
        }

        .cart-success .table tbody tr:nth-child(2n) td {
            background-color: #d9d9d9;
        }

        .total_payment {
            font-weight: bold;
            font-size: 24px;
        }

        .panel-foot .checkout-box div {
            margin-bottom: 15px;
            font-weight: 500;
        }

        .uk-text-left {
            text-align: left
        }

        .uk-text-center {
            text-align: center
        }

        .uk-text-right {
            text-align: right
        }
    </style>
</head>


<body>
    <div class="cart-success">
        <div class="panel-head">
            <h2 class="cart-heading"><span>Đặt hàng thành công</span></h2>
            <div class="discover-text mb20"><a href="{{ write_url('san-pham', true, true) }}">Khám phá thêm các sản phẩm
                    khác
                    tại
                    đây</a></div>
            <div class="panel-body">
                <h2 class="cart-heading"><span>Thông tin đơn hàng</span></h2>
                <div class="checkout-box">
                    <div class="checkout-box-head">
                        <div class="uk-grid uk-grid-medium">
                            <div class="uk-width-large-1-3"></div>
                            <div class="uk-width-large-1-3">
                                <div class="order-title uk-text-center">ĐƠN HÀNG #{{ $data['order']->code }}</div>
                            </div>

                            <div class="uk-width-large-1-3">
                                <div class="order-date">{{ convertDateTime($data['order']->created_at) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="checkout-box-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="uk-text-left">Tên sản phẩm</th>
                                    <th class="uk-text-center">Số lượng</th>
                                    <th class="uk-text-right">Giá niêm yết</th>
                                    <th class="uk-text-right">Giá bán</th>
                                    <th class="uk-text-right">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data['carts'] as $key => $val)
                                    @php
                                        $name = $val->name;
                                        $qty = $val->qty;
                                        $price = convert_price($val->price, true);
                                        $priceOriginal = convert_price($val->priceOriginal, true);
                                        $subtotal = convert_price($val->price * $qty, true);
                                    @endphp
                                    <tr>
                                        <td class="uk-text-left">{{ $name }}</td>
                                        <td class="uk-text-center">{{ $qty }}</td>
                                        <td class="uk-text-right">{{ $priceOriginal }}</td>
                                        <td class="uk-text-right">{{ $price }}</td>
                                        <td class="uk-text-right"><strong>{{ $subtotal }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4">Mã giảm giá</td>
                                    <td><strong>{{ $data['cartPromotion']['selectedPromotion']->code }}</strong></td>
                                </tr>

                                <tr>
                                    <td colspan="4">Tổng giá trị sản phẩm</td>
                                    <td><strong>{{ convert_price($data['cartCaculate']['discount'] + $data['cartCaculate']['cartTotal'], true) }}đ</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">Tổng giá trị khuyến mãi</td>
                                    <td><strong>{{ convert_price($data['cartCaculate']['discount'], true) }}đ</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">Phí giao hàng</td>
                                    <td><strong>0đ</strong></td>
                                </tr>
                                <tr class="total-payment">
                                    <td colspan="4"><span>Tổng thanh toán</span></td>
                                    <td><strong>{{ convert_price($data['cartCaculate']['cartTotal'], true) }}</strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="panel-foot">
                <h2 class="cart-heading"><span>Thông tin nhận hàng</span></h2>
                <div class="checkout-box">
                    <div>Tên người nhận: <span>{{ $data['order']->fullname }}</span></div>
                    <div>Email: <span>{{ $data['order']->email }}</span></div>
                    <div>Địa chỉ: <span>{{ $data['order']->address }}</span></div>
                    <div>Số điện thoại: <span>{{ $data['order']->phone }}</span></div>
                    <div>Hình thức thanh toán:
                        <span>{{ array_column(__('payment.method'), 'title', 'name')[$data['order']->method] ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
