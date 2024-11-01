@php
    $name = $post->languages->first()->pivot->name;
    $canonical = write_url($post->languages->first()->pivot->canonical, true, true);
    $image = asset($post->image);
    $catNames = $post->post_catalogues->first()->languages->first()->pivot->name;
@endphp
<div class="product-item-2 product">
    <a href="{{ $canonical }}" class="image img-cover"><img src="{{ $image }}" alt="{{ $name }}"></a>
    <div class="info">
        <div class="info-wrapper">
            <div class="category-title"><a href="{{ $canonical }}" title="{{ $name }}">{{ $catNames }}</a>
            </div>
            <h3 class="title"><a href="{{ $canonical }}" title="{{ $name }}">{{ $name }}</a></h3>
        </div>
    </div>
</div>
