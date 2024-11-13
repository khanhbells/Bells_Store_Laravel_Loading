<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>
                <input type="checkbox" value="" id="checkAll" class="input-checkbox">
            </th>
            <th>Họ tên</th>
            <th class="text-center">Số điện thoại</th>
            <th class="text-center">Email</th>
            <th class="text-center">Nội dung</th>
            <th class="text-center" style="width:35px;">Đánh giá</th>
            <th class="text-center" style="width:160px;">Đối tương</th>
            <th class="text-center" style="width:50px;">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($reviews) && is_object($reviews))
            @foreach ($reviews as $review)
                @php
                    $reviewableLink = write_url($review->reviewable->languages->first()->pivot->canonical, true, true);
                @endphp
                <tr>
                    <td><input type="checkbox" value="{{ $review->id }}" class="input-checkbox checkBoxItem"></td>
                    <td>
                        {{ $review->fullname }}
                    </td>
                    <td>
                        {{ $review->phone }}
                    </td>
                    <td>
                        {{ $review->email }}
                    </td>
                    <td>
                        {{ $review->description }}
                    </td>
                    <td class="text-center">
                        <div class="text-navy">
                            {{ $review->score }}
                        </div>
                    </td>
                    <td>
                        <a href="{{ $reviewableLink }}" target="_blank">Click để xem đối tượng</a>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('review.delete', $review->id) }}" class="btn btn-danger"><i
                                class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
{{ $reviews->links('pagination::bootstrap-4') }}
<script>
    var changeStatusUrl = "{{ url('ajax/dashboard/changeStatus') }}";
    var changeStatusAllUrl = "{{ url('ajax/dashboard/changeStatusAll') }}";
</script>
