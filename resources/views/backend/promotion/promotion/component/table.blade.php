<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>
                <input type="checkbox" value="" id="checkAll" class="input-checkbox">
            </th>
            <th>Tên widget</th>
            <th class="text-center">Từ khóa</th>
            <th class="text-center">Short code</th>
            @include('backend.dashboard.component.languageTh')
            <th class="text-center">Tình trạng</th>
            <th class="text-center">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($widgets) && is_object($widgets))
            @foreach ($widgets as $widget)
                <tr>
                    <td><input type="checkbox" value="{{ $widget->id }}" class="input-checkbox checkBoxItem"></td>
                    <td>
                        {{ $widget->name }}
                    </td>
                    <td>
                        {{ $widget->keyword }}
                    </td>
                    <td>
                        {{ !empty($widget->short_code) ? $widget->short_code : '-' }}
                    </td>
                    @foreach ($languages as $language)
                        @if (session('app_locale') == $language->canonical)
                            @continue;
                        @endif
                        @php
                            $translated = isset($widget->description[$language->id]) ? 1 : 0;
                        @endphp
                        <td class="text-center" style="width: 100px">
                            <a class="{{ $translated == 1 ? '' : 'text-danger' }}"
                                href="{{ route('widget.translate', ['languageId' => $language->id, 'id' => $widget->id]) }}">
                                {{ $translated == 1 ? 'Đã dịch' : 'Chưa dịch' }}
                            </a>
                        </td>
                    @endforeach

                    <td class="text-center js-switch-{{ $widget->id }}">
                        <input type="checkbox" value="{{ $widget->publish }}"
                            {{ $widget->publish == 2 ? 'checked' : '' }} class="js-switch  status" data-field="publish"
                            data-model="{{ $config['model'] }}" data-modelId="{{ $widget->id }}" />
                    </td>
                    <td class="text-center">
                        <a href="{{ route('widget.edit', $widget->id) }}" class="btn btn-success"><i
                                class="fa fa-edit"></i></a>
                        <a href="{{ route('widget.delete', $widget->id) }}" class="btn btn-danger"><i
                                class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
{{ $widgets->links('pagination::bootstrap-4') }}
<script>
    var changeStatusUrl = "{{ url('ajax/dashboard/changeStatus') }}";
    var changeStatusAllUrl = "{{ url('ajax/dashboard/changeStatusAll') }}";
</script>