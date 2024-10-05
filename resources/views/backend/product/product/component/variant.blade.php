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
                    <input type="checkbox" value="" class="" name="accept" id="variantCheckbox">
                    <label for="variantCheckbox" class="turnOnVariant">Sản phẩm này có nhiều biến thể. Ví dụ như khác
                        nhau về
                        màu sắc, kích thước</label>
                </div>
            </div>
        </div>
        <div class="variant-wrapper ">
            <div class="row variant-container">
                <div class="col-lg-3">
                    <div class="attribute-title">Chọn thuộc tính</div>
                </div>
                <div class="col-lg-9">
                    <div class="attribute-title">Chọn giá trị của thuộc tính (nhập 2 từ để tìm kiếm)</div>
                </div>
            </div>
            <div class="variant-body">

            </div>
            <div class="variant-foot mt10">
                <button type="button" class="add-variant">Thêm phiên bản mới</button>
            </div>
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
</script>
