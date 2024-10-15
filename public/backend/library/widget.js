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
            let option = {
                model: _this.val(),
                keyword: $('.search-model').val()
            }
            HT.sendAjax(option)
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
                html += `<button class="ajax-search-item">
                        <div class="uk-flex uk-flex-middle uk-flex-space-between">
                            <span>${data[i].languages[0].pivot.name}</span>
                            <div class="auto-icon">
                                
                            </div>
                        </div>
                    </button>`
            }
        }
        return html
    }
    HT.autoIcon = () => {
        return `<svg class="svg-next-icon button-selected-combobox svg-next-icon-size-12"
                    width="12" height="12" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 26 26">
                    <path
                        d="m.3,14c-0.2-0.2-0.3-0.5-0.3-0.7s0.1-0.5 0.3-0.7l1.4-1.4c0.4-0.4 1-0.4 1.4,0l5.5,5.5c0.2,0.2 0.5,0.2 0.7,0l13.9-13.9h0.1v8.88178e-16c0.4-0.4 1-0.4 1.4,0l1.4,1.4c0.4,0.4 0.4,1 0,1.4l-16.6,16.6c-0.2,0.2-0.4,0.3-0.7,0.3s-0.5-0.1-0.7-0.3l-7.8-7.8c-0.2-0.2-0.4-0.5-0.4-0.7z">
                    </path>
                </svg>`
    }

    $(document).ready(function () {
        HT.searchModel()
        HT.chooseModel()
    });

})(jQuery);

