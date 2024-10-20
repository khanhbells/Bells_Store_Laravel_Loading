<div class="ibox source-setting source-normal">
    <div class="ibox-title">
        <h5>Cài đặt cơ bản</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12 mb10">
                <div class="form-row">
                    <label for="" class="control-label text-left">Tên nguồn khách
                        <span class="text-danger">(*)</span>
                    </label>
                    <input type="name" name="name" value="{{ old('name', $source->name ?? '') }}"
                        class="form-control" placeholder="" autocomplete="off" />
                </div>
            </div>
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="" class="control-label text-left">Từ khóa nguồn
                        <span class="text-danger">(*)</span>
                    </label>
                    <input type="name" name="keyword" value="{{ old('keyword', $source->keyword ?? '') }}"
                        class="form-control" placeholder="" autocomplete="off" />
                </div>
            </div>
        </div>
    </div>
</div>
