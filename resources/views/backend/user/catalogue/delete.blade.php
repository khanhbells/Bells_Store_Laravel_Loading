@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['delete']['title']])
<form action="{{ route('user.catalogue.destroy', $userCatalogue->id) }}" method="post" class="box">
    @include('backend.dashboard.component.destroy', ['model' => $userCatalogue])
</form>
