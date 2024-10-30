<div class="panel-head">
    <div class="uk-flex uk-flex-middle uk-flex-space-between">
        <h2 class="cart-heading">
            <span>Thông tin đặt hàng</span>
        </h2>
        <span class="has-account">Bạn đã có tài khoản <a href="" title="Đăng nhập ngay">Đăng nhập
                ngay</a></span>
    </div>
</div>
<div class="panel-body mb30">
    <div class="cart-infomation">
        <div class="uk-grid uk-grid-medium mb20">
            <div class="uk-width-large-1-2">
                <div class="form-row">
                    <input type="text" name="fullname" value="{{ old('fullname') }}" placeholder="Nhập vào họ tên"
                        class="input-text">
                </div>
            </div>
            <div class="uk-width-large-1-2">
                <div class="form-row">
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        placeholder="Nhập vào số điện thoại" class="input-text">
                </div>
            </div>
        </div>
        <div class="form-row mb20">
            <input type="email" name="email" value="{{ old('email') }}" placeholder="Nhập vào email"
                class="input-text">
        </div>
        <div class="uk-grid uk-grid-medium mb20">
            <div class="uk-width-large-1-3">
                <select name="province_id" id="" class="province location setupSelect2"
                    data-target="districts">
                    <option value="0">[Chọn Thành Phố]</option>
                    @foreach ($provinces as $key => $val)
                        <option value="{{ $val->code }}">{{ $val->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="uk-width-large-1-3">
                <select name="district_id" id="" class="setupSelect2 districts location" data-target="wards">
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
            <input type="text" name="address" value="{{ old('address') }}" placeholder="Nhập vào địa chỉ"
                class="input-text">
        </div>
        <div class="form-row">
            <input type="text" name="description" value="{{ old('description') }}"
                placeholder="Ghi chú thêm (VD: Giao hàng vào lúc 15h)" class="input-text">
        </div>
    </div>
</div>
