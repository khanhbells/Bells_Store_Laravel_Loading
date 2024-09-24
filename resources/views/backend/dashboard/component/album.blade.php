<div class="ibox">
    <div class="ibox-title">
        <div class="uk-flex uk-flex-middle uk-flex-space-between">
            <h5>Album ảnh</h5>
            <div class="upload-album"><a href="" class="upload-picture">Chọn hình</a></div>
        </div>
        <div class="ibox-content">
            @php
                $gallery = isset($album) && count($album) ? $album : old('album');
            @endphp
            <div class="row">
                <div class="col-lg-12">
                    @if (!@isset($gallery) || count($gallery) == 0)
                        <div class="click-to-upload">
                            <div class="icon">
                                <a href="" class="upload-picture">
                                    <!-- SVG icon here -->
                                </a>
                            </div>
                            <div class="small-text">Sử dụng nút chọn hình hoặc click vào đây để thêm hình ảnh</div>
                        </div>
                    @endif
                    <div class="upload-list {{ isset($gallery) && count($gallery) ? '' : 'hidden' }}">
                        <div class="row">
                            <ul id="sortable" class="clearfix data-album sortui ui-sortable">
                                @if (@isset($gallery) && count($gallery))
                                    @foreach ($gallery as $key => $val)
                                        @php
                                            // Kiểm tra và loại bỏ tiền tố không cần thiết
                                            $imagePath = asset($val);
                                            if (
                                                strpos(
                                                    $imagePath,
                                                    'http://localhost:81/laravelversion1.com/public/laravelversion1.com/public/',
                                                ) === 0
                                            ) {
                                                $imagePath = preg_replace(
                                                    '/^http:\/\/localhost:81\/laravelversion1.com\/public/',
                                                    '',
                                                    $imagePath,
                                                );
                                            }
                                        @endphp
                                        <li class="ui-state-default">
                                            <div class="thumb">
                                                <span class="span image img-scaledown">
                                                    <img src="{{ $imagePath }}" alt="{{ $imagePath }}">
                                                    <input type="hidden" name="album[]" value="{{ $imagePath }}">
                                                </span>
                                                <button class="delete-image"><i class="fa fa-trash"></i></button>
                                            </div>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
