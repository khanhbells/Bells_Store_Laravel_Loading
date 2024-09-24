<div class="row mb15">
    <div class="col-lg-12">
        <div class="form-row">
            <label for="" class="control-label text-left">Tiêu đề bài viết
                <span class="text-danger">(*)</span>
            </label>
            <input type="text" name="name" value="{{ old('name', $post->name ?? '') }}" class="form-control"
                placeholder="" autocomplete="off" />
        </div>
    </div>
</div>
<div class="row mb30">
    <div class="col-lg-12">
        <div class="form-row">
            <label for="" class="control-label text-left">Mô tả ngắn
                <span class="text-danger">(*)</span>
            </label>
            <textarea type="text" name="description" class="form-control ck-editor" placeholder="" autocomplete="off"
                id="description" data-height="150">{{ old('description', $post->description ?? '') }}</textarea>
        </div>
    </div>
</div>
<div class="row mb15">
    <div class="col-lg-12">
        <div class="form-row">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <label for="" class="control-label text-left">Nội dung
                    <span class="text-danger">(*)</span>
                </label>
                <a href="" class="multipleUploadImageCkeditor" data-target="content">Upload nhiều hình ảnh</a>
            </div>
            <textarea type="text" name="content" class="form-control ck-editor" placeholder="" autocomplete="off" id="content"
                data-height="500">{{ old('content', $post->content ?? '') }}</textarea>
        </div>
    </div>
</div>
