<div class="row">
    <div class="col-lg-5">
        <div class="ibox">
            <div class="ibox-content">
                <div class="panel-group" id="accordion">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h5 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Liên kết tự
                                    tạo</a>
                            </h5>
                        </div>
                        <div id="collapseOne" class="panel-collapse collapse in">
                            <div class="panel-body">
                                <div class="panel-title">Tạo menu</div>
                                <div class="panel-description">
                                    <p>* Cài đặt Menu mà bạn muốn hiển thị.</p>
                                    <p><small class="text-danger">* Khi khởi tạo menu bạn phải chắc chắn rằng đường
                                            dẫn
                                            của menu có hoạt động. Đường dẫn trên website được khởi tạo tại các
                                            module:
                                            Bài viết, Sản phẩm, Dự án, ...</small></p>
                                    <p><small class="text-danger">* Tiêu đề và đường dẫn của menu không được bỏ
                                            trống.</small></p>
                                    <p><small class="text-danger">* Hệ Thống chỉ hỗ trợ tối đa 5 cấp menu.</small>
                                    </p>
                                </div>
                                <a style="color:#008; border-color:#4cd4d5; display:inline-block !important;"
                                    title="" class="btn btn-default add-menu m-b m-r right">Thêm
                                    đường dẫn</a>
                            </div>
                        </div>
                    </div>

                    @foreach (__('module.model') as $key => $val)
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-model="{{ $key }}" data-toggle="collapse" data-parent="#accordion"
                                        class="collapsed menu-module" aria-expanded="false"
                                        href="#{{ $key }}">{{ $val }}</a>
                                </h4>
                            </div>
                            <div id="{{ $key }}" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <form action="" method="get" data-model="{{ $key }}"
                                        class="searchModel">
                                        <input type="text" value="" class="form-control" name="keyword"
                                            placeholder="Nhập 2 ký tự để tìm kiếm...">
                                    </form>
                                    <div class="menu-list"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="ibox">
            <div class="ibox-content">
                <div class="row">
                    <div class="col-lg-4">
                        <label for="">Tên Menu</label>
                    </div>
                    <div class="col-lg-4">
                        <label for="">Đường dẫn</label>
                    </div>
                    <div class="col-lg-2">
                        <label for="">Vị trí</label>
                    </div>
                    <div class="col-lg-2">
                        <label for="">Xóa</label>
                    </div>
                </div>
                <div class="hr-line-dashed" style="margin:10px 0;"></div>
                <div class="menu-wrapper">
                    <div class="notification text-center">
                        <h4 class="notification-title">
                            Danh sách Liên kết này chưa có bất kỳ đường dẫn nào.
                        </h4>
                        <p class="notification-message">
                            Hãy nhấn vào
                            <span class="add-link">Thêm đường dẫn</span>
                            để bắt đầu thêm.
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
