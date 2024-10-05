(function ($) {
    "use strict";
    var HT = {};

    HT.setupProductVariant = () => {
        if ($('.turnOnVariant').length) {
            $(document).on('click', '.turnOnVariant', function () {
                let _this = $(this)
                if (_this.siblings('input:checked').length == 0) {
                    $('.variant-wrapper').removeClass('hidden')
                } else {
                    $('.variant-wrapper').addClass('hidden')
                }
            })
        }
    }
    HT.addVariant = () => {
        if ($('.add-variant').length) {
            $(document).on('click', '.add-variant', function () {
                let html = HT.renderVariantItem(attributeCatalogue);
                $('.variant-body').append(html)
                HT.checkMaxAttributeGroup(attributeCatalogue);
                HT.disableAttributeCatalogueChoose();
            })
        }
    }
    HT.renderVariantItem = (attributeCatalogue) => {
        let html = '';
        html = html + '<div class="row mb20 variant-item">';
        html = html + '<div class="col-lg-3">';
        html = html + '<div class="attribute-catalogue">';
        html = html + '<select name="" id="" class="choose-attribute niceSelect">';
        html = html + '<option value="">Chọn nhóm thuộc tính</option>';
        for (let index = 0; index < attributeCatalogue.length; index++) {
            html = html + '<option value="' + attributeCatalogue[index].id + '">' + attributeCatalogue[index].name + '</option>';
        }
        html = html + '</select>';
        html = html + '</div>';
        html = html + '</div>';
        html = html + '<div class="col-lg-8">';
        html = html + '<input type="text" name="" disabled class="fake-variant form-control">';
        html = html + '</div>';
        html = html + '<div class="col-lg-1">';
        html = html + '<button type="button" class="remove-attribute btn btn-danger"><i class="fa fa-trash"></i></button>';
        html = html + '</div>';
        html = html + '</div>';
        return html;
    }
    HT.chooseVariantGroup = () => {
        $(document).on('change', '.choose-attribute', function () {
            let _this = $(this)
            let attributeCatalogueId = _this.val()
            if (attributeCatalogueId != 0) {
                _this.parents('.col-lg-3').siblings('.col-lg-8').html(HT.select2Variant(attributeCatalogueId))
                $('.selectVariant').each(function (key, index) {
                    HT.getSelect2($(this))
                })
            } else {
                _this.parents('.col-lg-3').siblings('.col-lg-8').html('<input type="text" name="" disabled="" class="fake-variant form-control">')
            }
            HT.disableAttributeCatalogueChoose();
        })
    }
    HT.getSelect2 = (object) => {
        let option = {
            'attributeCatalogueId': object.attr('data-catid')
        }
        $(object).select2({
            minimumInputLength: 2,
            placeholder: 'Nhập tối thiểu 2 kí tự để tìm kiếm',
            ajax: {
                url: 'ajax/attribute/getAttribute',
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
    }
    HT.niceSelect = () => {
        $('.niceSelect').niceSelect();
    }
    HT.destroyNiceSelect = () => {
        if ($('.niceSelect').length) {
            $('.niceSelect').niceSelect('destroy')
        }
    }
    HT.disableAttributeCatalogueChoose = () => {
        let id = [];
        $('.choose-attribute').each(function () {
            let _this = $(this)
            let selected = _this.find('option:selected').val()
            if (selected != 0) {
                id.push(selected)
            }
        })
        $('.choose-attribute').find('option').removeAttr('disabled')
        for (let i = 0; i < id.length; i++) {
            $('.choose-attribute').find('option[value=' + id[i] + ']').prop('disabled', true)
        }
        HT.destroyNiceSelect()
        HT.niceSelect()
        $('.choose-attribute').find('option:selected').removeAttr('disabled')
    }
    HT.checkMaxAttributeGroup = (attributeCatalogue) => {
        console.log(attributeCatalogue)
        let variantItem = $('.variant-item').length
        if (variantItem >= attributeCatalogue.length) {
            $('.add-variant').remove()
        } else {
            $('.variant-foot').html('<button type="button" class="add-variant">Thêm phiên bản mới</button>')
        }
    }
    HT.removeAttribute = () => {
        $(document).on('click', '.remove-attribute', function () {
            let _this = $(this)
            _this.parents('.variant-item').remove()
            HT.checkMaxAttributeGroup(attributeCatalogue)
            HT.disableAttributeCatalogueChoose();
        })
    }
    HT.select2Variant = (attributeCatalogueId) => {
        let html = '<select class="selectVariant form-control" name="attribute[' + attributeCatalogueId + '][]" multiple data-catid="' + attributeCatalogueId + '"></select>'
        return html
    }
    $(document).ready(function () {
        // HT.setupProductVariant();
        HT.addVariant();
        HT.niceSelect();
        HT.chooseVariantGroup();
        HT.removeAttribute();
    });

})(jQuery);