@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['delete']['title']])
<form action="{{ route('permission.destroy', $permission->id) }}" method="post" class="box">
    @include('backend.dashboard.component.destroy', ['model' => $permission ?? null])
</form>
