@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['delete']['title']])
<form action="{{ route('widget.destroy', $widget->id) }}" method="post" class="box">
    @include('backend.dashboard.component.destroy', ['model' => $widget])
</form>
