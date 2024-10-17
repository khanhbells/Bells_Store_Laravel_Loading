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
                            'model' => $widget ?? null,
                        ])

                    </div>
                </div>
                @include('backend.dashboard.component.album', ['model' => $widget ?? null])
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Cấu hình nội dung của widget</h5>
                    </div>
                    <div class="ibox-content model-list">
                        <div class="labelText">Chọn Module</div>
                        @foreach (__('module.model') as $key => $val)
                            <div class="model-item uk-flex uk-flex-middle">
                                <input type="radio" id="{{ $key }}" class="input-radio"
                                    value="{{ $key }}" name="model"
                                    {{ old('model', $widget->model ?? null) == $key ? 'checked' : '' }}>
                                <label for="{{ $key }}">{{ $val }}</label>
                            </div>
                        @endforeach
                        <div class="search-model-box">
                            <i class="fa fa-search"></i>
                            <input type="text" class="form-control search-model">
                            <div class="ajax-search-result"></div>
                        </div>
                        @php
                            $modelItem = old('modelItem', $widgetItem ?? null);
                        @endphp
                        <div class="search-model-result">
                            @if (!is_null($modelItem))
                                @foreach ($modelItem['id'] as $key => $val)
                                    <div class="search-result-item" id="model-{{ $val }}"
                                        data-modelid="{{ $val }}">
                                        <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                            <div class="uk-flex uk-flex-middle">
                                                <span class="image img-cover"><img
                                                        src="{{ asset($modelItem['image'][$key]) }}"
                                                        alt=""></span>
                                                <span class="name">{{ $modelItem['name'][$key] }}</span>
                                                <div class="hidden">
                                                    <input type="text" name="modelItem[id][]"
                                                        value="{{ $val }}">
                                                    <input type="text" name="modelItem[name][]"
                                                        value="{{ $modelItem['name'][$key] }}">
                                                    <input type="text" name="modelItem[image][]"
                                                        value="{{ $modelItem['image'][$key] }}">
                                                </div>
                                            </div>
                                            <div class="deleted">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                    width="24" height="24">
                                                    <path fill="none" d="M0 0h24v24H0z" />
                                                    <path
                                                        d="M18.3 5.71a1 1 0 00-1.42 0L12 10.59 7.12 5.7a1 1 0 00-1.42 1.42l4.88 4.88-4.88 4.88a1 1 0 001.42 1.42L12 13.41l4.88 4.88a1 1 0 001.42-1.42l-4.88-4.88 4.88-4.88a1 1 0 000-1.42z" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
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
    var baseUrl = "{{ asset('') }}"; // Đây sẽ là đường dẫn đến thư mục gốc của ứng dụng
</script>
