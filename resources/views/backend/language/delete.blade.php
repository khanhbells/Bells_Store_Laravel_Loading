@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['delete']['title']])
<form action="{{ route('language.destroy', $language->id) }}" method="post" class="box">
    @include('backend.dashboard.component.destroy', ['model' => $language ?? null])
</form>
