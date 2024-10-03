<select name="publish" class="form-control mr10 setupSelect2">
    @php
        $publish = request('publish') ?: old('publish', -1); // Mặc định là -1 nếu không có giá trị nào được chọn
    @endphp

    @foreach (__('message.publish') as $key => $val)
        <option value="{{ $key }}" {{ $publish == $key ? 'selected' : '' }}>
            {{ $val }}
        </option>
    @endforeach
</select>
