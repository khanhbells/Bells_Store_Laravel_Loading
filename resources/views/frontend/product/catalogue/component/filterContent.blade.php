<div class="filter-content">
    <div class="filter-overlay">
        <div class="filter-close">
            <i class="fi fi-rs-cross"></i>
        </div>
        <div class="filter-content-container">
            @if (!is_null($filters))
                @foreach ($filters as $key => $val)
                    @php
                        $catName = $val->languages->first()->pivot->name;
                        if (is_null($val->attributes) || count($val->attributes) == 0) {
                            continue;
                        }
                    @endphp
                    <div class="filter-item">
                        <div class="filter-heading">{{ $catName }}</div>
                        @if (count($val->attributes))
                            <div class="filter-body">
                                @foreach ($val->attributes as $item)
                                    @php
                                        $attributeName = $item->languages->first()->pivot->name;
                                        $id = $item->id;
                                    @endphp
                                    <div class="filter-choose">
                                        <input type="checkbox" id="attribute-{{ $id }}"
                                            class="input-checkbox filtering filterAttribute" value="{{ $id }}"
                                            data-group="{{ $val->id }}">
                                        <label for="attribute-{{ $id }}">{{ $attributeName }}</label>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
            <div class="filter-item filter-price slider-box">
                <div class="filter-heading" for="priceRange">Lọc Theo Giá:</div>
                <div class="filter-price-content">
                    <input type="text" id="priceRange" readonly="" class="uk-hidden">
                    <div id="price-range"
                        class="slider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"></div>
                </div>
                <div class="filter-input-value mt5">
                    <div class="uk-flex uk-flex-middle uk-flex-space-between">
                        <input type="text" class="min-value input-value" value="0đ">
                        <input type="text" class="max-value input-value" value="100.000.000đ">
                    </div>
                </div>
            </div>
            {{-- <div class="filter-item filter-category">
                <div class="filter-heading">Tình trạng sản phẩm</div>
                <div class="filter-body">
                    <div class="filter-choose">
                        <input id="input-availble" type="checkbox" name="stock[]" value="1" class="uk-checkbox">
                        <label for="input-availble">Còn hàng</label>
                    </div>
                    <div class="filter-choose">
                        <input id="input-outstock" type="checkbox" name="stock[]" value="0" class="uk-checkbox">
                        <label for="input-outstock">Hết Hàng</label>
                    </div>
                </div>
            </div> --}}
            <div class="filter-review">
                <div class="filter-heading">Lọc theo đánh giá</div>
                <div class="filter-choose uk-flex uk-flex-middle">
                    <input id="input-rate-5" type="checkbox" name="rate[]" value="5"
                        class="input-checkbox filtering">
                    <label for="input-rate-5" class="uk-flex uk-flex-middle">
                        <div class="filter-star">
                            <i class="fi-rs-star"></i>
                            <i class="fi-rs-star"></i>
                            <i class="fi-rs-star"></i>
                            <i class="fi-rs-star"></i>
                            <i class="fi-rs-star"></i>
                        </div>
                    </label>
                    <span class="totalProduct ml5 mb5">(5)</span>
                </div>
                <div class="filter-choose uk-flex uk-flex-middle">
                    <input id="input-rate-5" type="checkbox" name="rate[]" value="4"
                        class="input-checkbox filtering">
                    <label for="input-rate-5" class="uk-flex uk-flex-middle">
                        <div class="filter-star">
                            <i class="fi-rs-star"></i>
                            <i class="fi-rs-star"></i>
                            <i class="fi-rs-star"></i>
                            <i class="fi-rs-star"></i>
                        </div>
                    </label>
                    <span class="totalProduct ml5 mb5">(4)</span>
                </div>
                <div class="filter-choose uk-flex uk-flex-middle">
                    <input id="input-rate-5" type="checkbox" name="rate[]" value="3"
                        class="input-checkbox filtering">
                    <label for="input-rate-5" class="uk-flex uk-flex-middle">
                        <div class="filter-star">
                            <i class="fi-rs-star"></i>
                            <i class="fi-rs-star"></i>
                            <i class="fi-rs-star"></i>
                        </div>
                    </label>
                    <span class="totalProduct ml5 mb5">(3)</span>
                </div>
                <div class="filter-choose uk-flex uk-flex-middle">
                    <input id="input-rate-5" type="checkbox" name="rate[]" value="2"
                        class="input-checkbox filtering">
                    <label for="input-rate-5" class="uk-flex uk-flex-middle">
                        <div class="filter-star">
                            <i class="fi-rs-star"></i>
                            <i class="fi-rs-star"></i>
                        </div>
                    </label>
                    <span class="totalProduct ml5 mb5">(2)</span>
                </div>
                <div class="filter-choose uk-flex uk-flex-middle">
                    <input id="input-rate-5" type="checkbox" name="rate[]" value="1"
                        class="input-checkbox filtering">
                    <label for="input-rate-5" class="uk-flex uk-flex-middle">
                        <div class="filter-star">
                            <i class="fi-rs-star"></i>
                        </div>
                    </label>
                    <span class="totalProduct ml5 mb5">(1)</span>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" class="product_catalogue_id" value="{{ $productCatalogue->id }}">
