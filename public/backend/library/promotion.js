(function ($) {
    "use strict";
    var HT = {};

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

                let sourceData = [
                    {
                        id: 1,
                        name: 'Tiktok'
                    },
                    {
                        id: 2,
                        name: 'Shoppe'
                    }
                ]
                let sourceHtml = HT.renderPromotionSource(sourceData).prop('outerHTML')
                console.log(sourceHtml);
                _this.parents('.ibox-content').append(sourceHtml)
                HT.promotionMultipleSelect2()
            }
        })
    }



    HT.renderPromotionSource = (sourceData) => {
        let wrapper = $('<div>').addClass('source-wrapper')
        if (sourceData.length) {
            let select = $('<select>')
                .addClass('multipleSelect2')
                .attr('name', 'source')
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
        let applyConditionData = [
            {
                id: 'staff_take_care_customer',
                name: 'Nhân viên phụ trách'
            },
            {
                id: 'customer_group',
                name: 'Nhóm khách hàng'
            },
            {
                id: 'customer_gender',
                name: 'Giới tính'
            },
            {
                id: 'customer_birthday',
                name: 'Ngày sinh'
            },
        ]
        let wrapper = $('<div>').addClass('apply-wrapper')
        let wrapperConditionItem = $('<div>').addClass('wrapper-condition')
        if (applyConditionData.length) {
            let select = $('<select>')
                .addClass('multipleSelect2 conditionItem')
                .attr('name', 'applyObject')
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
        let optionData = [
            {
                id: 1,
                name: 'Khách Vip'
            },
            {
                id: 2,
                name: 'Khách bán buôn'
            },
        ]
        let conditionItem = $('<div>').addClass('wrapperConditionItem mt10 ' + value)
        let select = $('<select>')
            .addClass('multipleSelect2 objectItem')
            .attr('name', 'customerGroup')
            .attr('multiple', true)
        for (let i = 0; i < optionData.length; i++) {
            let option = $('<option>').attr('value', optionData[i].id).text(optionData[i].name)
            select.append(option)
        }
        const conditionLabel = HT.createConditionLabel(label, value)
        conditionItem.append(conditionLabel)
        conditionItem.append(select)
        if ($('.wrapper-condition').find('.' + value).elExist()) {
            return;
        }
        $('.wrapper-condition').append(conditionItem)
        HT.promotionMultipleSelect2()

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

    HT.checkbtnJs100Condition = () => {
        let inputValue = $('.order_amount_range').find('tbody tr:last-child').find('.order_amount_range_to input').val().replace(/\./g, '')
        if (inputValue == 0) {
            alert('Bạn phải nhập vào giá trị đến');
            return false;
        }
        return inputValue

    }

    //Cấu hình HTML
    HT.btnJs100 = () => {
        $(document).on('click', '.btn-js-100', function () {
            let _button = $(this)

            let lastInputValue = HT.checkbtnJs100Condition()

            if (lastInputValue === false) {
                return
            }
            let r = (Math.random() + 1).toString(36).substring(7)
            let $tr = $('<tr>')
            let tdList = [
                { name: '', value: addCommas(lastInputValue), attribute: { 'readonly': true } },
                { name: '', value: 0, attribute: { 'readonly': false } },
            ]
            for (let i = 0; i < tdList.length; i++) {
                let $td = $('<td>')
                let $input = $('<input>').addClass('form-control int')
                    .attr('name', tdList[i].name)
                    .val(tdList[i].value)
                if (tdList[i].attribute.readonly != 'undefined' && tdList[i].attribute.readonly === true) {
                    $input.attr('readonly', true)
                }
                $td.append($input)
                $tr.append($td)
            }

            let discountTd = $('<td>').addClass('discountType')
            discountTd.append(
                $('<div>', { class: 'uk-flex uk-flex-middle' }).append(
                    $('<input>', {
                        type: 'text',
                        name: '',
                        class: 'form-control int',
                        placeholder: 0,
                        value: 0
                    })
                ).append(
                    $('<select>', {
                        class: 'multipleSelect2'
                    })
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


    $(document).ready(function () {
        HT.promotionNeverEnd()
        HT.promotionSource()
        HT.chooseCustomerCondition()
        HT.chooseApplyItem()
        HT.deleteCondition()
        HT.btnJs100()
        HT.promotionMultipleSelect2()
    });
})(jQuery);
