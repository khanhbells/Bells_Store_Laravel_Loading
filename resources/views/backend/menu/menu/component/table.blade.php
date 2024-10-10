<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>
                <input type="checkbox" value="" id="checkAll" class="input-checkbox">
            </th>
            <th class="text-center" style="width: 100px">Tên menu</th>
            <th class="text-center">Từ khóa</th>
            <th class="text-center">Ngày tạo</th>
            <th class="text-center">Người tạo</th>
            <th class="text-center">Tình trạng</th>
            <th class="text-center">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($menus) && is_object($menus))
            @foreach ($menus as $menu)
                <tr>
                    <td><input type="checkbox" value="{{ $menu->id }}" class="input-checkbox checkBoxItem"></td>
                    <td class="text-center">
                        <span class="image img-cover"><img class="avatar" src="{{ asset($menu->image) }}"
                                alt=""></span>
                    </td>
                    <td>
                    </td>
                    <td>
                    </td>
                    <td>
                    </td>
                    <td>
                    </td>
                    <td class="text-center js-switch-{{ $menu->id }}">
                        <input type="checkbox" value="{{ $menu->publish }}" {{ $menu->publish == 2 ? 'checked' : '' }}
                            class="js-switch  status" data-field="publish" data-model="{{ $config['model'] }}"
                            data-modelId="{{ $menu->id }}" />
                    </td>
                    <td class="text-center">
                        <a href="{{ route('menu.edit', $menu->id) }}" class="btn btn-success"><i
                                class="fa fa-edit"></i></a>
                        <a href="{{ route('menu.delete', $menu->id) }}" class="btn btn-danger"><i
                                class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
<script>
    var changeStatusUrl = "{{ url('ajax/dashboard/changeStatus') }}";
    var changeStatusAllUrl = "{{ url('ajax/dashboard/changeStatusAll') }}";
</script>
