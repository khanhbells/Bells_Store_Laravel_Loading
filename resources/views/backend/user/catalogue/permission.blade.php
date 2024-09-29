@include('backend.dashboard.component.breadcrumb', [
    'title' => $config['seo']['permission']['title'],
])
<form action="{{ route('user.catalogue.updatePermission') }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Cấp quyền</h5>
                    </div>
                    <div class="ibox-content">
                        <table class="table table-striped table-bordered">
                            <tr>
                                <th class="text-center">Tên quyền</th>
                                @foreach ($userCatalogues as $userCatalogue)
                                    @if ($userCatalogue->id != 4)
                                        <!-- Nếu id khác 4 thì hiển thị -->
                                        <th class="text-center">{{ $userCatalogue->name }}</th>
                                    @endif
                                @endforeach
                            </tr>
                            @foreach ($permissions as $permission)
                                <tr>
                                    <td><a href=""
                                            class="uk-flex uk-flex-middle uk-flex-space-between">{{ $permission->name }}
                                            <span style="color: red;">({{ $permission->canonical }})</span></a>
                                    </td>
                                    @foreach ($userCatalogues as $userCatalogue)
                                        @if ($userCatalogue->id != 4)
                                            <td>
                                                <input
                                                    {{ collect($userCatalogue->permissions)->contains('id', $permission->id) ? 'checked' : '' }}
                                                    type="checkbox" name="permission[{{ $userCatalogue->id }}][]"
                                                    value="{{ $permission->id }}" class="form-control">
                                            </td>
                                        @endif
                                    @endforeach
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <button class="btn btn-primary" type="submit" name="send" value="send">Lưu
                    lại</button>
            </div>
        </div>
    </div>
</form>
