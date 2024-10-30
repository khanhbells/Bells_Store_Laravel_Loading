<div class="panel-foot">
    <h2 class="cart-heading"><span>Hình thức thanh toán</span></h2>
    <div class="cart-method mb30">
        @php
            $submitted = old('method') ? true : false;
        @endphp
        @foreach (__('payment.method') as $key => $val)
            <label for="{{ $val['name'] }}" class="uk-flex uk-flex-middle method-item">
                <input type="radio" name="method" value="{{ $val['name'] }}"
                    {{ old('method', '') == $val['name'] || (!old('method') && $key == 0) ? 'checked' : '' }}
                    id="{{ $val['name'] }}">
                <span class="image"><img src="{{ asset($val['image']) }}" alt=""></span>
                <span class="title">
                    {{ $val['title'] }}
                </span>
            </label>
        @endforeach
    </div>
    <div class="cart-return mb10">
        <span>{!! __('payment.return') !!}</span>
    </div>
</div>
