<div class="ibox widget-setting widget-normal">
    <div class="ibox-title">
        <h5>Cài đặt cơ bản</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12 mb10">
                <div class="form-row">
                    <label for="" class="control-label text-left">Tên widget
                        <span class="text-danger">(*)</span>
                    </label>
                    <input type="name" name="name" value="{{ old('name', $widget->name ?? '') }}"
                        class="form-control" placeholder="" autocomplete="off" />
                </div>
            </div>
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="" class="control-label text-left">Từ khóa widget
                        <span class="text-danger">(*)</span>
                    </label>
                    <input type="name" name="keyword" value="{{ old('keyword', $widget->keyword ?? '') }}"
                        class="form-control" placeholder="" autocomplete="off" />
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ibox short-code">
    <div class="ibox-title">
        <h5>Short Code</h5>
    </div>
    <div class="ibox-content">
        <textarea name="short_code" id="" class="textarea form-control">{{ old('short_code', $widget->short_code ?? null) }}</textarea>
    </div>
</div>
