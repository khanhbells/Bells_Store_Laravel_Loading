<div class="panel-foot mt30">
    <div class="cart-summary-item">
        <div class="cart-summary">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <span class="summary-title">Giảm giá:</span>
                <div class="summary-value discount-value">
                    -{{ convert_price($cartPromotion['discount'], true) }}đ
                </div>
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
                    {{ count($carts) && !is_null($carts) ? convert_price($cartCaculate['cartTotal'] - $cartPromotion['discount'], true) : 0 }}đ
                </div>
            </div>
        </div>
    </div>
</div>
