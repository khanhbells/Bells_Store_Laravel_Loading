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
        <input type="text" placeholder="Chọn mã giảm giá" name="voucher" value="" readonly>
        <a href="" class="apply-voucher">Áp dụng</a>
    </div>
</div>
