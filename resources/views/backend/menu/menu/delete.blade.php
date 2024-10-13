@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['delete']['title']])
<form action="{{ route('menu.destroy', $menuCatalogue->id) }}" method="post" class="box">
    @include('backend.dashboard.component.destroy', ['model' => $menuCatalogue])
</form>
