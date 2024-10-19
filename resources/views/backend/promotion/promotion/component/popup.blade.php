<div id="findProduct" class="modal fade">
    <form action="" class="form create-menu-catalogue" method="">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                    </button>
                    <h4 class="modal-title">Chọn sản phẩm</h4>
                    <small class="font-bold text-navy">Chọn sản phẩm sẵn có hoặc tìm kiếm theo sản phẩm mà bạn mong
                        muốn</small>
                </div>
                <div class="modal-body">
                    <div class="search-model-box">
                        <i class="fa fa-search"></i>
                        <input type="text" class="form-control search-model"
                            placeholder="Tìm kiếm theo tên, mã sản phẩm SKU...">
                    </div>
                    <div class="search-list mt20">
                        Loading...
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="submit" name="create" value="create" class="btn btn-primary">Xác nhận</button>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    var baseUrl = "{{ asset('') }}"; // Đây sẽ là đường dẫn đến thư mục gốc của ứng dụng
</script>
