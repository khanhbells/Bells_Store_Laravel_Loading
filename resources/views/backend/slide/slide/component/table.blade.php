<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>
                <input type="checkbox" value="" id="checkAll" class="input-checkbox">
            <th class="text-center">Tên nhóm</th>
            <th class="text-center">Từ khóa</th>
            <th class="text-center">Danh sách hình ảnh</th>
            <th class="text-center">Tình trạng</th>
            <th class="text-center">Thao tác</th>
        </tr>
    </thead>

    <tbody>
        @if (isset($slides) && is_object($slides))
            @foreach ($slides as $slide)
                @php
                    $image = [];
                    $description = [];
                    $canonical = [];
                    $name = [];
                    $alt = [];
                    $window = [];
                @endphp
                @foreach ($slide['item'][$languageId] as $key => $val)
                    @php
                        $image[] = $val['image'];
                        $description[] = $val['description'];
                        $canonical[] = $val['canonical'];
                        $name[] = $val['name'];
                        $alt[] = $val['alt'];
                        $window[] = $val['window'];
                    @endphp
                @endforeach
                <tr>
                    <td><input type="checkbox" value="{{ $slide->id }}" class="input-checkbox checkBoxItem"></td>
                    <td>
                        {{ $slide->name }}
                    </td>
                    <td>
                        {{ $slide->keyword }}
                    </td>
                    <td>
                        <div class="uk-flex uk-flex-middle sortable clearfix data-album sortui ui-sortable">
                            @foreach ($image as $key => $val)
                                <div class="ui-state-default" data-id="{{ $val }}"
                                    data-slide-id="{{ $slide->id }}">
                                    <span class="img-cover images-index">
                                        <img src="{{ asset($val) }}" alt="">
                                        <input type="hidden" name="slide[image][]" value="{{ $val }}">
                                        <input type="hidden" name="slide[description][]"
                                            value="{{ $description[$key] }}">
                                        <input type="hidden" name="slide[canonical][]" value="{{ $canonical[$key] }}">
                                        <input type="hidden" name="slide[name][]" value="{{ $name[$key] }}">
                                        <input type="hidden" name="slide[alt][]" value="{{ $alt[$key] }}">
                                        <input type="hidden" name="slide[window][]" value="{{ $window[$key] }}">
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </td>
                    <td class="text-center js-switch-{{ $slide->id }}">
                        <input type="checkbox" value="{{ $slide->publish }}"
                            {{ $slide->publish == 2 ? 'checked' : '' }} class="js-switch  status" data-field="publish"
                            data-model="{{ $config['model'] }}" data-modelId="{{ $slide->id }}" />
                    </td>
                    <td class="text-center">
                        <a href="{{ route('slide.edit', $slide->id) }}" class="btn btn-success"><i
                                class="fa fa-edit"></i></a>
                        <a href="{{ route('slide.delete', $slide->id) }}" class="btn btn-danger"><i
                                class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
{{ $slides->links('pagination::bootstrap-4') }}
<script>
    var changeStatusUrl = "{{ url('ajax/dashboard/changeStatus') }}";
    var changeStatusAllUrl = "{{ url('ajax/dashboard/changeStatusAll') }}";
</script>
