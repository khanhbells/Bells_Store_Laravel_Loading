@include('backend.dashboard.component.breadcrumb', [
    'title' => isset($source) ? $config['seo']['update']['title'] : $config['seo']['create']['title'],
])
@include('backend.dashboard.component.formError')
@php
    $url = $config['method'] == 'create' ? route('source.store') : route('source.update', $source->id);
@endphp
<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-9">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Thông tin source</h5>
                    </div>
                    <div class="ibox-content sourceContent">
                        @include('backend.dashboard.component.content', [
                            'offTitle' => true,
                            'offContent' => true,
                            'model' => $source ?? null,
                        ])
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                @include('backend.source.component.aside')
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
