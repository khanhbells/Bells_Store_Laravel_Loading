<div>
    <div class=" mb10 text-danger"><i>*Tổng cuối là tổng chưa bao gồm giảm giá</i></div>
</div>
<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>
                <input type="checkbox" value="" id="checkAll" class="input-checkbox">
            </th>
            <th class="text-center" style="width: 100px">Mã</th>
            <th class="text-center">Ngày tạo</th>
            {{-- <th class="text-center">Email</th> --}}
            <th class="text-center">Khách hàng</th>
            <th class="text-right">Giảm giá</th>
            <th class="text-right">Phí ship</th>
            <th class="text-right">Tổng cuối</th>
            <th class="text-center">Giao hàng</th>
            <th class="text-center">Trạng thái</th>
            <th class="text-center">Thanh toán</th>
            <th class="text-center">Hình thức</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($orders) && is_object($orders))
            @foreach ($orders as $order)
                <tr>
                    <td><input type="checkbox" value="{{ $order->id }}" class="input-checkbox checkBoxItem"></td>
                    <td style="color: blue;cursor: pointer;">
                        {{ $order->code }}
                    </td>
                    <td>
                        {{ convertDateTime($order->created_at, 'H:i d-m-Y') }}
                    </td>
                    <td>
                        <div><b>N:</b> {{ $order->fullname }}</div>
                        <div><b>P:</b> {{ $order->phone }}</div>
                        <div><b>A:</b> {{ $order->address }}</div>
                    </td>

                    <td class="text-right" style="color: red">
                        {{ convert_price($order->promotion['discount'], true) }}đ
                    </td>
                    <td class="text-right" style="color: blue;font-weight:600">
                        {{ convert_price($order->shipping, true) }}đ
                    </td>
                    <td class="text-right " style="color: blue;font-weight:600">
                        {{ convert_price($order->cart['cartTotal'], true) }}đ
                    </td>
                    <td>
                        {{ __('cart.delivery')[$order->delivery] }}
                    </td>
                    <td>
                        {{ __('cart.confirm')[$order->confirm] }}
                    </td>
                    <td>
                        {{ __('cart.payment')[$order->payment] }}
                    </td>
                    <td>
                        {{ array_column(__('payment.method'), 'title', 'name')[$order->method] ?? '-' }}
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
{{ $orders->links('pagination::bootstrap-4') }}
<script>
    var changeStatusUrl = "{{ url('ajax/dashboard/changeStatus') }}";
    var changeStatusAllUrl = "{{ url('ajax/dashboard/changeStatusAll') }}";
</script>
