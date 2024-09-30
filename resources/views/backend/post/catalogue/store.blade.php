@include('backend.dashboard.component.breadcrumb', [
    'title' => isset($postCatalogue) ? $config['seo']['update']['title'] : $config['seo']['create']['title'],
])
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@php
    $url =
        $config['method'] == 'create'
            ? route('post.catalogue.store')
            : route('post.catalogue.update', $postCatalogue->id);
@endphp
<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-9">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ __('message.tableHeading') }}</h5>
                    </div>
                    <div class="ibox-content">
                        @include('backend.dashboard.component.content', [
                            'model' => $postCatalogue ?? null,
                        ])
                    </div>
                </div>
                @include('backend.dashboard.component.album')
                @include('backend.dashboard.component.seo', [
                    'model' => $postCatalogue ?? null,
                ])
            </div>
            <div class="col-lg-3">
                @include('backend.post.catalogue.component.aside')
                <div class="text-right">
                    <button class="btn btn-primary" type="submit" name="send"
                        value="send">{{ __('message.save') }}</button>
                </div>
            </div>
        </div>
    </div>
</form>
