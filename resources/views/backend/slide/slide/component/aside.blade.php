<div class="ibox slide-setting slide-normal">
    <div class="ibox-title">
        <h5>Cài đặt cơ bản</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12 mb10">
                <div class="form-row">
                    <label for="" class="control-label text-left">Tên slide
                        <span class="text-danger">(*)</span>
                    </label>
                    <input type="name" name="name" value="{{ old('name', $slide->name ?? '') }}"
                        class="form-control" placeholder="" autocomplete="off" />
                </div>
            </div>
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="" class="control-label text-left">Từ khóa slide
                        <span class="text-danger">(*)</span>
                    </label>
                    <input type="name" name="keyword" value="{{ old('keyword', $slide->keyword ?? '') }}"
                        class="form-control" placeholder="" autocomplete="off" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="slide-setting">
                    <div class="setting-item">
                        <div class="uk-flex uk-flex-middle">
                            <span class="setting-text">Chiều rộng</span>
                            <div class="setting-value">
                                <input value="{{ old('setting.width', 0) }}" type="text" name="setting[width]"
                                    class="form-control int">
                                <span class="px">px</span>
                            </div>
                        </div>
                    </div>
                    <div class="setting-item">
                        <div class="uk-flex uk-flex-middle">
                            <span class="setting-text">Chiều cao</span>
                            <div class="setting-value">
                                <input value="{{ old('setting.height', 0) }}" type="text" name="setting[height]"
                                    class="form-control int">
                                <span class="px">px</span>
                            </div>
                        </div>
                    </div>
                    <div class="setting-item">
                        <div class="uk-flex uk-flex-middle">
                            <span class="setting-text">Hiệu ứng</span>
                            <div class="setting-value">
                                <select name="setting[animation]" id="" class="form-control setupSelect2">
                                    @foreach (__('module.effect') as $key => $val)
                                        <option {{ $key == old('setting.animation') ? 'selected' : '' }}
                                            value="{{ $key }}">{{ $val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="setting-item">
                        <div class="uk-flex uk-flex-middle">
                            <span class="setting-text">Mũi tên</span>
                            <div class="setting-value">
                                <input type="checkbox" name="setting[arrow]" value="accept"
                                    @if (!old() || old('setting.arrow') == 'accept') checked="checked" @endif>
                            </div>
                        </div>
                    </div>
                    <div class="setting-item">
                        <div class="uk-flex uk-flex-middle">
                            <span class="setting-text">Điều hướng</span>
                            <div class="setting-value">
                                @foreach (__('module.navigate') as $key => $val)
                                    <div class="nav-setting-item uk-flex uk-flex-middle">
                                        <input type="radio" value="{{ $key }}" name="setting[navigate]"
                                            id="navigate_{{ $key }}"{{ old('setting.navigate', !old() ? 'dots' : $key) === $key ? 'checked' : '' }}>
                                        <label for="navigate_{{ $key }}">{{ $val }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ibox slide-setting slide-advance">
    <div class="ibox-title uk-flex uk-flex-middle uk-flex-space-between">
        <h5>Cài đặt nâng cao</h5>
        <div class="ibox-tools">
            <a class="collapse-link">
                <i class="fa fa-chevron-up"></i>
            </a>
        </div>
    </div>
    <div class="ibox-content">
        <div class="setting-item">
            <div class="uk-flex uk-flex-middle">
                <span class="setting-text">Tự động chạy</span>
                <div class="setting-value">
                    <input type="checkbox" name="setting[autoplay]" value="accept"
                        {{ 'accept' == old('setting.autoplay') ? 'checked' : '' }}>
                </div>
            </div>
        </div>
        <div class="setting-item">
            <div class="uk-flex uk-flex-middle">
                <span class="setting-text">Dừng khi <br> di chuột</span>
                <div class="setting-value">
                    <input type="checkbox" name="setting[pauseHover]" value="accept"
                        {{ 'accept' == old('setting.pauseHover') ? 'checked' : '' }}>
                </div>
            </div>
        </div>
        <div class="setting-item">
            <div class="uk-flex uk-flex-middle">
                <span class="setting-text">Chuyển ảnh</span>
                <div class="setting-value">
                    <input type="text" name="setting[animationDelay]" class="form-control int"
                        value="{{ old('setting.animationDelay', 0) }}">
                    <span class="px">ms</span>
                </div>
            </div>
        </div>
        <div class="setting-item">
            <div class="uk-flex uk-flex-middle">
                <span class="setting-text">Tốc độ <br> hiệu ứng</span>
                <div class="setting-value">
                    <input type="text" name="setting[animationSpeed]" class="form-control int"
                        value="{{ old('setting.animationSpeed', 0) }}">
                    <span class="px">ms</span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ibox short-code">
    <div class="ibox-title">
        <h5>Short Code</h5>
    </div>
    <div class="ibox-content">
        <textarea name="short_code" id="" class="textarea form-control">{{ old('short_code') }}</textarea>
    </div>
</div>
