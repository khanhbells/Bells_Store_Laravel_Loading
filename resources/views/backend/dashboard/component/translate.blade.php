<div class="row mb15">
    <div class="col-lg-12">
        <div class="form-row">
            <label for="" class="control-label text-left">{{ __('message.title') }}
                <span class="text-danger">(*)</span>
            </label>
            <input type="text" name="translate_name" value="{{ old('translate_name', $model->name ?? '') }}"
                class="form-control" placeholder="" autocomplete="off" />
        </div>
    </div>
</div>
<div class="row mb30">
    <div class="col-lg-12">
        <div class="form-row">
            <label for="" class="control-label text-left">{{ __('message.description') }}
                <span class="text-danger">(*)</span>
            </label>
            <textarea type="text" name="translate_description" class="form-control ck-editor" placeholder="" autocomplete="off"
                id="description_1" data-height="150">{{ old('description', $model->description ?? '') }}</textarea>
        </div>
    </div>
</div>
<div class="row mb15">
    <div class="col-lg-12">
        <div class="form-row">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <label for="" class="control-label text-left">{{ __('message.content') }}
                    <span class="text-danger">(*)</span>
                </label>
                <a href="" class="multipleUploadImageCkeditor"
                    data-target="content_1">{{ __('message.uploadImages') }}</a>
            </div>
            <textarea type="text" name="translate_content" class="form-control ck-editor" placeholder="" autocomplete="off"
                id="content_1" data-height="500">{{ old('content', $model->content ?? '') }}</textarea>
        </div>
    </div>
</div>
