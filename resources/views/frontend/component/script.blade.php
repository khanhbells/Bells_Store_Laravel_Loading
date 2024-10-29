<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<div id="fb-root"></div>
<script async defer crossorigin="anonymous"
    src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v17.0&appId=103609027035330&autoLogAppEvents=1"
    nonce="E1aWx0Pa"></script>
<script src="{{ asset('frontend/resources/plugins/wow/dist/wow.min.js') }}"></script>
<script src="{{ asset('frontend/resources/uikit/js/uikit.min.js') }}"></script>
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script src="{{ asset('frontend/resources/uikit/js/components/sticky.min.js') }}"></script>
<script src="{{ asset('frontend/resources/function.js') }}"></script>
<script src="{{ asset('frontend\core\plugins\jquery-nice-select-1.1.0\js\jquery.nice-select.js') }}"></script>
@php
    $coreScript = ['backend/js/plugins/toastr/toastr.min.js'];
    if (isset($config['js'])) {
        foreach ($config['js'] as $key => $val) {
            array_push($coreScript, $val);
        }
    }
@endphp
@foreach ($coreScript as $item)
    <script src="{{ asset($item) }}"></script>
@endforeach
