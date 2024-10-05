@include('backend.dashboard.component.breadcrumb', [
    'title' => isset($attributeCatalogue) ? $config['seo']['update']['title'] : $config['seo']['create']['title'],
])
@include('backend.dashboard.component.formError')
@php
    $url = $config['method'] == 'create' ? route('attribute.catalogue.store') : route('attribute.catalogue.update', $attributeCatalogue->id);
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
                            'model' => $attributeCatalogue ?? null,
                        ])
                    </div>
                </div>
                @include('backend.dashboard.component.album')
                @include('backend.dashboard.component.seo', [
                    'model' => $attributeCatalogue ?? null,
                ])
            </div>
            <div class="col-lg-3">
                @include('backend.attribute.catalogue.component.aside')
                @include('backend.dashboard.component.button')
            </div>
        </div>
    </div>
</form>
