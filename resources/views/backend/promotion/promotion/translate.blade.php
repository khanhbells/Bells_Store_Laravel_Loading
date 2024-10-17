@php
    $title =
        str_replace('{language}', $translate['name'], $config['seo']['create']['translate']) . ' ' . $widget['name'];
@endphp
@include('backend.dashboard.component.breadcrumb', [
    'title' => $title,
])
@include('backend.dashboard.component.formError');
<form action="{{ route('widget.saveTranslate') }}" method="post">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <input type="hidden" name="translateId" value="{{ $translate->id }}">
        <input type="hidden" name="widgetId" value="{{ $widget->id }}">
        <div class="row">
            <div class="col-lg-6">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ __('message.tableHeading') }}</h5>
                    </div>
                    <div class="ibox-content">
                        @include('backend.dashboard.component.content', [
                            'model' => $widget ?? null,
                            'disabled' => 1,
                            'offTitle' => true,
                            'offContent' => true,
                        ])
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ __('message.tableHeading') }}</h5>
                    </div>
                    <div class="ibox-content">
                        @include('backend.dashboard.component.translate', [
                            'model' => $widgetTranslate ?? null,
                            'offTitle' => true,
                            'offContent' => true,
                        ])
                    </div>
                </div>
                <div class="text-right">
                    <button class="btn btn-primary" type="submit" name="send"
                        value="send">{{ __('message.save') }}</button>
                </div>
            </div>
        </div>
    </div>
</form>