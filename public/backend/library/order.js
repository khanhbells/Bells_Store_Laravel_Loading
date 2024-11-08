(function ($) {
    "use strict";
    var HT = {};
    var _token = $('meta[name="csrf-token"]').attr('content');

    HT.select2 = () => {
        if ($('.setupSelect2').length) {
            $('.setupSelect2').select2();
        }
    }

    HT.loadCity = (province_id) => {
        if (province_id != '') {
            setTimeout(() => {
                $(".provinces").val(province_id).trigger('change');
            }, 0)
        }
    }

    HT.getLocation = () => {
        $(document).on('change', '.location', function () {
            let _this = $(this)
            let option = {
                'data': {
                    'location_id': _this.val(),
                },
                'target': _this.attr('data-target')
            }
            HT.sendDataTogetLocation(option);
        })
    }

    HT.sendDataTogetLocation = (option) => {
        let district_id = $('.district_id').val()
        let ward_id = $('.ward_id').val()
        $.ajax({
            url: getLocation,
            type: 'GET',
            data: option,
            dataType: 'json',
            success: function (res) {

                $('.' + option.target).html(res.html)
                if (district_id != '' && option.target == 'districts') {
                    $('.districts').val(district_id).trigger('change')
                }
                if (ward_id != '' && option.target == 'wards') {
                    $('.wards').val(ward_id).trigger('change')
                }
                //Hieu don gian la sau khi lay du lieu thi gan vao ten cai class nay, 
                //thẻ nào mà gọi đến class này thì nó sẽ render ra dữ liệu mà nó lấy được 
            },
        });
    }

    HT.editOrder = () => {
        $(document).on('click', '.edit-order', function () {
            let _this = $(this)
            let target = _this.attr('data-target')
            let html = ''
            let originalHtml = _this.parents('.ibox').find('.ibox-content').html()
            if (target === 'description') {
                html = HT.renderDescriptionOrder(_this)
            } else if (target === 'customerInfo') {
                html = HT.renderCustomerOrderInformation()
                setTimeout(() => {
                    HT.select2();
                }, 0)
            }
            _this.parents('.ibox').find('.ibox-content').html(html)
            HT.changeEditToCancle(_this, originalHtml)
        })
    }

    HT.changeEditToCancle = (_this, originalHtml) => {
        let encodedHtml = btoa(encodeURIComponent(originalHtml.trim()));
        _this.html('Hủy bỏ').removeClass('edit-order').addClass('cancle-edit').attr('data-html', encodedHtml)
    }

    HT.cancleEdit = () => {
        $(document).on('click', '.cancle-edit', function () {
            let _this = $(this)
            let originalHtml = decodeURIComponent(atob(_this.attr('data-html')))
            _this.html('Sửa').removeClass('cancle-edit').addClass('edit-order')
            _this.parents('.ibox').find('.ibox-content').html(originalHtml)
        })
    }

    HT.renderDescriptionOrder = (_this) => {

        let inputValue = _this.parents('.ibox').find('.ibox-content').text().trim()
        return '<input class="form-control ajax-edit" name="description"  data-field="description" value="' + inputValue + '">'
    }

    HT.renderCustomerOrderInformation = () => {
        let data = {
            fullname: $('.fullname').text(),
            email: $('.email').text(),
            phone: $('.phone').text(),
            address: $('.address').text(),
            ward_id: $('.ward_id').val(),
            district_id: $('.district_id').val(),
            province_id: $('.province_id').val(),
        }
        let html = `
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="">Họ tên</label>
                    <input type="text" name="fullname" value="${data.fullname}" class="form-control">
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="">Email</label>
                    <input type="text" name="email" value="${data.email}" class="form-control">
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="">Số điện thoại</label>
                    <input type="text" name="phone" value="${data.phone}" class="form-control">
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="">Địa chỉ</label>
                    <input type="text" name="address" value="${data.address}" class="form-control">
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="">Thành phố</label>
                    <select name="province_id" class="setupSelect2 provinces location" data-target="districts">
                        <option>
                            [Chọn Thành Phố]
                        </option>
                        ${HT.provincesList(data.province_id)}
                    </select>
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="">Quận/Huyện</label>
                    <select name="district_id" class="setupSelect2 districts location" data-target="wards">
                        <option>
                            [Chọn Quận Huyện]
                        </option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="">Phường/Xã</label>
                    <select name="ward_id" class="setupSelect2 wards">
                        <option>
                            [Chọn Phường Xã]
                        </option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <button class="btn btn-primary saveCustomer">Lưu lại</button>
                </div>
            </div>
        </div>
        `
        setTimeout(() => {
            HT.loadCity(data.province_id)
        }, 0)
        return html;
    }

    HT.saveCustomer = () => {
        $(document).on('click', '.saveCustomer', function (e) {
            e.preventDefault()
            let _this = $(this)
            let option = {
                id: $('.orderId').val(),
                payload: {
                    fullname: $('input[name=fullname]').val(),
                    email: $('input[name=email]').val(),
                    phone: $('input[name=phone]').val(),
                    address: $('input[name=address]').val(),
                    ward_id: $('.wards').val(),
                    district_id: $('.districts').val(),
                    province_id: $('.provinces').val(),
                },
                _token: _token
            }
            HT.ajaxUpdateOrderInfo(option, _this);
        })


    }

    HT.provincesList = () => {
        let html = ''
        for (let i = 0; i < provinces.length; i++) {
            html += '<option value="' + provinces[i].id + '">' + provinces[i].name + '</option>'
        }
        return html
    }

    HT.updateDescription = () => {
        $(document).on('change', '.ajax-edit', function () {
            let _this = $(this)
            let field = _this.attr('data-field')
            let value = _this.val()
            let option = {
                id: $('.orderId').val(),
                payload: {
                    [field]: value
                },
                _token: _token
            }
            HT.ajaxUpdateOrderInfo(option, _this)
        })

    }

    HT.ajaxUpdateOrderInfo = (option, _this) => {
        $.ajax({
            url: 'ajax/order/update',
            type: 'POST',
            data: option,
            dataType: 'json',
            success: function (res) {
                if (res.error == 10) {
                    if (_this.parents('.ibox').find('.cancle-edit').attr('data-target') == 'description') {
                        HT.renderDescriptionHtml(option.payload, _this.parents('.ibox'))
                    } else if (_this.parents('.ibox').find('.cancle-edit').attr('data-target') == 'customerInfo') {
                        HT.renderCustomerInfoHtml(res)
                    }
                }
            },
        });
    }

    HT.renderCustomerInfoHtml = (res) => {
        let html = `<div class="customer-line">
                        <strong>N:</strong>
                        <span class="fullname">${res.order.fullname}</span>
                    </div>
                    <div class="customer-line">
                        <strong>E:</strong>
                        <span class="email"> ${res.order.email}</span>
                    </div>
                    <div class="customer-line">
                        <strong>P:</strong>
                        <span class="phone"> ${res.order.phone}</span>
                    </div>
                    <div class="customer-line">
                        <strong>A:</strong>
                        <span class="address"> ${res.order.address}</span>

                    </div>
                    <div class="customer-line">
                        <strong>P:</strong>
                        ${res.order.ward_name}

                    </div>
                    <div class="customer-line">
                        <strong>Q:</strong>
                        <span class="district_name">
                        ${res.order.district_name}
                        </span>

                    </div>
                    <div class="customer-line">
                        <strong>T:</strong>
                        <span class="province_name">
                            ${res.order.province_name}
                        </span>
                    </div>`
        $('.order-customer-information').html(html)
        $('.ward_id').val(res.order.ward_id)
        $('.district_id').val(res.order.district_id)
        $('.province_id').val(res.order.province_id)

        $('.order-customer-information').parents('.ibox').find('.cancle-edit').html('Sửa').removeClass('cancle-edit').addClass('edit-order').attr('data-html', '').html('Sửa')
    }

    HT.renderDescriptionHtml = (payload, target) => {
        target.find('.ibox-content').html(payload.description)
        target.find('.cancle-edit').html('Sửa').removeClass('cancle-edit').addClass('edit-order').attr('data-html', '').html('Sửa')

    }

    HT.updateField = () => {
        $(document).on('click', '.updateField', function () {
            let _this = $(this)
            let option = {
                payload: {
                    [_this.attr('data-field')]: _this.attr('data-value')
                },
                id: $('.orderId').val(),
                _token: _token
            }
            $.ajax({
                url: 'ajax/order/update',
                type: 'POST',
                data: option,
                dataType: 'json',
                success: function (res) {
                    if (res.error == 10) {
                        HT.createOrderConfirmSection(_this)
                    }
                },
            });
        })
    }
    HT.updateBadge = () => {
        $(document).on('change', '.updateBadge', function () {
            let _this = $(this)
            let option = {
                payload: {
                    [_this.attr('data-field')]: _this.val()
                },
                id: _this.parents('tr').find('.checkBoxItem').val(),
                _token: _token
            }
            let confirmStatus = _this.parents('tr').find('.confirm').val()
            toastr.clear()
            if (confirmStatus != 'pending') {
                $.ajax({
                    url: 'ajax/order/update',
                    type: 'POST',
                    data: option,
                    dataType: 'json',
                    success: function (res) {
                        if (res.error === 10) {
                            toastr.success('Cập nhật trạng thái thành công', 'Thông báo từ hệ thống!')
                        } else {
                            toastr.error('Có vấn đề xảy ra hãy thử lại', 'Thông báo từ hệ thống!')
                        }
                    },
                });
            } else {
                // let originalStatus = _this.siblings('.changerOrderStatus').val()
                // _this.val(originalStatus)
                toastr.error('Bạn phải xác nhận đơn hàng trước khi thực hiện cập nhật này', 'Thông báo từ hệ thống!')
            }

        })
    }

    HT.createOrderConfirmSection = (_this) => {
        let button = `<button class="button updateField" data-value="cancle" data-field="confirm" data-title="ĐÃ HỦY THANH TOÁN ĐƠN HÀNG">Hủy
                                đơn</button>`
        let correctImage = 'backend/img/correct.png'
        $('.confirm-box').find('img').attr('src', BASE_URL + correctImage)
        $('.isConfirm').html(_this.attr('data-title'))
        if (_this.attr('data-field') == 'confirm') {
            $('.confirm-block').html('Đã xác nhận')
            $('.cancle-block').html(button)
        }
        if (_this.attr('data-field') == 'cancle') {
            _this.parent().html('Đơn hàng đã được hủy')
        }
    }



    $(document).ready(function () {
        HT.select2()
        HT.editOrder()
        HT.updateDescription()
        HT.cancleEdit()
        HT.getLocation()
        HT.saveCustomer()
        HT.updateField()
        HT.updateBadge()
    });

})(jQuery);

