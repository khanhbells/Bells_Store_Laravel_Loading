@include('backend.dashboard.component.breadcrumb', [
    'title' => isset($slide) ? $config['seo']['update']['title'] : $config['seo']['create']['title'],
    //'title' => $config['seo'][config['method']]['title'] ,
])
@include('backend.dashboard.component.formError')
@php
    $url = $config['method'] == 'create' ? route('slide.store') : route('slide.update', $slide->id);
@endphp
<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-9">
                @include('backend.slide.slide.component.list')
            </div>
            <div class="col-lg-3">
                @include('backend.slide.slide.component.aside')
            </div>
        </div>
        <hr>
        <div class="text-right mt20">
            <button class="btn btn-primary" type="submit" name="send" value="send">Lưu
                lại</button>
        </div>
    </div>
</form>
