@include('backend.dashboard.component.breadcrumb', [
    'title' => isset($widget) ? $config['seo']['update']['title'] : $config['seo']['create']['title'],
])
@include('backend.dashboard.component.formError')
@php
    $url = $config['method'] == 'create' ? route('widget.store') : route('widget.update', $widget->id);
@endphp
<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-9">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Thông tin widget</h5>
                    </div>
                    <div class="ibox-content widgetContent">
                        @include('backend.dashboard.component.content', [
                            'offTitle' => true,
                            'offContent' => true,
                        ])

                    </div>
                </div>
                @include('backend.dashboard.component.album')
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Cấu hình nội dung của widget</h5>
                    </div>
                    <div class="ibox-content model-list">
                        <div class="labelText">Chọn Module</div>
                        @foreach (__('module.model') as $key => $val)
                            <div class="model-item uk-flex uk-flex-middle">
                                <input type="radio" id="{{ $key }}" class="input-radio"
                                    value="{{ $key }}" name="model">
                                <label for="{{ $key }}">{{ $val }}</label>
                            </div>
                        @endforeach
                        <div class="search-model-box">
                            <i class="fa fa-search"></i>
                            <input type="text" class="form-control search-model">
                            <div class="ajax-search-result">

                            </div>
                        </div>
                        <div class="search-model-result hidden">
                            @for ($i = 0; $i < 10; $i++)
                                <div class="search-result-item">
                                    <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                        <div class="uk-flex uk-flex-middle">
                                            <span class="image img-cover"><img
                                                    src="{{ asset('backend/img/rails_logo.png') }}"
                                                    alt=""></span>
                                            <span class="name">BELLS STORE</span>
                                        </div>
                                        <div class="deleted">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24"
                                                height="24">
                                                <path fill="none" d="M0 0h24v24H0z" />
                                                <path
                                                    d="M18.3 5.71a1 1 0 00-1.42 0L12 10.59 7.12 5.7a1 1 0 00-1.42 1.42l4.88 4.88-4.88 4.88a1 1 0 001.42 1.42L12 13.41l4.88 4.88a1 1 0 001.42-1.42l-4.88-4.88 4.88-4.88a1 1 0 000-1.42z" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                @include('backend.widget.component.aside')
            </div>
        </div>
        <div class="text-right">
            <button class="btn btn-primary" type="submit" name="send" value="send">Lưu
                lại</button>
        </div>
    </div>
</form>
<script>
    var province_id = '{{ isset($widget->province_id) ? $widget->province_id : old('province_id') }}'
    var district_id = '{{ isset($widget->district_id) ? $widget->district_id : old('district_id') }}'
    var ward_id = '{{ isset($widget->ward_id) ? $widget->ward_id : old('ward_id') }}'
    var getLocation = "{{ url('ajax/location/getLocation') }}";
</script>
