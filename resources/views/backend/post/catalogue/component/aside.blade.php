<div class="ibox w">
    <div class="ibox-title">
        <h5>{{ __('message.parent') }}</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <span class="text-danger notice">{{ __('message.parent_notice') }}</span>
                    <select name="parent_id" class="form-control setupSelect2" id="">
                        @foreach ($dropdown as $key => $val)
                            <option
                                {{ $key == old('parent_id', isset($postCatalogue->parent_id) ? $postCatalogue->parent_id : '') ? 'selected' : '' }}
                                value="{{ $key }}">{{ $val }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ibox">
    <div class="ibox-title">
        <h5>{{ __('message.image') }}</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <span class="image img-cover image-target">
                        <img src="{{ old('image', isset($postCatalogue) && $postCatalogue->image != null ? asset($postCatalogue->image) : asset('backend/img/not_found.jpg')) }}"
                            alt="">
                    </span>
                    <input type="hidden" name="image"
                        value="{{ old('image', isset($postCatalogue) && $postCatalogue->image != null ? asset($postCatalogue->image) : '') }}">
                </div>

            </div>
        </div>
    </div>
</div>
<div class="ibox mb15">
    <div class="ibox-title">
        <h5>{{ __('message.advange') }}</h5>
    </div>
    <div class="ibox-content ">
        <div class="row ">
            <div class="col-lg-12">
                <div class="form-row">
                    <div class="mb15">
                        <select name="publish" class="form-control setupSelect2" id="">
                            @foreach (config('app.general.publish') as $key => $val)
                                <option
                                    {{ $key == old('publish', isset($postCatalogue->publish) ? $postCatalogue->publish : '') ? 'selected' : '' }}
                                    value="{{ $key }}">
                                    {{ $val }}</option>
                            @endforeach
                        </select>
                    </div>
                    <select name="follow" class="form-control setupSelect2" id="">
                        @foreach (config('app.general.follow') as $key => $val)
                            <option
                                {{ $key == old('follow', isset($postCatalogue->follow) ? $postCatalogue->follow : '') ? 'selected' : '' }}
                                value="{{ $key }}">{{ $val }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
