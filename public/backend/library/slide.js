(function ($) {
    "use strict";
    var HT = {};
    let _token = $('meta[name="csrf-token"]').attr('content');
    // Khai báo biến counter ở phạm vi toàn cục
    var counter

    // Khởi tạo counter dựa trên số lượng slide-item hiện có
    $(document).ready(function () {
        let existingSlides = $('.slide-item').length;
        counter = (existingSlides * 2) + 1;  // Mỗi slide sử dụng 2 tab, do đó nhân số lượng slide hiện có với 2
    });

    HT.addSlide = (type) => {
        $(document).on('click', '.addSlide', function (e) {
            e.preventDefault()
            if (typeof (type) == 'undefined') {
                type = 'Images';
            }
            var finder = new CKFinder();
            finder.resourceType = type;
            finder.selectActionFunction = function (fileUrl, data, allFiles) {
                let html = ''
                for (var i = 0; i < allFiles.length; i++) {
                    let image = allFiles[i].url
                    html += HT.renderSlideItemHtml(image)
                }
                $('.slide-list').append(html)
                HT.checkSlideNotification()
            };
            finder.popup();
        })

    }

    HT.replaceImage = (type) => {
        $(document).on('click', '.choose-image-text', function (e) {
            e.preventDefault();

            let currentImageElement = $(this).closest('.slide-image').find('img');

            if (typeof (type) == 'undefined') {
                type = 'Images';
            }

            var finder = new CKFinder();
            finder.resourceType = type;

            // Khi người dùng chọn ảnh mới
            finder.selectActionFunction = function (fileUrl, data) {
                currentImageElement.attr('src', fileUrl);

                currentImageElement.closest('.slide-image').find('input[name="slide[image][]"]').val(fileUrl);
            };

            finder.popup();
        });
    }

    //Kiem tra xem co anh hay khong
    HT.checkSlideNotification = () => {
        let slideItem = $('.slide-item')
        if (slideItem.length) {
            $('.slide-notification').hide()
        } else {
            $('.slide-notification').show()
        }
    }

    let i = 0;
    HT.renderSlideItemHtml = (image) => {
        let tab_1 = "tab-" + counter
        let tab_2 = "tab-" + (counter + 1)

        let html = `
        <div class="col-lg-12 ui-state-default">
    <div class="slide-item mb20">
        <div class="row custom-row">
            <div class="col-lg-3">
                <span class="slide-image img-cover"><img
                    src="${image}"
                    alt="">
                    <input type="hidden" name="slide[image][]" value="${image}">
                    <button class="delete-image"><i class="fa fa-trash"></i></button>
                    <button class="choose-image-text">Đổi ảnh</button>
                    </span>
                </div>
                    <div class="col-lg-9">
                        <div class="tabs-container">
                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#${tab_1}"> Thông
                                    tin
                                    chung</a></li>
                                <li class=""><a data-toggle="tab" href="#${tab_2}">SEO</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div id="${tab_1}" class="tab-pane active">
                                    <div class="panel-body">
                                        <div class="label-text mb5">Mô tả</div>
                                        <div class="form-row mb10">
                                            <textarea name="slide[description][]" class="form-control"></textarea>
                                        </div>
                                        <div class="form-row form-row-url">
                                            <input type="text" name="slide[canonical][]" class="form-control" placeholder="URL">
                                                <div class="overlay">
                                                    <div class="uk-flex uk-flex-middle">
                                                        <label for="input_${tab_1}">Mở trong tab
                                                            mới</label>
                                                        <input type="checkbox" name="slide[window][${i}]" value="_blank" id="input_${tab_1}">
                                                    </div>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="${tab_2}" class="tab-pane">
                                    <div class="panel-body">
                                        <div class="label-text mb5">Tiêu đề ảnh</div>
                                        <div class="form-row form-row-url slide-seo-tab">
                                            <input type="text" name="slide[name][]" class="form-control"
                                                placeholder="Tiêu đề ảnh...">
                                        </div>
                                        <div class="label-text mb5 mt12">Mô tả ảnh</div>
                                        <div class="form-row form-row-url slide-seo-tab">
                                            <input type="text" name="slide[alt][]" class="form-control"
                                                placeholder="Mô tả ảnh...">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
        <hr>
    </div>
        `
        counter += 2;
        i++;
        return html
    }

    HT.switchImages = () => {
        $(".sortable").sortable({
            update: function (event, ui) {
                // Lấy ID của slide từ phần tử cha
                let slideId = $(this).find('.ui-state-default').data('slide-id');

                // Khởi tạo đối tượng để chứa dữ liệu
                let slideData = {
                    image: [],
                    description: [],
                    canonical: [],
                    name: [],
                    alt: [],
                    window: []
                };

                // Lặp qua tất cả các hình ảnh để thu thập dữ liệu
                $(this).find('.ui-state-default').each(function () {
                    slideData.image.push($(this).find('input[name="slide[image][]"]').val());
                    slideData.description.push($(this).find('input[name="slide[description][]"]').val());
                    slideData.canonical.push($(this).find('input[name="slide[canonical][]"]').val());
                    slideData.name.push($(this).find('input[name="slide[name][]"]').val());
                    slideData.alt.push($(this).find('input[name="slide[alt][]"]').val());
                    slideData.window.push($(this).find('input[name="slide[window][]"]').val());
                });

                // Tạo đối tượng option để gửi
                let option = {
                    'id': slideId,
                    'slide': slideData,
                    '_token': _token
                };
                $.ajax({
                    url: 'ajax/slide/updateImage', // Đường dẫn API server xử lý việc cập nhật thứ tự
                    method: 'POST',
                    data: option,
                    success: function (response) {
                        console.log('Thứ tự ảnh đã được cập nhật thành công', response);
                    },
                    error: function (error) {
                        console.log('Có lỗi khi cập nhật thứ tự ảnh', error);
                    }
                });
            }
        });
        $(".sortable").disableSelection();
    }



    HT.deleteSlide = () => {
        $(document).on('click', '.delete-image', function () {
            let _this = $(this)
            _this.parents('.ui-state-default').remove()
            HT.checkSlideNotification()
        })
    }


    $(document).ready(function () {
        HT.addSlide()
        HT.deleteSlide()
        HT.replaceImage()
        HT.switchImages()
    });

})(jQuery);

