<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>
                <input type="checkbox" value="" id="checkAll" class="input-checkbox">
            </th>
            <th class="text-center">Tên chương trình</th>
            <th class="text-center">Chiết khấu</th>
            <th class="text-center">Loại khuyến mại</th>
            <th class="text-center">Ngày bắt đầu</th>
            <th class="text-center">Ngày kết thúc</th>
            <th class="text-center">Tình trạng</th>
            <th class="text-center">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($promotions) && is_object($promotions))
            @foreach ($promotions as $key => $promotion)
                @php
                    $startDate = convertDateTime($promotion->startDate);
                    $endDate = $promotion->endDate != null ? convertDateTime($promotion->endDate) : null;
                    $status = '';
                    if ($promotion->endDate !== null && strtotime($promotion->endDate) - strtotime(now()) <= 0) {
                        $status = '<span class="text-danger text-small">- Hết hạn</span>';
                    } elseif (isset($promotion->neverEndDate)) {
                        $status = '<span class="text-danger text-small">- Vô hạn</span>';
                    }
                @endphp
                <tr>
                    <td><input type="checkbox" value="{{ $promotion->id }}" class="input-checkbox checkBoxItem"></td>
                    <td>
                        <div> {{ $promotion->name }} {!! $status !!}</div>
                        <div class="text-small text-success">Mã KM: {{ $promotion->code }}</div>
                    </td>
                    <td>
                        <div class="discount-infomation text-danger text-center">
                            {!! renderDiscountInformation($promotion) !!}
                        </div>

                    </td>

                    <td>
                        <div>{{ __('module.promotion')[$promotion->method] }}</div>

                    </td>
                    <td>
                        {{ $startDate }}
                    </td>
                    <td>
                        {{ $promotion->neverEndDate === 'accept' ? 'Không giới hạn' : $endDate }}
                    </td>
                    <td class="text-center js-switch-{{ $promotion->id }}">
                        <input type="checkbox" value="{{ $promotion->publish }}"
                            {{ $promotion->publish == 2 ? 'checked' : '' }} class="js-switch  status"
                            data-field="publish" data-model="{{ $config['model'] }}"
                            data-modelId="{{ $promotion->id }}" />
                    </td>
                    <td class="text-center">
                        <a href="{{ route('promotion.edit', $promotion->id) }}" class="btn btn-success"><i
                                class="fa fa-edit"></i></a>
                        <a href="{{ route('promotion.delete', $promotion->id) }}" class="btn btn-danger"><i
                                class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
{{ $promotions->links('pagination::bootstrap-4') }}
<script>
    var changeStatusUrl = "{{ url('ajax/dashboard/changeStatus') }}";
    var changeStatusAllUrl = "{{ url('ajax/dashboard/changeStatusAll') }}";
</script>
