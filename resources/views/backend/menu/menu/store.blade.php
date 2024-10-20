@include('backend.dashboard.component.breadcrumb', [
    'title' => $config['seo']['create']['title'],
    //'title' => $config['seo'][config['method']]['title'] ,
])
@include('backend.dashboard.component.formError')
{{-- @php
    $url = $config['method'] == 'create' ? route('menu.store') : route('menu.update', $menu->id);
@endphp --}}
<form action="{{ route('menu.store') }}" method="post" class="box menuContainer">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        @include('backend.menu.menu.component.catalogue')
        <hr>
        @include('backend.menu.menu.component.list')
        <input type="hidden" name="redirect" value="{{ $id ?? 0 }}">
        <div class="text-right">
            <button class="btn btn-primary" type="submit" name="send" value="send">Lưu
                lại</button>
        </div>
    </div>
</form>
@include('backend.menu.menu.component.popup')
