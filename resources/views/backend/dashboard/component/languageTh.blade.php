@foreach ($languages as $language)
    @if (session('app_locale') == $language->canonical)
        @continue;
    @endif
    <th class="text-center" style="width: 100px"><span class="image img-scaledown language-flag"><img
                src="{{ asset($language->image) }}" alt=""></span></th>
@endforeach
