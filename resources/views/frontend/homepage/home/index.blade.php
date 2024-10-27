@extends('frontend.homepage.layout')
@section('content')
    <div id="homepage" class="homepage">
        @include('frontend.component.slide')
        <div class="panel-category page-setup">
            <div class="uk-container uk-container-center">
                @if (!is_null($widgets['category-highlight']->object))
                    <div class="panel-head">
                        <div class="uk-flex uk-flex-middle">
                            <h2 class="heading-1"><span>Danh mục sản phẩm</span></h2>
                            @include('frontend.component.catalogue', [
                                'category' => $widgets['category-highlight'],
                            ])
                        </div>
                    </div>
                @endif
                @if (isset($widgets['category']->object))
                    <div class="panel-body">
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-container">
                            <div class="swiper-wrapper">
                                @foreach ($widgets['category']->object as $key => $val)
                                    @php
                                        $name = $val->languages->first()->pivot->name;
                                        $canonical = write_url($val->languages->first()->pivot->canonical, true, true);
                                        $image = $val->image;
                                        $productCount = $val->products_count ?? 0;
                                    @endphp
                                    <div class="swiper-slide">
                                        <div class="category-item bg-<?php echo rand(1, 7); ?>">
                                            <a href="{{ asset($image) }}" class="image img-scaledown img-zoomin"><img
                                                    src="{{ asset($image) }}" alt="{{ $name }}"></a>
                                            <div class="title"><a href="{{ write_url($canonical) }}"
                                                    title="{{ $name }}">{{ $name }}</a>
                                            </div>
                                            <div class="total-product">{{ $productCount }} sản phẩm</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        @php
            $banner = App\Enums\SlideEnum::BANNER_BODY;
        @endphp
        @if (count($slides[$banner]['item']))
            <div class="panel-banner">
                <div class="uk-container uk-container-center">
                    <div class="panel-body">
                        <div class="uk-grid uk-grid-medium">
                            @foreach ($slides[$banner]['item'] as $key => $val)
                                @php
                                    $name = $val['name'];
                                    $description = $val['description'];
                                    $canonical = write_url($val['canonical'], false, false);
                                    $image = $val['image'];
                                @endphp
                                <div class="uk-width-large-1-3">
                                    <div class="banner-item">
                                        <span class="image"><img src="{{ asset($image) }}"
                                                alt="{{ $name }}"></span>
                                        <div class="banner-overlay">
                                            <div class="banner-title">{!! $description !!}</div>
                                            <a class="btn-shop" href="{{ $canonical }}" title="{{ $name }}">Mua
                                                ngay</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (!is_null($widgets['category-home']))
            @foreach ($widgets['category-home']->object as $category)
                @php
                    $catName = $category->languages->first()->pivot->name;
                    $catCanonical = write_url($category->languages->first()->pivot->canonical, true, true);
                    $childrens = $category->childrens ?? null;

                @endphp
                <div class="panel-popular mb30">
                    <div class="uk-container uk-container-center">
                        <div class="panel-head">
                            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                <h2 class="heading-1"><a href="{{ $catCanonical }}"
                                        title="{{ $catName }}">{{ $catName }}</a></h2>
                                @if (!is_null($childrens))
                                    <div class="category-children">
                                        <ul class="uk-list uk-clearfix uk-flex uk-flex-middle">
                                            <li class=""><a href="{{ $catCanonical }}"
                                                    title="{{ $catName }}">Tất cả</a></li>
                                            @foreach ($childrens as $children)
                                                @php
                                                    $childName = $children->languages->first()->pivot->name;
                                                    $childCanonical = write_url(
                                                        $children->languages->first()->pivot->canonical,
                                                        true,
                                                        true,
                                                    );
                                                @endphp
                                                <li class=""><a href="{{ $childCanonical }}"
                                                        title="{{ $childName }}">{{ $childName }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @if (isset($category->products) && count($category->products))
                            <div class="panel-body">
                                <div class="uk-grid uk-grid-medium">
                                    @foreach ($category->products as $product)
                                        <div class="uk-width-large-1-5 mb20">
                                            @include('frontend.component.product-item', [
                                                'product' => $product,
                                            ])
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif
        @php
            $image = $widgets['bestseller']->album ? $widgets['bestseller']->album[0] : '';
            $description = $widgets['bestseller']->description[$config['language']];
            $title = $widgets['bestseller']->name;
        @endphp
        <div class="panel-bestseller">
            <div class="uk-container uk-container-center">
                <div class="panel-head">
                    <div class="uk-flex uk-flex-middle uk-flex-space-between">
                        <h2 class="heading-1"><span>{{ $title }}</span></h2>
                        @include('frontend.component.catalogue', [
                            'category' => $widgets['category-highlight'],
                        ])
                    </div>
                </div>
                <div class="panel-body">
                    <div class="uk-grid uk-grid-medium">
                        <div class="uk-width-large-1-4">
                            <div class="best-seller-banner">
                                <a class="image img-cover"><img src="{{ asset($image) }}" alt="{{ asset($image) }}"></a>
                                <div class="banner-title">{!! $description !!}</div>
                            </div>
                        </div>
                        <div class="uk-width-large-3-4">
                            @if (!is_null($widgets['bestseller']->object))
                                <div class="product-wrapper">
                                    <div class="swiper-button-next"></div>
                                    <div class="swiper-button-prev"></div>
                                    <div class="swiper-container">
                                        <div class="swiper-wrapper">
                                            @foreach ($widgets['bestseller']->object as $key => $val)
                                                <div class="swiper-slide">
                                                    @include('frontend.component.product-item', [
                                                        'product' => $val,
                                                    ])
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-deal page-setup">
            <div class="uk-container uk-container-center">
                <div class="panel-head">
                    <div class="uk-flex uk-flex-middle uk-flex-space-between">
                        <h2 class="heading-1"><span>Giảm giá trong ngày</span></h2>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="uk-grid uk-grid-medium">
                        <?php for($i = 0; $i<=3; $i++){  ?>
                        <div class="uk-width-large-1-4">
                            @include('frontend.component.product-item-2')
                        </div>
                        <?php }  ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="uk-container uk-container-center">
            <div class="panel-group">
                <div class="panel-body">
                    <div class="group-title">Stay home & get your daily <br> needs from our shop</div>
                    <div class="group-description">Start Your Daily Shopping with Nest Mart</div>
                    <span class="image img-scaledowm"><img src="{{ asset('frontend/resources/img/banner-9-min.png') }}"
                            alt=""></span>
                </div>
            </div>
        </div>
        <div class="panel-commit">
            <div class="uk-container uk-container-center">
                <div class="uk-grid uk-grid-medium">
                    <div class="uk-width-large-1-5">
                        <div class="commit-item">
                            <div class="uk-flex uk-flex-middle">
                                <span class="image"><img src="{{ asset('frontend/resources/img/commit-1.png') }}"
                                        alt=""></span>
                                <div class="info">
                                    <div class="title">Giá ưu đãi</div>
                                    <div class="description">Khi mua từ 500.000đ</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-width-large-1-5">
                        <div class="commit-item">
                            <div class="uk-flex uk-flex-middle">
                                <span class="image"><img src="{{ asset('frontend/resources/img/commit-2.png') }}"
                                        alt=""></span>
                                <div class="info">
                                    <div class="title">Miễn phí vận chuyển</div>
                                    <div class="description">Trong bán kính 2km</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-width-large-1-5">
                        <div class="commit-item">
                            <div class="uk-flex uk-flex-middle">
                                <span class="image"><img src="{{ asset('frontend/resources/img/commit-3.png') }}"
                                        alt=""></span>
                                <div class="info">
                                    <div class="title">Ưu đãi</div>
                                    <div class="description">Khi đăng ký tài khoản</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-width-large-1-5">
                        <div class="commit-item">
                            <div class="uk-flex uk-flex-middle">
                                <span class="image"><img src="{{ asset('frontend/resources/img/commit-4.png') }}"
                                        alt=""></span>
                                <div class="info">
                                    <div class="title">Đa dạng </div>
                                    <div class="description">Sản phẩm đa dạng</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-width-large-1-5">
                        <div class="commit-item">
                            <div class="uk-flex uk-flex-middle">
                                <span class="image"><img src="{{ asset('frontend/resources/img/commit-5.png') }}"
                                        alt=""></span>
                                <div class="info">
                                    <div class="title">Đổi trả </div>
                                    <div class="description">Đổi trả trong ngày</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
