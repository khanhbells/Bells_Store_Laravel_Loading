@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['delete']['title']])
<form action="{{ route('slide.destroy', $slide->id) }}" method="post" class="box">
    @include('backend.dashboard.component.destroy', ['model' => $slide])
</form>
