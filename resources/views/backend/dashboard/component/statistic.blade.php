<div class="row">
    <div class="col-lg-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <span class="label label-success pull-right">Tháng</span>
                <h5>Đơn hàng trong tháng</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $orderStatistic['orderCurrentMonth'] }} đơn hàng</h1>
                {!! growHtml($orderStatistic['grow']) !!}
                <small>Tăng trưởng so với tháng trước</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <span class="label label-info pull-right">Tỷ lệ hủy chiếm</span>
                <h5>Tổng số đơn hàng</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $orderStatistic['totalOrders'] }} đơn hàng</h1>
                <div class="stat-percent font-bold text-info">
                    {{ cancelRate($orderStatistic['totalOrders'], $orderStatistic['cancleOrders']) }}%</div>
                <small class="text-danger">Số đơn hủy {{ $orderStatistic['cancleOrders'] }}</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <span class="label label-primary pull-right">Total</span>
                <h5>Tổng doanh thu</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ convert_price($orderStatistic['revenue'], true) }}đ</h1>
                <small>Tổng doanh thu</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <span class="label label-danger pull-right">Customer</span>
                <h5>Tổng số khách hàng</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $customerStatistic['totalCustomers'] }} khách hàng</h1>
                <small>Tổng số khách hàng</small>
            </div>
        </div>
    </div>
</div>
