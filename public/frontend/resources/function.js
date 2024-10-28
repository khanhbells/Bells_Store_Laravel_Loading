(function ($) {
	"use strict";
	var HT = {}; // Khai báo là 1 đối tượng
	var timer;

	HT.swiperOption = (setting) => {
		// console.log(setting);
		let option = {}
		if (setting.animation.length) {
			option.effect = setting.animation;
		}
		if (setting.arrow === 'accept') {
			option.navigation = {
				nextEl: '.swiper-button-next',
				prevEl: '.swiper-button-prev',
			}
		}
		if (setting.autoplay === 'accept') {
			option.autoplay = {
				delay: 2000,
				disableOnInteraction: false,
			}
		}
		if (setting.navigate === 'dots') {
			option.pagination = {
				el: '.swiper-pagination',
			}
		}
		return option
	}

	/* MAIN VARIABLE */
	HT.swiper = () => {
		if ($('.panel-slide').length) {
			let setting = JSON.parse($('.panel-slide').attr('data-setting'))
			let option = HT.swiperOption(setting)
			var swiper = new Swiper(".panel-slide .swiper-container", option);
		}

	}

	HT.swiperCategory = () => {
		var swiper = new Swiper(".panel-category .swiper-container", {
			loop: false,
			pagination: {
				el: '.swiper-pagination',
			},
			spaceBetween: 20,
			slidesPerView: 3,
			breakpoints: {
				415: {
					slidesPerView: 3,
				},
				500: {
					slidesPerView: 3,
				},
				768: {
					slidesPerView: 6,
				},
				1280: {
					slidesPerView: 10,
				}
			},
			navigation: {
				nextEl: '.swiper-button-next',
				prevEl: '.swiper-button-prev',
			},

		});
	}

	HT.swiperBestSeller = () => {
		var swiper = new Swiper(".panel-bestseller .swiper-container", {
			loop: false,
			pagination: {
				el: '.swiper-pagination',
			},
			spaceBetween: 20,
			slidesPerView: 2,
			breakpoints: {
				415: {
					slidesPerView: 1,
				},
				500: {
					slidesPerView: 2,
				},
				768: {
					slidesPerView: 3,
				},
				1280: {
					slidesPerView: 4,
				}
			},
			navigation: {
				nextEl: '.swiper-button-next',
				prevEl: '.swiper-button-prev',
			},

		});
	}




	HT.wow = () => {
		var wow = new WOW(
			{
				boxClass: 'wow',      // animated element css class (default is wow)
				animateClass: 'animated', // animation css class (default is animated)
				offset: 0,          // distance to the element when triggering the animation (default is 0)
				mobile: true,       // trigger animations on mobile devices (default is true)
				live: true,       // act on asynchronously loaded content (default is true)
				callback: function (box) {
					// the callback is fired every time an animation is started
					// the argument that is passed in is the DOM node being animated
				},
				scrollContainer: null,    // optional scroll container selector, otherwise use window,
				resetAnimation: true,     // reset animation on end (default is true)
			}
		);
		wow.init();


	}// arrow function

	HT.countdown = () => {

	}



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

	HT.search = () => {
		$(document).on('keyup', '.keyword', function (e) {
			e.preventDefault()
			let _this = $(this)
			let keyword = _this.val().trim()

			if (keyword.length > 2) {
				let doneTypingTimer = 1000;
				clearTimeout(timer)
				timer = setTimeout(function () {
					HT.sendDataToSearch(keyword)
				}, doneTypingTimer)
			} else {
				$('.ajax-search-result').html(''); // Xóa kết quả tìm kiếm
				return false;
			}
		})
	}

	HT.sendDataToSearch = (keyword) => {
		$.ajax({
			url: 'ajax/product/loadProductPromotion',
			type: 'GET',
			data: {
				model: 'Product',
				keyword: keyword
			},
			dataType: 'json',
			success: function (res) {
				HT.renderProductSearch(res.objects);
			},
		});
	}

	HT.renderProductSearch = (object) => {
		let html = ''
		if (object.data.length) {
			let model = $('.select-product-and-quantity').val()
			for (let i = 0; i < object.data.length; i++) {
				let image = object.data[i].image
				let name = object.data[i].variant_name
				let product_variant_id = object.data[i].product_variant_id
				let product_id = object.data[i].id
				let sku = object.data[i].sku
				let price = object.data[i].price
				let uuid = object.data[i].uuid
				let canonical = object.data[i].canonical

				html += `<div class="search-object-item" data-uuid="${uuid}" data-productid="${product_id}"
							data-variant_id="${product_variant_id ? product_variant_id : ''}"
							data-name="${name}" data-type="Product">
							<div class="uk-flex uk-flex-middle uk-flex-space-between">
								<div class="object-info">
									<a href="${canonical}">
									<div class="uk-flex uk-flex-middle">
										<span class="image img-scaledown">
											<img src="` + baseUrl + `${image[0]}" alt="">
										</span>
										<div class="object-name">
											<div class="name">${name}</div>
											<div class="jscode">Mã SP: ${sku}</div>
										</div>
									</div>
									</a>
								</div>
								<div class="object-extra-info">
									<div class="price">${addCommas(price)}</div>
								</div>
							</div>
						</div>`
			}
		}
		html = html + HT.paginationLinks(object.links).prop('outerHTML')
		$('.ajax-search-result').html(html)
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

	HT.popupSwiperSlide = () => {

	}

	HT.niceSelect = () => {
		if ($('.nice-select').length) {
			$('.nice-select').niceSelect();
		}

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


	$(document).ready(function () {
		HT.wow()
		HT.changeQuantity()
		HT.mySwiper()
		HT.swiperCategory()
		HT.swiperBestSeller()

		/* CORE JS */
		HT.swiper()
		HT.niceSelect()
		HT.search()
		HT.countdown()
		HT.popupSwiperSlide()
		HT.selectVariantProduct()
		HT.loadProductVariant()
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