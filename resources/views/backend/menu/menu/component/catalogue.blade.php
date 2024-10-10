<div class="row">
    <div class="col-lg-5">
        <div class="panel-head">
            <div class="panel-title">Vị trí menu</div>
            <div class="panel-description">
                <p>- Website có vị trí hiển thị cho từng menu</p>
                <p>- Lựa chọn vị trí mà bạn muốn hiển thị</p>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="ibox">
            <div class="ibox-content">
                <div class="row">
                    <div class="col-lg-12 mb10">
                        <div class="uk-flex uk-flex-middle uk-flex-space-between">
                            <div for="" class="text-bold">Chọn vị trí hiển thị <span
                                    class="text-danger">(*)</span></div>
                            <button data-toggle="modal" data-target="#createMenuCatalogue" type="button" name=""
                                class="addMenuCatalogue btn btn-danger">Tạo vị
                                trí
                                hiển thị</button>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        @if (count($menuCatalogues))
                            <select class="setupSelect2" name="menu_catalogue_id" id="">
                                <option value="">[Chọn vị trí hiển thị]</option>
                                @foreach ($menuCatalogues as $key => $val)
                                    <option value="{{ $val->id }}">{{ $val->name }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
