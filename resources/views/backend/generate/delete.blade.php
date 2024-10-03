@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['delete']['title']])
<form action="{{ route('generate.destroy', $generate->id) }}" method="post" class="box">
    @include('backend.dashboard.component.destroy', ['model' => $generate ?? null])
</form>
