<form action="{{ route('order.index') }}" method="">
    <div class="filter-wrapper">
        <div class="uk-flex uk-flex-middle uk-flex-space-between">
            <div class="uk-flex uk-flex-middle">
                @include('backend.dashboard.component.perpage')
                <div class="date-item-box">
                    <input type="text" name="created_at" value="{{ request('created_at') ?: old('created_at') }}"
                        class="rangepicker form-control" readonly placeholder="Nhấn để chọn ngày">
                </div>
            </div>
            <div class="action">
                <div class="uk-flex uk-flex-middle">
                    <div class="mr10">
                        @foreach (__('cart') as $key => $val)
                            @php
                                ${$key} = request($key) ?: old($key); // Mặc định là -1 nếu không có giá trị nào được chọn
                            @endphp
                            <select name="{{ $key }}" class="form-control mr10 setupSelect2">
                                @foreach ($val as $index => $item)
                                    <option value="{{ $index }}" {{ ${$key} == $index ? 'selected' : '' }}>
                                        {{ $item }}
                                    </option>
                                @endforeach
                            </select>
                        @endforeach
                    </div>
                    @include('backend.dashboard.component.keyword')
                </div>
            </div>
        </div>
    </div>
</form>
