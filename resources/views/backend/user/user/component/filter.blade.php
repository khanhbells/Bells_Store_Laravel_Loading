<form action="{{ route('user.index') }}" method="">
    <div class="filter-wrapper">
        <div class="uk-flex uk-flex-middle uk-flex-space-between">
            @include('backend.dashboard.component.perpage')
            <div class="action">
                <div class="uk-flex uk-flex-middle">
                    @include('backend.dashboard.component.filterPublish')
                    <select name="user_catalogue_id" class="form-control mr10 setupSelect2">
                        <option value="0" selected="selected">Chọn Nhóm Thành Viên</option>
                        <option value="1">Quản trị viên</option>
                    </select>
                    @include('backend.dashboard.component.keyword')
                    <a href="{{ route('user.create') }}" class="btn btn-danger"><i class="fa fa-plus mr5"></i>Thêm mới
                        thành
                        viên</a>
                </div>
            </div>
        </div>
    </div>
</form>
