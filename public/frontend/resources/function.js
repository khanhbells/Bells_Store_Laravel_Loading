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

	HT.niceSelect = () => {
		if ($('.nice-select').length) {
			$('.nice-select').niceSelect();
		}

	}


	$(document).ready(function () {
		HT.wow()
		HT.swiperCategory()
		HT.swiperBestSeller()

		/* CORE JS */
		HT.swiper()
		HT.niceSelect()
		HT.search()
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