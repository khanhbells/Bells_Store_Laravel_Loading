<form action="{{ route('user.catalogue.index') }}" method="">
    <div class="filter-wrapper">
        <div class="uk-flex uk-flex-middle uk-flex-space-between">
            @include('backend.dashboard.component.perpage')
            <div class="action">
                <div class="uk-flex uk-flex-middle">
                    @include('backend.dashboard.component.filterPublish')
                    @include('backend.dashboard.component.keyword')
                    <div class="uk-flex uk-flex-middle">
                        <a href="{{ route('user.catalogue.permissison') }}" class="btn btn-warning mr10"><i
                                class="fa fa-key mr5"></i>Phân quyền</a>
                        <a href="{{ route('user.catalogue.create') }}" class="btn btn-danger"><i
                                class="fa fa-plus mr5"></i>Thêm mới
                            nhóm thành viên</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</form>
