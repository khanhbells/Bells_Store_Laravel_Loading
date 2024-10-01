@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['delete']['title']])
<form action="{{ route('generate.destroy', $generate->id) }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-head">
                    <div class="panel-title">Thông tin chung</div>
                    <div class="panel-description">
                        <p class="text-danger">- Bạn có chắc chắn muốn xóa bản ghi:
                            <strong>{{ $generate->name }}</strong>
                        </p>
                        <p class="text-danger"><strong>- Lưu ý: Hãy cẩn thận khi xóa bản ghi! </strong></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Tên module
                                        <span class="text-danger">(*)</span>
                                    </label>
                                    <input type="name" name="name"
                                        value="{{ old('name', $generate->name ?? '') }}" class="form-control"
                                        placeholder="" autocomplete="off" readonly />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-right mb15">
            <button class="btn btn-danger" type="submit" name="send" value="send">Xóa dữ liệu</button>
        </div>
    </div>
</form>
