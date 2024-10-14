@php
    $title =
        str_replace('{language}', $language->name, $config['seo']['translate']['title']) . ' ' . $menuCatalogue->name;
@endphp
@include('backend.dashboard.component.breadcrumb', [
    'title' => $title,
])
<form action="{{ route('menu.translate.save', ['languageId' => $languageId]) }}" method="post">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-4">
                <div class="panel-title">Thông tin chung</div>
                <div class="panel-description">
                    <p>+ Hệ thống tự động lấy ra bản dịch của các Menu <span class="text-success">nếu có</span>
                    </p>
                    <p>+ Cập nhật các thông tin về bản dịch cho các menu của ban phía bên phải
                        <span class="text-success"> menu đến vị trí mong muốn</span>
                    </p>
                    <p>+ Lưu ý cập nhật đầy đủ thông tin <span class="text-success"> Quản lý menu con</span>
                    </p>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="ibox">
                    <div class="ibox-title">
                        <div class="uk-flex uk-flex-middle uk-flex-space-between">
                            <h5 style="margin: 0">Danh sách bản dịch</h5>
                        </div>
                        <div class="ibox-content">
                            @if (count($menus))
                                @foreach ($menus as $key => $val)
                                    @php
                                        $name = $val->languages->first()->pivot->name;
                                        $canonical = $val->languages->first()->pivot->canonical;
                                    @endphp
                                    <div class="menu-translate-item">
                                        <div class="row">
                                            <div class="col-lg-12 mb10 text-danger text-bold">
                                                <div>Menu: {{ $val->position }}</div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-row">
                                                    <div class="uk-flex uk-flex-middle">
                                                        <div class="menu-name">Tên Menu</div>
                                                        <input type="text" name="name" value="{{ $name }}"
                                                            class="form-control" placeholder="" autocomplete="off"
                                                            disabled>
                                                    </div>
                                                </div>
                                                <div class="form-row">
                                                    <div class="uk-flex uk-flex-middle">
                                                        <div class="menu-name">Đường dẫn</div>
                                                        <input type="text" name="name" value="{{ $canonical }}"
                                                            class="form-control" placeholder="" autocomplete="off"
                                                            disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-row">
                                                    <input type="text" name="translate[name][]"
                                                        value="{{ $val->translate_name ?? '' }}" class="form-control"
                                                        placeholder="Nhập vào bản dịch của bạn..." autocomplete="off">
                                                </div>
                                                <div class="form-row">
                                                    <input type="text" name="translate[canonical][]"
                                                        value="{{ $val->translate_canonical ?? '' }}"
                                                        class="form-control" placeholder="Nhập vào bản dịch của bạn..."
                                                        autocomplete="off">
                                                    <input type="hidden" name="translate[id][]"
                                                        value="{{ $val->id ?? '' }}" class="form-control"
                                                        placeholder="Nhập vào bản dịch của bạn..." autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-right mb15">
            <button class="btn btn-primary" type="submit" name="send" value="send">Lưu
                lại</button>
        </div>
    </div>
</form>