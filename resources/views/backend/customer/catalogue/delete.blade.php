@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['delete']['title']])
<form action="{{ route('customer.catalogue.destroy', $customerCatalogue->id) }}" method="post" class="box">
    @include('backend.dashboard.component.destroy', ['model' => $customerCatalogue])
</form>
