@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['delete']['title']])
<form action="{{ route('source.destroy', $source->id) }}" method="post" class="box">
    @include('backend.dashboard.component.destroy', ['model' => $source])
</form>
