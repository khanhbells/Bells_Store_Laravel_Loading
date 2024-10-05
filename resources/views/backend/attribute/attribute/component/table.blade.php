<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th style="width:50px;">
                <input type="checkbox" value="" id="checkAll" class="input-checkbox">
            </th>
            <th>Tiêu đề</th>
            @include('backend.dashboard.component.languageTh')
            <th style="width:80px;" class="text-center">Vị trí</th>
            <th class="text-center" style="width:100px;">Tình trạng</th>
            <th class="text-center" style="width:100px;">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($attributes) && is_object($attributes))
            @foreach ($attributes as $attribute)
                <tr id="{{ $attribute->id }}">
                    <td><input type="checkbox" value="{{ $attribute->id }}" class="input-checkbox checkBoxItem">
                    </td>
                    <td>
                        <div class="uk-flex uk-flex-middle">

                            <div class="main-info">
                                <div class="name"><span class="maintitle">{{ $attribute->name }}</span></div>
                                <div class="catalogue">
                                    <span class="text-danger">Nhóm hiển thị: </span>
                                    @php
                                        $displayedNames = [];
                                    @endphp
                                    @foreach ($attribute->attribute_catalogues as $val)
                                        @php
                                            $currentLanguageId = $languageId;
                                            $currentLanguage = $val->attribute_catalogue_language->firstWhere(
                                                'language_id',
                                                $currentLanguageId,
                                            );
                                        @endphp
                                        @if ($currentLanguage && !in_array($currentLanguage->name, $displayedNames))
                                            <a
                                                href="{{ route('attribute.index', ['attribute_catalogue_id' => $val->id]) }}">{{ $currentLanguage->name }}</a>
                                            @php
                                                // Thêm tên nhóm vào mảng để tránh lặp lại
                                                $displayedNames[] = $currentLanguage->name;
                                            @endphp
                                        @endif
                                    @endforeach

                                </div>
                            </div>
                        </div>
                    </td>
                    @include('backend.dashboard.component.languageTd', [
                        'model' => $attribute,
                        'modeling' => 'Attribute',
                    ])
                    <td>
                        <input value="{{ $attribute->order }}" type="text" name="order"
                            class="form-control sort-order text-right" data-id="{{ $attribute->id }}"
                            data-model="{{ $config['model'] }}">
                    </td>
                    <td class="text-center js-switch-{{ $attribute->id }}">
                        <input type="checkbox" value="{{ $attribute->publish }}"
                            {{ $attribute->publish == 2 ? 'checked' : '' }} class="js-switch  status"
                            data-field="publish" data-model="{{ $config['model'] }}"
                            data-modelId="{{ $attribute->id }}" />
                    </td>
                    <td class="text-center">
                        <a href="{{ route('attribute.edit', $attribute->id) }}" class="btn btn-success"><i
                                class="fa fa-edit"></i></a>
                        <a href="{{ route('attribute.delete', $attribute->id) }}" class="btn btn-danger"><i
                                class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
{{ $attributes->links('pagination::bootstrap-4') }}
<script>
    var changeStatusUrl = "{{ url('ajax/dashboard/changeStatus') }}";
    var changeStatusAllUrl = "{{ url('ajax/dashboard/changeStatusAll') }}";
</script>
