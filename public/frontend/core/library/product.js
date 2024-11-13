(function ($) {
    "use strict";
    var HT = {}; // Khai báo là 1 đối tượng
    var timer;

    HT.changeQuantity = () => {
        $(document).on('click', '.quantity-button', function () {
            let _this = $(this);
            let quantity = $('.quantity-text').val();
            let newQuantity = 0;

            if (_this.hasClass('minus')) {
                newQuantity = quantity - 1;
            } else {
                newQuantity = parseInt(quantity) + 1;
            }

            if (newQuantity < 1) {
                newQuantity = 1;
            }

            $('.quantity-text').val(newQuantity);
        });
    }

    HT.mySwiper = () => {
        var swiper = new Swiper(".mySwiper", {
            spaceBetween: 10,
            slidesPerView: 4,
            freeMode: true,
            watchSlidesProgress: true,
        });
        var swiper2 = new Swiper(".mySwiper2", {
            spaceBetween: 10,
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            thumbs: {
                swiper: swiper,
            },
        });
    }
    HT.selectVariantProduct = () => {
        if ($('.choose-attribute').length) {
            $(document).on('click', '.choose-attribute', function (e) {
                e.preventDefault()
                let _this = $(this)
                let attribute_id = _this.attr('data-attributeid')
                let attribute_name = _this.text()
                _this.parents('.attribute-item').find('span').html(attribute_name)
                _this.parents('.attribute-value').find('.choose-attribute').removeClass('active')
                _this.addClass('active')
                HT.handleAttribute()
            })
        }

    }

    HT.handleAttribute = () => {
        let attribute_id = []
        let flag = true;
        $('.attribute-value .choose-attribute').each(function () {
            let _this = $(this)
            if (_this.hasClass('active')) {
                attribute_id.push(_this.attr('data-attributeid'))
            }
        })
        $('.attribute').each(function () {
            if ($(this).find('.choose-attribute.active').length === 0) {
                flag = false
                return false
            }
        })

        if (flag) {
            $.ajax({
                url: 'ajax/product/loadVariant',
                type: 'GET',
                data: {
                    'attribute_id': attribute_id,
                    'product_id': $('input[name=product_id]').val(),
                    'language_id': $('input[name=language_id]').val(),
                },
                dataType: 'json',
                beforeSend: function () {

                },
                success: function (res) {
                    HT.setVariantPrice(res)
                    HT.setupVariantGallery(res)
                    HT.setupVariantName(res)
                    HT.setupVariantUrl(res, attribute_id)
                },
            });
        }
    }

    HT.setupVariantUrl = (res, attribute_id) => {
        let queryString = '?attribute_id=' + attribute_id.join(',')
        let productCanonical = $('.productCanonical').val()
        productCanonical = productCanonical + queryString;
        let stateObject = { attribute_id: attribute_id };
        history.pushState(stateObject, "Page Title", productCanonical);


    }

    HT.setVariantPrice = (res) => {
        $('.popup-product .price').html(res.variantPrice.html);
    }

    HT.setupVariantName = (res) => {
        let productName = $('.productName').val()
        let productVariantName = productName + ' ' + res.variant.languages[0].pivot.name
        $('.product-main-title span').html(productVariantName)
    }

    HT.setupVariantGallery = (gallery) => {
        let album = gallery.variant.album.split(',')
        let html = `<div class="swiper-container">
		<div style="--swiper-navigation-color: #fff; --swiper-pagination-color: #fff"
			class="swiper mySwiper2">
			<div class="swiper-button-next"></div>
			<div class="swiper-button-prev"></div>
			<div class="swiper-wrapper">`;

        album.forEach((val) => {
            html += `<div class="swiper-slide" data-swiper-autoplay="2000">
						<a href="${assetBaseUrl + val}" class="image img-cover"><img src="${assetBaseUrl + val}" alt="${assetBaseUrl + val}"></a>
					</div>`;
        });
        html += `
			</div>
				<div class="swiper-pagination"></div>
				</div>
			</div>
			<br>
			<div class="swiper-container-thumbs">
				<div thumbsSlider="" class="swiper mySwiper">
				<div class="swiper-wrapper">`;

        album.forEach((val) => {
            html += `<div class="swiper-slide">
						<span class="image img-cover"><img src="${assetBaseUrl + val}" alt="${assetBaseUrl + val}"></span>
					</div>`;
        });
        html += `</div>
				</div>
			</div>`;
        if (album.length && album != 0) {
            $('.popup-gallery').html(html);
            HT.mySwiper();
        }
    };

    HT.loadProductVariant = () => {
        let attributeCatalogue = JSON.parse($('.attributeCatalogue').val());
        if (typeof attributeCatalogue != 'undefined' && attributeCatalogue.length) {
            HT.handleAttribute();
        }

    }

    HT.chooseReviewStar = () => {
        $(document).on('mouseOver click', '.popup-rating label', function () {
            let _this = $(this)
            let title = _this.attr('title')
            $('.rate-text').removeClass('hidden').html(title)

        })
    }


    $(document).ready(function () {
        /* CORE JS */
        HT.changeQuantity()
        HT.mySwiper()
        HT.selectVariantProduct()
        HT.loadProductVariant()
        HT.chooseReviewStar()
    });

})(jQuery);



addCommas = (nStr) => {
    nStr = String(nStr);
    nStr = nStr.replace(/\./gi, "");
    let str = '';
    for (let i = nStr.length; i > 0; i -= 3) {
        let a = ((i - 3) < 0) ? 0 : (i - 3);
        str = nStr.slice(a, i) + '.' + str;
    }
    str = str.slice(0, str.length - 1);
    return str;
}