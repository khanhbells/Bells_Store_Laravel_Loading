@include('backend.dashboard.component.breadcrumb', [
    'title' => isset($product) ? $config['seo']['update']['title'] : $config['seo']['create']['title'],
])
@include('backend.dashboard.component.formError')
@php
    $url = $config['method'] == 'create' ? route('product.store') : route('product.update', $product->id);
@endphp
<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-9">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Thông tin chung</h5>
                    </div>
                    <div class="ibox-content">
                        @include('backend.dashboard.component.content', [
                            'model' => $product ?? null,
                        ])
                    </div>
                </div>
                @include('backend.dashboard.component.album')
                @include('backend.product.product.component.variant')
                @include('backend.dashboard.component.seo', [
                    'model' => $product ?? null,
                ])
            </div>
            <div class="col-lg-3">
                @include('backend.product.product.component.aside')
                <div class="text-right">
                    <button class="btn btn-primary" type="submit" name="send" value="send">Lưu
                        lại</button>
                </div>
            </div>
        </div>
    </div>
</form>
