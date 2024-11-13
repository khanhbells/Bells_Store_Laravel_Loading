@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['delete']['title']])
<form action="{{ route('review.destroy', $review->id) }}" method="post" class="box">
    @include('backend.dashboard.component.destroy', ['model' => $review])
</form>
