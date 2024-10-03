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
        @if (isset(${module}s) && is_object(${module}s))
            @foreach (${module}s as ${module})
                <tr id="{{ ${module}->id }}">
                    <td><input type="checkbox" value="{{ ${module}->id }}" class="input-checkbox checkBoxItem">
                    </td>
                    <td>
                        <div class="uk-flex uk-flex-middle">
                            <div class="image mr5">
                                <div class="img-cover image-post"><img src="{{ asset(${module}->image) }}" alt="">
                                </div>
                            </div>
                            <div class="main-info">
                                <div class="name"><span class="maintitle">{{ ${module}->name }}</span></div>
                                <div class="catalogue">
                                    <span class="text-danger">Nhóm hiển thị: </span>
                                    @php
                                        $displayedNames = [];
                                    @endphp
                                    @foreach (${module}->{module}_catalogues as $val)
                                        @php
                                            $currentLanguageId = $languageId;
                                            $currentLanguage = $val->{module}_catalogue_language->firstWhere(
                                                'language_id',
                                                $currentLanguageId,
                                            );
                                        @endphp
                                        @if ($currentLanguage && !in_array($currentLanguage->name, $displayedNames))
                                            <a
                                                href="{{ route('{module}.index', ['{module}_catalogue_id' => $val->id]) }}">{{ $currentLanguage->name }}</a>
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
                        'model' => ${module},
                        'modeling' => '{Module}',
                    ])
                    <td>
                        <input value="{{ ${module}->order }}" type="text" name="order"
                            class="form-control sort-order text-right" data-id="{{ ${module}->id }}"
                            data-model="{{ $config['model'] }}">
                    </td>
                    <td class="text-center js-switch-{{ ${module}->id }}">
                        <input type="checkbox" value="{{ ${module}->publish }}" {{ ${module}->publish == 2 ? 'checked' : '' }}
                            class="js-switch  status" data-field="publish" data-model="{{ $config['model'] }}"
                            data-modelId="{{ ${module}->id }}" />
                    </td>
                    <td class="text-center">
                        <a href="{{ route('{module}.edit', ${module}->id) }}" class="btn btn-success"><i
                                class="fa fa-edit"></i></a>
                        <a href="{{ route('{module}.delete', ${module}->id) }}" class="btn btn-danger"><i
                                class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
{{ ${module}s->links('pagination::bootstrap-4') }}
<script>
    var changeStatusUrl = "{{ url('ajax/dashboard/changeStatus') }}";
    var changeStatusAllUrl = "{{ url('ajax/dashboard/changeStatusAll') }}";
</script>
