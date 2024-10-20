@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['delete']['title']])
<form action="{{ route('customer.destroy', $customer->id) }}" method="post" class="box">
    @include('backend.dashboard.component.destroy', ['model' => $customer])
</form>
