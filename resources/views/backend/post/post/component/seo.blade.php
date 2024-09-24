<div class="ibox">
    <div class="ibox-title">
        <h5>Cấu hình SEO</h5>
    </div>
    <div class="ibox-content">
        <div class="seo-container">
            <div class="meta-title">
                {{ old('meta_title', $post->meta_title ?? '') ? old('meta_title', $post->meta_title ?? '') : '[Tiêu đề của SEO]' }}
            </div>
            <div class="canonical">
                {{ old('canonical', isset($post) && $post->canonical ? config('app.url') . $post->canonical . config('app.general.suffix') : false) ?: '[Đường dẫn SEO]' }}
            </div>
            <div class="meta-description">
                {{ old('meta-description', $post->meta_description ?? '') ? old('meta-description', $post->meta_description ?? '') : '[Mô tả của SEO]' }}
            </div>
        </div>
        <div class="seo-wrapper">
            <div class="row mb15">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label for="" class="control-label text-left">
                            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                <span>Mô tả SEO</span>
                                <span class="count-meta-title">0 ký tự</span>
                            </div>
                        </label>
                        <input type="text" name="meta_title" value="{{ old('meta_title', $post->meta_title ?? '') }}"
                            class="form-control" placeholder="" autocomplete="off" />
                    </div>
                </div>
            </div>
            <div class="row mb15">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label for="" class="control-label text-left">
                            <span>Từ khóa SEO</span>
                        </label>
                        <input type="text" name="meta_keyword"
                            value="{{ old('meta_keyword', $post->meta_keyword ?? '') }}" class="form-control"
                            placeholder="" autocomplete="off" />
                    </div>
                </div>
            </div>
            <div class="row mb15">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label for="" class="control-label text-left">
                            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                <span>Mô tả SEO</span>
                                <span class="count-meta-description">0 ký tự</span>
                            </div>
                        </label>
                        <textarea type="text" name="meta_description" class="form-control" placeholder="" autocomplete="off">{{ old('meta_description', $post->meta_description ?? '') }}</textarea>
                    </div>
                </div>
            </div>
            <div class="row mb15">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label for="" class="control-label text-left">
                            <span>Đường dẫn <span class="text-danger">(*)</span></span>
                        </label>
                        <div class="input-wrapper">
                            <input type="text" name="canonical"
                                value="{{ old('canonical', $post->canonical ?? '') }}" class="form-control"
                                placeholder="" autocomplete="off" />
                            <span class="baseUrl">{{ config('app.url') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
