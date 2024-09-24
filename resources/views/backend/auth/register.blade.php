<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>BELLS STORE | Register</title>
    <link href="backend/css/bootstrap.min.css" rel="stylesheet">
    <link href="backend/font-awesome/css/font-awesome.css" rel="stylesheet">
    {{-- <link href="backend/css/plugins/iCheck/custom.css" rel="stylesheet"> --}}
    <link href="backend/css/animate.css" rel="stylesheet">
    <link href="backend/plugin/jquery-ui.css" rel="stylesheet">
    <link href="backend/css/style.css" rel="stylesheet">
    <link href="backend/css/customize.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
</head>

<body class="gray-bg">
    <div class="  loginscreen   animated fadeInDown">
        <div>
            <div>
                <h1 class=" text-center logo-name">Bells Store</h1>
            </div>
            <h2 class="text-center">Register to Bells Store</h2>
            <p class="text-center">Create account to see it in action.</p>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('register.store') }}" method="post" class="box">
                @csrf
                <div class="wrapper wrapper-content animated fadeInRight d-flex justify-content-center align-items-center"
                    style="min-height: 100vh;">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="panel-head">
                                <div class="panel-title"><strong class="fs15">Thông tin chung</strong></div>
                                <div class="panel-description">
                                    <p class="fs15">- Nhập thông tin chung của người sử dụng</p>
                                    <p class="fs15">- Lưu ý: Những trường đánh dấu <span
                                            class="text-danger">(*)</span> là bắt buộc
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="ibox">
                                <div class="ibox-content">
                                    <div class="row mb15">
                                        <div class="col-lg-6">
                                            <div class="form-row">
                                                <label for="" class="control-label text-left">Email
                                                    <span class="text-danger">(*)</span>
                                                </label>
                                                <input type="email" name="email" value="{{ old('email') }}"
                                                    class="form-control" placeholder="" autocomplete="off" />
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-row">
                                                <label for="" class="control-label text-left">Họ tên
                                                    <span class="text-danger">(*)</span>
                                                </label>
                                                <input type="text" name="name" value="{{ old('name') }}"
                                                    class="form-control" placeholder="" autocomplete="off" />
                                            </div>
                                        </div>
                                    </div>
                                    @php
                                        $userCatalogue = ['[Chọn nhóm thành viên]', 'Quản trị viên', 'Cộng tác viên'];

                                    @endphp
                                    <div class="row mb15">
                                        <div class="col-lg-6">
                                            <div class="form-row">
                                                <label for="" class="control-label text-left">Ngày sinh
                                                </label>
                                                <input type="date" name="birthday" value="{{ old('birthday') }}"
                                                    class="form-control" placeholder="" autocomplete="off" />
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-row">
                                                <label for="" class="control-label text-left">Ảnh đại diện
                                                </label>
                                                <input type="text" name="image" value="{{ old('image') }}"
                                                    class="form-control upload-image" placeholder="" autocomplete="off"
                                                    data-upload="Images" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb15">
                                        <div class="col-lg-6">
                                            <div class="form-row">
                                                <label for="" class="control-label text-left">Mật khẩu
                                                    <span class="text-danger">(*)</span>
                                                </label>
                                                <input type="password" name="password" value=""
                                                    class="form-control" placeholder="" autocomplete="off" />
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-row">
                                                <label for="" class="control-label text-left">Nhập lại mật
                                                    khẩu
                                                    <span class="text-danger">(*)</span>
                                                </label>
                                                <input type="password" name="re_password" value=""
                                                    class="form-control" placeholder="" autocomplete="off" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="panel-head">
                                <div class="panel-title"><strong class="fs15">Thông tin liên hệ</strong></div>
                                <div class="panel-description">
                                    <p class="fs15">- Nhập thông tin liên hệ của người sử dụng</p>
                                    <p class="fs15">- Lưu ý: Những trường đánh dấu <span
                                            class="text-danger">(*)</span> là bắt buộc
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="ibox">
                                <div class="ibox-content">
                                    <div class="row mb15">
                                        <div class="col-lg-6">
                                            <div class="form-row">
                                                <label for="" class="control-label text-left">Thành phố
                                                </label>
                                                <select name="province_id"
                                                    class="form-control setupSelect2 province location"data-target="districts">
                                                    <option value="0">[Chọn Thành Phố]</option>
                                                    @if (isset($provinces))
                                                        @foreach ($provinces as $province)
                                                            <option @if (old('province_id') == $province->code) selected @endif
                                                                value="{{ $province->code }}">{{ $province->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-row">
                                                <label for="" class="control-label text-left">Quận/Huyện
                                                </label>
                                                <select name="district_id"
                                                    class="form-control setupSelect2 districts location"
                                                    data-target="wards">
                                                    <option value="0">[Chọn Quận Huyện]</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb15">
                                        <div class="col-lg-6">
                                            <div class="form-row">
                                                <label for="" class="control-label text-left">Phường/Xã
                                                </label>
                                                <select name="ward_id" class="form-control setupSelect2 wards">
                                                    <option value="0">[Chọn Phường/Xã]</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-row">
                                                <label for="" class="control-label text-left">Địa chỉ
                                                </label>
                                                <input type="text" name="address" value="{{ old('address') }}"
                                                    class="form-control" placeholder="" autocomplete="off" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb15">
                                        <div class="col-lg-6">
                                            <div class="form-row">
                                                <label for="" class="control-label text-left">Số Điện Thoại
                                                </label>
                                                <input type="text" name="phone" value="{{ old('phone') }}"
                                                    class="form-control" placeholder="" autocomplete="off" />
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-row">
                                                <label for="" class="control-label text-left">Ghi chú
                                                </label>
                                                <input type="text" name="description"
                                                    value="{{ old('description') }}" class="form-control"
                                                    placeholder="" autocomplete="off" />
                                            </div>
                                            <input type="text" hidden name="user_catalogue_id" value="4">
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row mb15">
                                        <div class="col-lg-6">
                                            <div class="form-row">
                                                <div class="text-left">
                                                    <button class="btn btn-primary" type="submit" name="send"
                                                        value="send"><a style="color: aliceblue"
                                                            href="{{ route('auth.admin') }}"><span></span>Quay
                                                            lại trang đăng nhập</a></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-row">
                                                <div class="text-right">
                                                    <button class="btn btn-primary" type="submit" name="send"
                                                        value="send">Đăng ký ngay</button>
                                                </div>
                                            </div>
                                            <input type="text" hidden name="user_catalogue_id" value="4">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <p class="m-t"> <small>Inspinia we app framework base on Bootstrap 3 &copy; 2014</small> </p>
        </div>
    </div>

    <!-- Mainly scripts -->
    <script src="backend/js/jquery-3.1.1.min.js"></script>
    <script src="backend/js/bootstrap.min.js"></script>
    <script src="backend/js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="backend/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="backend/js/inspinia.js"></script>
    <script src="backend/js/plugins/pace/pace.min.js"></script>
    {{-- <script src="backend/js/plugins/iCheck/icheck.min.js"></script> --}}
    <script src="backend/library/library1.js"></script>
    <script src="backend/plugin/jquery-ui.js"></script>
    <script src="backend/library/location.js"></script>
    <script src="backend/library/finder.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="backend/plugin/ckfinder_2/ckfinder.js"></script>
    <script>
        // jQuery(document).ready(function($) {
        //     $('.i-checks').iCheck({
        //         checkboxClass: 'icheckbox_square-green',
        //         radioClass: 'iradio_square-green',
        //     });
        // });
    </script>
    <script>
        var province_id = '{{ isset($user->province_id) ? $user->province_id : old('province_id') }}'
        var district_id = '{{ isset($user->district_id) ? $user->district_id : old('district_id') }}'
        var ward_id = '{{ isset($user->ward_id) ? $user->ward_id : old('ward_id') }}'
        var getLocation = "{{ url('ajax/location/getLocation') }}";
    </script>
</body>

</html>
