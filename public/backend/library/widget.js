(function ($) {
    "use strict";
    var HT = {};
    var _token = $('meta[name="csrf-token"]').attr('content');
    var typingTimer;
    var doneTyingInterval = 300;

    HT.searchModel = () => {
        $(document).on('keyup', '.search-model', function (e) {
            e.preventDefault()
            let _this = $(this)
            if ($('input[type=radio]:checked').length === 0) {
                alert('Bạn chưa chọn Module');
                _this.val('')
                return false;
            }

            let keyword = _this.val().trim(); // Loại bỏ khoảng trắng ở đầu và cuối

            // Kiểm tra nếu input trống hoặc chỉ chứa khoảng trắng
            if (keyword === '') {
                $('.ajax-search-result').html('').hide(); // Xóa kết quả tìm kiếm
                return false; // Không tiếp tục gửi yêu cầu Ajax
            }

            let option = {
                model: $('input[type=radio]:checked').val(),
                keyword: keyword
            }
            HT.sendAjax(option);
        })
    }

    HT.chooseModel = () => {
        $(document).on('change', '.input-radio', function () {
            let _this = $(this)
            let keyword = $('.search-model').val().trim();
            let option = {
                model: _this.val(),
                keyword: keyword
            }
            $('.search-model-result').html('')
            if (keyword.length >= 2) {
                HT.sendAjax(option)
            }
        })
    }

    HT.sendAjax = (option) => {
        clearTimeout(typingTimer)
        typingTimer = setTimeout(function () {
            $.ajax({
                url: 'ajax/dashboard/findModelObject',
                type: 'GET',
                data: option,
                dataType: 'json',
                success: function (res) {
                    let html = HT.renderSearchResult(res)
                    if (html.length) {
                        $('.ajax-search-result').html(html).show()
                    } else {
                        $('.ajax-search-result').html(html).hide()
                    }
                },
                beforeSend: function () {
                    $('.ajax-search-result').html('').hide()
                },
            });
        }, doneTyingInterval)
    }

    HT.renderSearchResult = (data) => {
        let html = ''
        if (data.length) {
            for (let i = 0; i < data.length; i++) {

                let flag = ($('#model-' + data[i].id).length) ? 1 : 0
                let setChecked = ($('#model-' + data[i].id).length) ? HT.setChecked() : ''
                html += `<button 
                            class="ajax-search-item" 
                            data-canonical="${data[i].languages[0].pivot.canonical}" 
                            data-flag="${flag}" 
                            data-image="${data[i].image}" 
                            data-name="${data[i].languages[0].pivot.name}" 
                            data-id="${data[i].id}"
                        >
                        <div class="uk-flex uk-flex-middle uk-flex-space-between">
                            <span>${data[i].languages[0].pivot.name}</span>
                            <div class="auto-icon">
                                ${setChecked}
                            </div>
                        </div>
                    </button>`
            }
        }
        return html
    }
    HT.setChecked = () => {
        return `<svg class="svg-next-icon button-selected-combobox svg-next-icon-size-12"
                    width="12" height="12" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 26 26">
                    <path
                        d="m.3,14c-0.2-0.2-0.3-0.5-0.3-0.7s0.1-0.5 0.3-0.7l1.4-1.4c0.4-0.4 1-0.4 1.4,0l5.5,5.5c0.2,0.2 0.5,0.2 0.7,0l13.9-13.9h0.1v8.88178e-16c0.4-0.4 1-0.4 1.4,0l1.4,1.4c0.4,0.4 0.4,1 0,1.4l-16.6,16.6c-0.2,0.2-0.4,0.3-0.7,0.3s-0.5-0.1-0.7-0.3l-7.8-7.8c-0.2-0.2-0.4-0.5-0.4-0.7z">
                    </path>
                </svg>`
    }

    HT.unfocusSearchBox = () => {
        $(document).on('click', 'html', function (e) {
            if (!$(e.target).hasClass('ajax-search-result') || !$(e.target).hasClass('search-model')) {
                $('.ajax-search-result').html('')
            }
        })
        $(document).on('click', '.ajax-search-result', function (e) {
            e.stopPropagation();
        })
    }

    HT.addModel = () => {
        $(document).on('click', '.ajax-search-item', function (e) {
            e.preventDefault()
            let _this = $(this)
            let data = _this.data()
            let html = HT.modelTemplate(data);
            let flag = _this.attr('data-flag')
            if (flag == 0) {
                _this.find('.auto-icon').html(HT.setChecked())
                _this.attr('data-flag', 1)
                $('.search-model-result').append(HT.modelTemplate(data))
            } else {
                $('#model-' + data.id).remove()
                _this.find('.auto-icon').html('')
                _this.attr('data-flag', 0)
            }


        })
    }

    HT.modelTemplate = (data) => {
        let html = `<div class="search-result-item" id="model-${data.id}" data-modelid="${data.id}">
                        <div class="uk-flex uk-flex-middle uk-flex-space-between">
                            <div class="uk-flex uk-flex-middle">
                                <span class="image img-cover"><img
                                        src="` + baseUrl + `${data.image}"
                                        alt=""></span>
                                <span class="name">${data.name}</span>
                                <div class="hidden">
                                    <input type="text" name="modelItem[id][]" value="${data.id}">
                                    <input type="text" name="modelItem[name][]" value="${data.name}">
                                    <input type="text" name="modelItem[image][]" value="${data.image}">
                                </div>
                            </div>
                            <div class="deleted">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24"
                                    height="24">
                                    <path fill="none" d="M0 0h24v24H0z" />
                                    <path
                                        d="M18.3 5.71a1 1 0 00-1.42 0L12 10.59 7.12 5.7a1 1 0 00-1.42 1.42l4.88 4.88-4.88 4.88a1 1 0 001.42 1.42L12 13.41l4.88 4.88a1 1 0 001.42-1.42l-4.88-4.88 4.88-4.88a1 1 0 000-1.42z" />
                                </svg>
                            </div>
                        </div>
                    </div>`
        return html
    }

    HT.deleteModel = () => {
        $(document).on('click', '.deleted', function (e) {
            e.preventDefault();

            // Xóa phần tử cha (.search-result-item)
            let parentItem = $(this).closest('.search-result-item');
            let modelId = parentItem.data('modelid');

            // Xóa phần tử khỏi danh sách hiển thị kết quả tìm kiếm
            parentItem.remove();

            // Reset trạng thái của item trong danh sách tìm kiếm (ajax-search-item)
            let searchItem = $(`.ajax-search-item[data-id="${modelId}"]`);
            searchItem.attr('data-flag', 0); // Reset cờ flag về 0
            searchItem.find('.auto-icon').html(''); // Xóa icon đã chọn
        });
    }

    $(document).ready(function () {
        HT.searchModel()
        HT.chooseModel()
        HT.unfocusSearchBox()
        HT.addModel()
        HT.deleteModel()
    });

})(jQuery);

