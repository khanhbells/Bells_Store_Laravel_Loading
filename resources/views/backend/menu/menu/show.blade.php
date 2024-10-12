@include('backend.dashboard.component.breadcrumb', [
    'title' => $config['seo']['show']['title'],
])
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-4">
            <div class="panel-title">Danh sách menu</div>
            <div class="panel-description">
                <p>+ Danh sách Menu giúp bạn dễ dàng kiểm soát bố cục menu. Bạn có thể thêm mới hoặc cập nhật menu bằng
                    nút <span class="text-success">Cập nhật Menu</span></p>
                <p>+ Bạn có thể thay đổi vị trí hiển thị của menu bằng cách kéo thả menu đến vị trí mong muốn bằng cách
                    <span class="text-success"> menu đến vị trí mong muốn</span>
                </p>
                <p>+ Dễ dàng khởi tạo menu con bằng cách ấn vào nút <span class="text-success"> Quản lý menu con</span>
                </p>
                <p class="text-danger">+ Hỗ trợ danh mục con cấp 5</p>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="ibox">
                <div class="ibox-title">
                    <div class="uk-flex uk-flex-middle uk-flex-space-between">
                        <h5 style="margin: 0">Menu chính</h5>
                        <a href="" class="custom-button">Cập nhật menu</a>
                    </div>
                    <div class="ibox-content" id="dataCatalogue" data-catalogueId="{{ $id }}">
                        @php
                            $menus = recursive($menus);
                            $menuString = recursive_menu($menus);
                        @endphp
                        @if (count($menus))
                            <div class="dd" id="nestable2">
                                <ol class="dd-list">
                                    {!! $menuString !!}
                                </ol>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
