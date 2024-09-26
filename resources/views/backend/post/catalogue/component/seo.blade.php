<div class="ibox">
    <div class="ibox-title">
        <h5>{{ __('message.seo') }}</h5>
    </div>
    <div class="ibox-content">
        <div class="seo-container">
            <div class="meta-title">
                {{ old('meta_title', $postCatalogue->meta_title ?? '') ? old('meta_title', $postCatalogue->meta_title ?? '') : __('message.seo_title') }}
            </div>
            <div class="canonical">
                {{ old('canonical', isset($postCatalogue) && $postCatalogue->canonical ? config('app.url') . $postCatalogue->canonical . config('app.general.suffix') : false) ?: __('message.seo_canonical') }}
            </div>
            <div class="meta-description">
                {{ old('meta-description', $postCatalogue->meta_description ?? '') ? old('meta-description', $postCatalogue->meta_description ?? '') : __('message.seo_description') }}
            </div>
        </div>
        <div class="seo-wrapper">
            <div class="row mb15">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label for="" class="control-label text-left">
                            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                <span>{{ __('message.seo_meta_title') }}</span>
                                <span class="count-meta-title">0 {{ __('message.character') }}</span>
                            </div>
                        </label>
                        <input type="text" name="meta_title"
                            value="{{ old('meta_title', $postCatalogue->meta_title ?? '') }}" class="form-control"
                            placeholder="" autocomplete="off" />
                    </div>
                </div>
            </div>
            <div class="row mb15">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label for="" class="control-label text-left">
                            <span>{{ __('message.seo_meta_keyword') }}</span>
                        </label>
                        <input type="text" name="meta_keyword"
                            value="{{ old('meta_keyword', $postCatalogue->meta_keyword ?? '') }}" class="form-control"
                            placeholder="" autocomplete="off" />
                    </div>
                </div>
            </div>
            <div class="row mb15">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label for="" class="control-label text-left">
                            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                <span>{{ __('message.seo_meta_description') }}</span>
                                <span class="count-meta-description">0 {{ __('message.character') }}</span>
                            </div>
                        </label>
                        <textarea type="text" name="meta_description" class="form-control" placeholder="" autocomplete="off">{{ old('meta_description', $postCatalogue->meta_description ?? '') }}</textarea>
                    </div>
                </div>
            </div>
            <div class="row mb15">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label for="" class="control-label text-left">
                            <span>{{ __('message.canonical') }} <span class="text-danger">(*)</span></span>
                        </label>
                        <div class="input-wrapper">
                            <input type="text" name="canonical"
                                value="{{ old('canonical', $postCatalogue->canonical ?? '') }}" class="form-control"
                                placeholder="" autocomplete="off" />
                            <span class="baseUrl">{{ config('app.url') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
