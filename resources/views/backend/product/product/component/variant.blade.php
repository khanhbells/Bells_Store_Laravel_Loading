<div class="ibox variant-box">
    <div class="ibox-title">
        <div>
            <h5>Sản phẩm có nhiều phiên bản</h5>
        </div>
        <div class="description">Cho phép bạn bán các phiên bản khác nhau của sản phẩm, ví dụ: nước hoa thì có các
            <strong class="text-danger">màu sắc</strong> và <strong class="text-danger">size</strong> số khác nhau. Mỗi
            phiên bản sẽ là 1 dòng trong mục danh sách phiên bản phía dưới
        </div>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="variant-checkbox uk-flex uk-flex-middle">
                    <input type="checkbox" value="1" class="variantInputCheckbox" name="accept"
                        id="variantCheckbox" {{ old('accept') == 1 ? 'checked' : '' }}>
                    <label for="variantCheckbox" class="turnOnVariant">Sản phẩm này có nhiều biến thể. Ví dụ như khác
                        nhau về
                        màu sắc, kích thước</label>
                </div>
            </div>
        </div>
        <div class="variant-wrapper {{ old('accept') == 1 ? '' : 'hidden' }}">
            <div class="row variant-container">
                <div class="col-lg-3">
                    <div class="attribute-title">Chọn thuộc tính</div>
                </div>
                <div class="col-lg-9">
                    <div class="attribute-title">Chọn giá trị của thuộc tính (nhập 2 từ để tìm kiếm)</div>
                </div>
            </div>
            <div class="variant-body">
                @if (old('attributeCatalogue'))
                    @foreach (old('attributeCatalogue') as $keyAttr => $valAttr)
                        <div class="row mb20 variant-item">
                            <div class="col-lg-3">
                                <div class="attribute-catalogue">
                                    <select name="attributeCatalogue[]" id=""
                                        class="setupselect2 form-control">
                                        <option value="">Chọn nhóm thuộc tính</option>
                                        @foreach ($attributeCatalogue as $key => $val)
                                            <option {{ $valAttr == $val->id ? 'selected' : '' }}
                                                value="{{ $val->id }}">
                                                {{ $val->attribute_catalogue_language->first()->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                {{-- <input type="text" name="" disabled class="fake-variant form-control"> --}}
                                <select name="attribute[{{ $valAttr }}][]"
                                    class="selectVariant form-control variant-{{ $valAttr }}" multiple
                                    data-catid="{{ $valAttr }}" id=""></select>
                            </div>
                            <div class="col-lg-1">
                                <button type="button" class="remove-attribute btn btn-danger"><i
                                        class="fa fa-trash"></i></button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            <div class="variant-foot mt10">
                <button type="button" class="add-variant">Thêm thuộc tính mới</button>
            </div>
        </div>

    </div>
</div>
<div class="ibox product-variant">
    <div class="ibox-title">
        <h5>Danh sách phiên bản</h5>
    </div>
    <div class="ibox-content">
        <div class="table-responsive">
            <table class="table table-striped variantTable">
                <thead></thead>
                <tbody></tbody>
            </table>
        </div>

    </div>
</div>
<script>
    var attributeCatalogue =
        @json(
            $attributeCatalogue->map(function ($item) {
                    $name = $item->attribute_catalogue_language->first()->name;
                    return [
                        'id' => $item->id,
                        'name' => $name,
                    ];
                })->values());
    var attribute = '{{ base64_encode(json_encode(old('attribute'))) }}'
</script>
