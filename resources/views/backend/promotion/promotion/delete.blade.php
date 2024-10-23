@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['delete']['title']])
<form action="{{ route('promotion.destroy', $promotion->id) }}" method="post" class="box">
    @include('backend.dashboard.component.destroy', ['model' => $promotion])
</form>
