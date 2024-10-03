<form action="{{ route('generate.index') }}" method="">
    <div class="filter-wrapper">
        <div class="uk-flex uk-flex-middle uk-flex-space-between">
            @include('backend.dashboard.component.perpage')
            <div class="action">
                <div class="uk-flex uk-flex-middle">
                    <div class="uk-search uk-flex uk-flex-middle mr10 ">
                        @include('backend.dashboard.component.keyword')
                    </div>
                    <a href="{{ route('generate.create') }}" class="btn btn-danger"><i class="fa fa-plus mr5"></i>Tạo
                        module mới</a>
                </div>
            </div>
        </div>
    </div>

</form>
