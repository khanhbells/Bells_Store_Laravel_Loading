@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['delete']['title']])
<form action="{{ route('user.destroy', $user->id) }}" method="post" class="box">
    @include('backend.dashboard.component.destroy', ['model' => $user])
</form>
