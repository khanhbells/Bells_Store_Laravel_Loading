<form action="{{ route('customer.index') }}" method="">
    <div class="filter-wrapper">
        <div class="uk-flex uk-flex-middle uk-flex-space-between">
            @include('backend.dashboard.component.perpage')
            <div class="action">
                <div class="uk-flex uk-flex-middle">
                    @include('backend.dashboard.component.filterPublish')
                    @php
                        $catalogues = request('customer_catalogue_id') ?: old('customer_catalogue_id', 0); // Mặc định là -1 nếu không có giá trị nào được chọn
                    @endphp
                    <select name="customer_catalogue_id" class="form-control mr10 setupSelect2">
                        <option value="0">Chọn nhóm thành viên</option>
                        @foreach ($customerCatalogues as $val)
                            <option value="{{ $val->id }}" {{ $catalogues == $val->id ? 'selected' : '' }}>
                                {{ $val->name }}</option>
                        @endforeach
                    </select>
                    @include('backend.dashboard.component.keyword')
                    <a href="{{ route('customer.create') }}" class="btn btn-danger"><i class="fa fa-plus mr5"></i>Thêm
                        mới
                        thành
                        viên</a>
                </div>
            </div>
        </div>
    </div>
</form>
