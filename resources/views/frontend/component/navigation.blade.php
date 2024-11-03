<div class="navigation">
    <ul class="uk-list uk-clearfix uk-navbar-nav main-menu">
        <li class="children"><a href="" title="Trang chủ">Trang chủ</a>
        </li>
        @if (isset($menu['menu-content']))
            {!! $menu['menu-content'] !!}
        @endif
    </ul>
</div>
