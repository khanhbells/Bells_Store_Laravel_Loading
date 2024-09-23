<form action="{{ route('language.index') }}" method="">
    <div class="filter-wrapper">
        <div class="uk-flex uk-flex-middle uk-flex-space-between">
            <div class="perpage">
                <div class="uk-flex uk-flex-middle uk-flex-space-between">
                    <select name="perpage" class="form-control input-sm perpage filter mr10">
                        @php
                            $perpage = request('perpage') ?: old('perpage');
                        @endphp
                        @for ($i = 20; $i <= 200; $i += 20)
                            <option {{ $perpage == $i ? 'selected' : '' }} value="{{ $i }}">
                                {{ $i }} bản ghi</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="action">
                <div class="uk-flex uk-flex-middle">
                    <select name="publish" class="form-control mr10 setupSelect2">
                        @php
                            $publish = request('publish') ?: old('publish', -1); // Mặc định là -1 nếu không có giá trị nào được chọn
                        @endphp
                        {{-- <option value="-1" {{ $publish == -1 ? 'selected' : '' }}>Tất cả tình trạng</option> --}}
                        @foreach (config('app.general.publish') as $key => $val)
                            <option value="{{ $key }}" {{ $publish == $key ? 'selected' : '' }}>
                                {{ $val }}
                            </option>
                        @endforeach
                    </select>
                    <div class="uk-search uk-flex uk-flex-middle mr10 ">
                        <div class="input-group">
                            <input type="text" name="keyword" value="{{ request('keyword') ?: old('keyword') }}"
                                placeholder="Nhập Từ khóa bạn muốn tìm kiếm..." class="form-control">
                            <span class="input-group-btn">
                                <button type="submit" name="search" value="search"
                                    class="btn btn-primary mb0 btn-sm">Tìm
                                    kiếm</button>
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('language.create') }}" class="btn btn-danger"><i class="fa fa-plus mr5"></i>Thêm
                        mới ngôn ngữ</a>
                </div>
            </div>
        </div>
    </div>

</form>
