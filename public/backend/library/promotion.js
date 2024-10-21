(function ($) {
    "use strict";
    var HT = {};
    var typingTimer;
    var doneTyingInterval = 1000;

    $.fn.elExist = function () {
        return this.length > 0
    }

    HT.promotionNeverEnd = () => {
        $(document).on('change', '#neverEnd', function () {
            let _this = $(this)
            let isChecked = _this.prop('checked')
            if (isChecked) {
                $('input[name=endDate]').val('').attr('disabled', true)
            } else {
                let endDate = $('input[name=startDate]').val()
                $('input[name=endDate]').val(endDate).attr('disabled', false)
            }
        })
    }

    HT.promotionSource = () => {
        $(document).on('click', '.chooseSource', function () {
            let _this = $(this)
            let flag = (_this.attr('id') == 'allSource') ? true : false;
            if (flag) {
                _this.parents('.ibox-content').find('.source-wrapper').remove()
            } else {
                $.ajax({
                    url: 'ajax/source/getAllSource',
                    type: 'GET',
                    // data: option,
                    dataType: 'json',
                    success: function (res) {
                        let sourceData = res.data
                        if (!$('.source-wrapper').length) {
                            let sourceHtml = HT.renderPromotionSource(sourceData).prop('outerHTML')
                            _this.parents('.ibox-content').append(sourceHtml)
                            HT.promotionMultipleSelect2()
                        }
                    },
                });
            }
        })
    }



    HT.renderPromotionSource = (sourceData) => {
        let wrapper = $('<div>').addClass('source-wrapper')
        if (sourceData.length) {
            let select = $('<select>')
                .addClass('multipleSelect2')
                .attr('name', 'sourceValue[]')
                .attr('multiple', true)
            for (let i = 0; i < sourceData.length; i++) {
                let option = $('<option>').attr('value', sourceData[i].id).text(sourceData[i].name)
                select.append(option)
            }
            wrapper.append(select)
        }
        return wrapper;
    }



    HT.chooseCustomerCondition = () => {
        $(document).on('change', '.chooseApply', function () {
            let _this = $(this)
            let id = _this.attr('id')
            if (id === 'allApply') {
                _this.parents('.ibox-content').find('.apply-wrapper').remove()
            } else {
                let applyHtml = HT.renderApplyCondition().prop('outerHTML')
                _this.parents('.ibox-content').append(applyHtml)
                HT.promotionMultipleSelect2()
            }

        })
    }
    HT.renderApplyCondition = () => {
        let applyConditionData = JSON.parse($('.applyStatusList').val())
        let wrapper = $('<div>').addClass('apply-wrapper')
        let wrapperConditionItem = $('<div>').addClass('wrapper-condition')
        if (applyConditionData.length) {
            let select = $('<select>')
                .addClass('multipleSelect2 conditionItem')
                .attr('name', 'applyValue[]')
                .attr('multiple', true)
            for (let i = 0; i < applyConditionData.length; i++) {
                let option = $('<option>').attr('value', applyConditionData[i].id).text(applyConditionData[i].name)
                select.append(option)
            }
            wrapper.append(select)
            wrapper.append(wrapperConditionItem)
        }

        return wrapper;
    }

    HT.chooseApplyItem = () => {
        $(document).on('change', '.conditionItem', function () {
            let _this = $(this)
            let condition = {
                value: _this.val(),
                label: _this.select2('data')
            }

            $('.wrapperConditionItem').each(function () {
                let _item = $(this)
                let itemClass = _item.attr('class').split(' ')[2]
                if (condition.value.includes(itemClass) == false) {
                    _item.remove()
                }

            })

            for (let i = 0; i < condition.value.length; i++) {
                let value = condition.value[i]
                let html = HT.createConditionItem(value, condition.label[i].text)

            }
        })
    }

    HT.createConditionItem = (value, label) => {
        if (!$('.wrapper-condition').find('.' + value).elExist()) {
            $.ajax({
                url: 'ajax/dashboard/getPromotionConditionValue',
                type: 'GET',
                data: {
                    value: value
                },
                dataType: 'json',
                success: function (res) {
                    let optionData = res.data
                    let conditionItem = $('<div>').addClass('wrapperConditionItem mt10 ' + value)
                    let conditionHiddenInput = $('.condition_input_' + value)
                    let conditionHiddenInputValue = []
                    if (conditionHiddenInput.length) {
                        conditionHiddenInputValue = JSON.parse(conditionHiddenInput.val())
                    }
                    let select = $('<select>')
                        .addClass('multipleSelect2 objectItem')
                        .attr('name', value + "[]")
                        .attr('multiple', true)
                    for (let i = 0; i < optionData.length; i++) {
                        let option = $('<option>').attr('value', optionData[i].id).text(optionData[i].text)
                        select.append(option)
                    }
                    console.log(conditionHiddenInputValue);
                    select.val(conditionHiddenInputValue).trigger('change')

                    const conditionLabel = HT.createConditionLabel(label, value)
                    conditionItem.append(conditionLabel)
                    conditionItem.append(select)

                    $('.wrapper-condition').append(conditionItem)
                    HT.promotionMultipleSelect2()
                },
            });

        }


    }

    HT.createConditionLabel = (label, value) => {
        // let deleteButton = $('<div>').addClass('delete').html(`<svg data-icon="TrashSolidLarge" aria-hidden="true" focusable="false" width="15" height="16" viewBox="0 0 15 16" class="bem-Svg" style="display: block;">
        //                                 <path fill="currentColor" d="M2 14a1 1 0 001 1h9a1 1 0 001-1V6H2v8zM13 2h-3a1 1 0 01-1-1H6a1 1 0 01-1 1H1v2h13V2h-1z">
        //                                 </path>
        //                             </svg>`).attr('data-condition-item', value)
        let conditionLabel = $('<div>').addClass('conditionLabel').text(label)
        let flex = $('<div>').addClass('uk-flex uk-flex-middle uk-flex-space-between')
        let wrapperBox = $('<div>').addClass('mb5')
        flex.append(conditionLabel)
        // .append(deleteButton)
        wrapperBox.append(flex)
        return wrapperBox.prop('outerHTML')

    }

    HT.deleteCondition = () => {
        $(document).on('click', '.wrapperConditionItem .delete', function () {
            let _this = $(this)
            let unSelectedValue = _this.attr('data-condition-item')
            let selectedItem = $('.conditionItem').val()
            let indexOf = selectedItem.indexOf(unSelectedValue)
            if (indexOf !== -1) {
                selectedItem.splice(selectedItem, indexOf)
            }


            // $('.conditionItem').val(unSelectedValue).trigger('change')
        })
    }



    HT.promotionMultipleSelect2 = () => {
        $('.multipleSelect2').select2({
            // minimumInputLength: 2,
            placeholder: 'Click vào ô để lựa chọn...',
            // ajax: {
            //     url: 'ajax/attribute/getAttribute',
            //     type: 'GET',
            //     dataType: 'json',
            //     deley: 250,
            //     data: function (params) {
            //         return {
            //             search: params.term,
            //             option: option,
            //         }
            //     },
            //     processResults: function (data) {
            //         return {
            //             results: data.items
            //         }
            //     },
            //     cache: true

            // }
        });
    }

    var ranges = []

    //Cấu hình HTML
    HT.btnJs100 = () => {
        $(document).on('click', '.btn-js-100', function () {
            let _button = $(this)
            let trLastChild = $('.order_amount_range').find('tbody tr:last-child');
            let newTo = parseInt(trLastChild.find('.order_amount_range_to input').val().replace(/\./g, ''))
            let $tr = $('<tr>')
            let tdList = [
                {
                    class: 'order_amount_range_from td-range',
                    name: 'promotion_order_amount_range[amountFrom][]',
                    value: addCommas(parseInt(newTo) + 1),
                },
                {
                    class: 'order_amount_range_to td-range',
                    name: 'promotion_order_amount_range[amountTo][]',
                    value: 0,
                },
            ]
            for (let i = 0; i < tdList.length; i++) {
                let $td = $('<td>', { class: tdList[i].class })
                let $input = $('<input>').addClass('form-control int')
                    .attr('name', tdList[i].name)
                    .attr('value', tdList[i].value)
                $td.append($input)
                $tr.append($td)
            }

            let discountTd = $('<td>').addClass('discountType')
            discountTd.append(
                $('<div>', { class: 'uk-flex uk-flex-middle' }).append(
                    $('<input>', {
                        type: 'text',
                        name: 'promotion_order_amount_range[amountValue][]',
                        class: 'form-control int',
                        placeholder: 0,
                        value: 0
                    })
                ).append(
                    $('<select>', {
                        class: 'multipleSelect2'
                    }).append('name', 'promotion_order_amount_range[amountType][]')
                        .append($('<option>', { value: 'cash', text: 'đ' }))
                        .append($('<option>', { value: 'percent', text: '%' }))
                )
            )
            $tr.append(discountTd)
            let deleteButton = $('<td>').append(
                $('<div>', {
                    class: 'delete-some-item delete-order-amount-range-condition'
                }).append(`<svg data-icon="TrashSolidLarge" aria-hidden="true"
                                focusable="false" width="15" height="16"
                                viewBox="0 0 15 16" class="bem-Svg" style="display: block;">
                                <path fill="currentColor"
                                    d="M2 14a1 1 0 001 1h9a1 1 0 001-1V6H2v8zM13 2h-3a1 1 0 01-1-1H6a1 1 0 01-1 1H1v2h13V2h-1z">
                                </path>
                            </svg>`)
            )
            $tr.append(deleteButton)
            $('.order_amount_range table tbody').append($tr)
            HT.promotionMultipleSelect2()
        })
    }

    HT.rangeOnChange = () => {
        $(document).on('change', '.td-range .form-control', function (e) {
            e.preventDefault()
            let _this = $(this)
            let ranges = []
            $('.order_amount_range tbody tr').each(function (index, value) {
                let newFrom = parseInt($(this).find('.order_amount_range_from .form-control').val().replace(/\./g, ''))
                let newTo = parseInt($(this).find('.order_amount_range_to .form-control').val().replace(/\./g, ''))
                if (!isNaN(newFrom)) {
                    ranges.push({ from: newFrom, to: newTo })
                }
            })
            console.log(ranges);

            // for (let i = 0; i < ranges.length; i++) {
            //     for (let j = i+1; j < ranges.length; j++) {

            //     }

            // }
        })
    }

    HT.deleteAmountRangeCondition = () => {
        $(document).on('click', '.delete-order-amount-range-condition', function () {
            let _this = $(this)
            _this.parents('tr').remove()
        })
    }

    HT.renderOrderRangeConditionContainer = () => {

        $(document).on('change', '.promotionMethod', function () {
            let _this = $(this)
            let option = _this.val()
            switch (option) {
                case 'order_amount_range':
                    HT.renderOrderAmountRange()
                    break;
                case 'product_and_quantity':
                    HT.renderProductAndQuantity()
                    break;
                // case 'product_quantity_range':
                //     console.log('product_quantity_range');
                //     break;
                // case 'goods_discount_by_quantity':
                //     console.log('goods_discount_by_quantity');
                //     break;
                default:
                    HT.removePromotionContainer()
            }
        })
        let method = $('.preload_promotionMethod').val()
        if (method.length && typeof method !== 'undefined') {
            $('.promotionMethod').val(method).trigger('change')
        }
    }

    HT.removePromotionContainer = () => {

        $('.promotion-container').html('')
    }

    HT.renderOrderAmountRange = () => {
        let $tr = ''
        let order_amount_range = JSON.parse($('.input_order_amount_range').val()) || {
            amountFrom: ['0'],
            amountTo: ['0'],
            amountValue: ['0'],
            amountType: ['cash'],
        }
        for (let i = 0; i < order_amount_range.amountFrom.length; i++) {
            let $amountFrom = order_amount_range.amountFrom[i]
            let $amountTo = order_amount_range.amountTo[i]
            let $amountValue = order_amount_range.amountValue[i]
            let $amountType = order_amount_range.amountType[i]
            $tr += `<tr>
                <td class="order_amount_range_from td-range">
                    <input type="text" name="promotion_order_amount_range[amountFrom][]" class="form-control int"
                        placeholder="0" value="${$amountFrom}">
                </td>
                <td class="order_amount_range_to td-range">
                    <input type="text" name="promotion_order_amount_range[amountTo][]" class="form-control int"
                        placeholder="0" value="${$amountTo}">
                </td>
                <td class="discountType">
                    <div class="uk-flex uk-flex-middle">
                        <input type="text" name="promotion_order_amount_range[amountValue][]" class="form-control int"
                            placeholder="0" value="${$amountValue}">
                        <select name="promotion_order_amount_range[amountType][]" class="multipleSelect2" id="">
                            <option value="cash" ${($amountType == 'cash') ? 'selected' : ''}>đ</option>
                            <option value="percent" ${($amountType == 'percent') ? 'selected' : ''}>%</option>
                        </select>
                    </div>
                </td>
                <td>
                <div class="delete-some-item delete-order-amount-range-condition"><svg data-icon="TrashSolidLarge" aria-hidden="true" focusable="false" width="15" height="16" viewBox="0 0 15 16" class="bem-Svg" style="display: block;">
                    <path fill="currentColor" d="M2 14a1 1 0 001 1h9a1 1 0 001-1V6H2v8zM13 2h-3a1 1 0 01-1-1H6a1 1 0 01-1 1H1v2h13V2h-1z">
                    </path>
                </svg></div>
                </td>
            </tr>`
        }

        let html = `<div class="order_amount_range">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th class="text-right">Giá trị từ</th>
                                            <th class="text-right">Giá trị đến</th>
                                            <th class="text-right">Chiết khấu (đ/%)</th>
                                            <th class="text-right"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${$tr}
                                    </tbody>
                                </table>
                                <button class="btn btn-success btn-custom btn-js-100" value="" type="button">Thêm
                                    điều
                                    kiện</button>
                            </div>`
        HT.renderPromotionalContainer(html)
    }

    HT.renderProductAndQuantity = () => {
        let selectData = JSON.parse($('.input-product-and-quantity').val())
        let selectHtml = ''
        let moduleType = $('.preload_select-product-and-quantity').val()
        for (let key in selectData) {
            selectHtml += ' <option ' + ((moduleType.length && typeof moduleType !== 'undefined' && moduleType == key) ? 'selected' : '') + ' value="' + key + '">' + selectData[key] + '</option>'
        }

        let preloadData = JSON.parse($('.input_product_and_quantity').val()) || {
            quantity: ['1'],
            maxDiscountValue: ['0'],
            discountValue: ['0'],
            discountType: ['cash'],
        }
        let html = `<div class="product_and_quantity">
                        <div class="choose-module mt20">
                            <div class="fix-label" style="color: blue">Sản phẩm áp dụng</div>
                            <select name="module_type" id=""
                                class="multipleSelect2 select-product-and-quantity">
                                ${selectHtml}
                            </select>
                        </div>
                    <table class="table table-striped mt20">
                        <thead>
                            <tr>
                                <th class="text-right" style="width:400px">Sản phẩm mua</th>
                                <th class="text-right" style="width:80px">Tối thiểu </th>
                                <th class="text-right">Giới hạn khuyến mãi</th>
                                <th class="text-right">Chiết khấu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="chooseProductPromotionTd">
                                    <div class="product-quantity" data-toggle="modal"
                                        data-target="#findProduct">
                                        <div class="boxWrapper">
                                            <div class="boxSearchIcon">
                                                <i class="fa fa-search"></i>
                                            </div>

                                            <div class="boxSearchInput fixGrid6">
                                                <p>Tìm theo tên, mã sản phẩm</p>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            <td>
                                <input type="text" name="product_and_quantity[quantity]" class="form-control int"
                                    value="${preloadData.quantity}">
                            </td>
                            <td class="order_amount_range_to td-range">
                                <input type="text" name="product_and_quantity[maxDiscountValue]" class="form-control int"
                                    placeholder="0" value="${preloadData.maxDiscountValue}">
                            </td>
                            <td class="discountType">
                                <div class="uk-flex uk-flex-middle">
                                    <input type="text" name="product_and_quantity[discountValue]" class="form-control int"
                                        placeholder="0" value="${preloadData.discountValue}">
                                    <select name="product_and_quantity[discountType]" class="multipleSelect2" id="">
                                        <option value="cash" ${(preloadData.discountType == 'cash') ? 'selected' : ''}>đ</option>
                                        <option value="percent" ${(preloadData.discountType == 'percent') ? 'selected' : ''}>%</option>
                                    </select>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                </div>`
        HT.renderPromotionalContainer(html)
    }

    HT.renderPromotionalContainer = (html) => {
        $('.promotion-container').html(html)
        HT.promotionMultipleSelect2()
    }

    HT.setupAjaxSearch = () => {
        $('.ajaxSearch').each(function () {
            let _this = $(this)
            let option = {
                model: _this.attr('data-model')
            }
            _this.select2({
                minimumInputLength: 2,
                placeholder: 'Nhập vào 2 từ để tìm kiếm',
                closeOnSelect: true,
                ajax: {
                    url: 'ajax/dashboard/findPromotionObject',
                    type: 'GET',
                    dataType: 'json',
                    deley: 250,
                    data: function (params) {
                        return {
                            search: params.term,
                            option: option,
                        }
                    },
                    processResults: function (data) {
                        return {
                            results: data.items
                        }
                    },
                    cache: true

                }
            });
        })
    }

    HT.productQuantityListProduct = () => {
        $(document).on('click', '.product-quantity', function (e) {
            e.preventDefault()
            let option = {
                model: $('.select-product-and-quantity').val(),
            }
            HT.loadProduct(option)
        })
    }

    HT.loadProduct = (option) => {
        $.ajax({
            url: 'ajax/product/loadProductPromotion',
            type: 'GET',
            data: option,
            dataType: 'json',
            success: function (res) {
                HT.fillTObjectList(res)
            },
        });
    }

    HT.getPaginationMenu = () => {
        $(document).on('click', '.page-link', function (e) {
            e.preventDefault()
            let _this = $(this)
            let option = {
                model: $('.select-product-and-quantity').val(),
                page: _this.text(),
                keyword: $('.search-model').val()

            }
            HT.loadProduct(option)
        })
    }

    HT.fillTObjectList = (data) => {
        switch (data.model) {
            case 'Product':
                HT.fillProductToList(data.objects)
                break;
            case 'ProductCatalogue':
                HT.fillProductCatalogueToList(data.objects)
                break;
        }
    }

    HT.fillProductCatalogueToList = (object) => {
        let html = ''
        if (object.data.length) {
            let model = $('.select-product-and-quantity').val()
            for (let i = 0; i < object.data.length; i++) {
                let name = object.data[i].name
                let id = object.data[i].id
                let classBox = model + '_' + id
                let isChecked = ($('.boxWrapper .' + classBox + '').length) ? true : false
                html += `<div class="search-object-item" data-productid="${id}" data-name="${name}" data-type="ProductCatalogue">
                                <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                    <div class="object-info">
                                        <div class="uk-flex uk-flex-middle">
                                            <input type="checkbox" name="" value="${id}"
                                                class="input-checkbox mt10" ${(isChecked) ? 'checked' : ''}>
                                            <div class="object-name">
                                                <div class="name" style="margin:9px 0 0 5px">${name}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>`
            }
        }
        html = html + HT.paginationLinks(object.links).prop('outerHTML')
        $('.search-list').html(html)
    }

    HT.fillProductToList = (object) => {
        let html = ''
        if (object.data.length) {
            let model = $('.select-product-and-quantity').val()
            for (let i = 0; i < object.data.length; i++) {
                let image = object.data[i].image
                let name = object.data[i].variant_name
                let product_variant_id = object.data[i].product_variant_id
                let product_id = object.data[i].id
                let inventory = (typeof object.data.inventory != 'undefined') ? inventory : 0
                let couldSell = (typeof object.data.couldSell != 'undefined') ? couldSell : 0
                let sku = object.data[i].sku
                let price = object.data[i].price
                let classBox = model + '_' + product_id + '_' + product_variant_id

                let isChecked = ($('.boxWrapper .' + classBox + '').length) ? true : false
                console.log(isChecked);
                html += `<div class="search-object-item" data-productid="${product_id}" data-variant_id="${product_variant_id}" data-name="${name}" data-type="Product">
                                <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                    <div class="object-info">
                                        <div class="uk-flex uk-flex-middle">
                                            <input type="checkbox" name="" value="${product_id + '_' + product_variant_id}"
                                                class="input-checkbox mt10" ${(isChecked) ? 'checked' : ''}>
                                            <span class="image img-scaledown">
                                                <img src="` + baseUrl + `${image[0]}"
                                                    alt="">
                                            </span>
                                            <div class="object-name">
                                                <div class="name">${name}</div>
                                                <div class="jscode">Mã SP: ${sku}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="object-extra-info">
                                        <div class="price">${addCommas(price)}</div>
                                        <div class="object-inventory">
                                            <div class="uk-flex uk-flex-middle">
                                                <span class="text-1">Tồn kho:</span>
                                                <span class="text-value">${inventory}</span>
                                                <span class="text-1 slash">|</span>
                                                <span class="text-value">Có thể bán: ${couldSell}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>`
            }
        }
        html = html + HT.paginationLinks(object.links).prop('outerHTML')
        $('.search-list').html(html)
    }

    HT.searchObject = () => {

        $(document).on('keyup', '.search-model', function (e) {
            let _this = $(this)
            let keyword = _this.val()
            let option = {
                model: $('.select-product-and-quantity').val(),
                keyword: keyword
            }
            clearTimeout(typingTimer)
            typingTimer = setTimeout(function () {
                HT.loadProduct(option)
            }, doneTyingInterval)
        })
    }

    var objectChoose = []
    HT.chooseProductPromotion = () => {
        $(document).on('click', '.search-object-item, .input-checkbox', function (e) {
            // Nếu nhấn vào chính checkbox hoặc toàn bộ .search-object-item
            let _this = $(this).hasClass('search-object-item') ? $(this) : $(this).closest('.search-object-item')
            let checkbox = _this.find('input[type=checkbox]')
            let isChecked = checkbox.prop('checked')
            let objectType = _this.attr('data-type')
            let objectItem = {
                id: _this.attr('data-productid'),
                product_variant_id: _this.attr('data-variant_id') || null,
                name: _this.attr('data-name'),
                type: objectType
            }

            if (objectType == 'Product') {
                var classBox = $('.select-product-and-quantity').val() + '_' + objectItem.id + '_' + objectItem.product_variant_id
            } else {
                var classBox = $('.select-product-and-quantity').val() + '_' + objectItem.id
            }

            if (isChecked) {
                // Nếu checkbox đang được chọn, bỏ chọn và xóa phần tử khỏi mảng objectChoose
                if (objectType == 'Product') {
                    objectChoose = objectChoose.filter(item =>
                        !(item.id === objectItem.id && item.product_variant_id === objectItem.product_variant_id)
                    )
                } else {
                    objectChoose = objectChoose.filter(item =>
                        !(item.id === objectItem.id)
                    )
                }
                checkbox.prop('checked', false)

                // Xóa phần tử fixGrid6 khỏi giao diện
                $(`.boxWrapper .${classBox}`).remove()

            } else {
                // Nếu checkbox chưa được chọn, thêm vào mảng objectChoose và chọn checkbox
                objectChoose.push(objectItem)

                checkbox.prop('checked', true)
            }
        })
    }


    HT.changePromotionMethod = () => {
        $(document).on('change', '.select-product-and-quantity', function () {
            // Chỉ xóa các phần tử có class .fixGrid6 nhưng không có class .boxSearchInput
            if ($('.boxSearchIcon').length == 0) {
                $('.boxWrapper').prepend(HT.boxSearchIcon())
            }
            $('.fixGrid6').not('.boxSearchInput').remove()
            objectChoose = []
        })
    }

    HT.paginationLinks = (links) => {
        let nav = $('<nav>')
        if (links.length > 3) {
            let paginationUl = $('<ul>').addClass('pagination')
            $.each(links, function (index, link) {
                let liClass = 'page-item'
                if (link.active) {
                    liClass += ' active '
                } else if (!link.url) {
                    liClass += ' disabled '
                }
                let li = $('<li>').addClass(liClass)
                if (link.label == 'pagination.previous') {
                    let span = $('<span>').addClass('page-link').attr('aria-hidden', true).html('‹')
                    li.append(span)
                } else if (link.label == 'pagination.next') {
                    let span = $('<a>').addClass('page-link').attr('aria-hidden', true).html('›')
                    li.append(span)
                } else if (link.url) {
                    let a = $('<a>').addClass('page-link').text(link.label).attr('href', link.url)
                    li.append(a)
                }
                paginationUl.append(li)
            })
            nav.append(paginationUl)
        }
        return nav
    }

    HT.confirmProductPromotion = () => {
        let preloadObject = JSON.parse($('.input_object').val()) || {
            id: [],
            product_variant_id: [],
            name: [],
            type: [] // Thêm mảng type vào preloadObject
        }

        let objectArray = preloadObject.id.map((id, index) => ({
            id: id,
            product_variant_id: preloadObject.product_variant_id[index] || 'null',
            name: preloadObject.name[index],
            type: preloadObject.type
        }))
        // console.log(objectArray);

        if (objectArray.length && typeof objectArray !== 'undefined') {
            let preloadHtml = HT.renderBoxWrapper(objectArray)
            HT.checkFixGrid(preloadHtml)
        }

        $(document).on('click', '.confirm-product-promotion', function () {
            let html = HT.renderBoxWrapper(objectChoose)
            HT.checkFixGrid(html)
            $('#findProduct').modal('hide')
        })
    }

    HT.renderBoxWrapper = (objectData) => {
        let html = ''
        let model = $('.select-product-and-quantity').val()
        if (objectData.length) {
            for (let i = 0; i < objectData.length; i++) {
                let { id, product_variant_id, name, type } = objectData[i]
                if (type == 'Product') {
                    var classBox = `${model}_${id}_${product_variant_id}`
                } else {
                    var classBox = `${model}_${id}`
                }
                if (!$(`.boxWrapper .${classBox}`).length) {
                    html += `<div class="fixGrid6 ${classBox}">
                    <div class="goods-item">
                        <a class="goods-item-name" title="${name}">${name}</a>
                        <button class="delete-goods-item">
                            <svg viewBox="0 0 16 16" fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                                width="16" height="16">
                                <path d="M1 1L15 15" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M15 1L1 15" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                        <div class="hidden">
                            <input name="object[id][]" value="${id}">
                            <input name="object[product_variant_id][]" value="${product_variant_id}">
                            <input name="object[name][]" value="${name}">
                            <input name="object[type]" value="${type}">
                        </div>
                    </div>
                </div>`
                }
            }
        }
        return html
    }

    HT.checkFixGrid = (html) => {
        if ($('.fixGrid6').elExist) {
            $('.boxSearchIcon').remove()
            $('.boxWrapper').prepend(html)
        } else {
            $('.fixGrid6').remove()
            $('.boxWrapper').prepend(HT.boxSearchIcon())
        }
    }

    HT.boxSearchIcon = () => {
        return `<div class="boxSearchIcon">
                    <i class="fa fa-search"></i>
                </div>`
    }

    HT.deleteGoodItem = () => {
        $(document).on('click', '.delete-goods-item', function (e) {
            e.stopPropagation()
            let _button = $(this)
            let _parent = _button.parents('.fixGrid6')

            // Lấy thông tin sản phẩm cần xóa
            let product_id = _parent.find('input[name="object[id][]"]').val()
            let product_variant_id = _parent.find('input[name="object[product_variant_id][]"]').val()

            // Xóa phần tử trong mảng objectChoose
            objectChoose = objectChoose.filter(item => {
                return !(item.product_id === product_id && item.product_variant_id === product_variant_id)
            })

            // Xóa phần tử trong giao diện
            _parent.remove()
        })
    }

    HT.checkConditionItemSet = () => {
        let checkedValue = $('.conditionItemSelected').val()
        if (checkedValue.length && $('.conditionItem').length) {
            checkedValue = JSON.parse(checkedValue)
            $('.conditionItem').val(checkedValue).trigger('change')
        }
    }

    $(document).ready(function () {
        setTimeout(function () {
            HT.checkConditionItemSet();
        }, 100);
        HT.promotionNeverEnd()
        HT.promotionSource()
        HT.chooseCustomerCondition()
        HT.chooseApplyItem()
        HT.deleteCondition()
        HT.btnJs100()
        HT.promotionMultipleSelect2()
        HT.deleteAmountRangeCondition()
        HT.renderOrderRangeConditionContainer()
        HT.setupAjaxSearch()
        HT.productQuantityListProduct()
        HT.getPaginationMenu()
        HT.searchObject()
        HT.chooseProductPromotion()
        HT.confirmProductPromotion()
        HT.deleteGoodItem()
        HT.changePromotionMethod()
    });
})(jQuery);
