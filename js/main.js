$(document).ready(function () {
	$.fn.extend({
		toggleText: function (a, b) {
			return this.text(this.text() === b ? a : b)
		},
	})

	if (!localStorage.getItem('fb')) {
		$('.fb-container').addClass('fb-active')
		$('.nav-spacing').addClass('fb-active')
	}

	$('.fb-close').on('click', function (e) {
		e.preventDefault()
		localStorage.setItem('fb', 'disabled')
		$('.fb-container').removeClass('fb-active')
		$('.nav-spacing').removeClass('fb-active')
	})

	var nav = $('.nav')
	var navBtn = $('#menu-toggle')
	let mobileNav = $('#mobile-menu')
	var navIcon = $('.menu-icon')
	var navMenu = $('.nav-main-wrapper')
	var navTop = $('.nav-top')
	var navTopMenu = $('.nav-top-menu')
	var navMob = $('.nav-mob')
	var navMenuList = $('.nav-menu-list')
	var navMenuItem = $('.nav-menu-item')
	// var navLang = $('.nav-top');
	var navMenuItemHead = $('.nav-menu-item--head')
	var langMenu = $('.nav-lang')
	var langActiveEl = $('.nav-lang-item.active')
	var langDropdown = $('.nav-lang-dropdown')

	navBtn.on('click', function () {
		// navMob.toggle()
		mobileNav.toggleClass('open')
		navBtn.toggleClass('menu-toggle-active')
		// navLang.toggleClass('open')

		$('body').toggleClass('fixed')
	})

	navMenuItemHead.on('click', function () {
		$(this).siblings('.submenu').slideToggle(200)
	})

	langActiveEl.on('click', function (e) {
		e.stopPropagation()
		langDropdown.toggleClass('open')
	})

	$('body').on('click', function () {
		$(this).find('.icon').removeClass('open')
	})

	//open faq questions
	const openDropdownButtonsHome = document.querySelectorAll('#open-question-btn-home')
	const questionAnswersHome = document.querySelectorAll('#answer-home')

	openDropdownButtonsHome.forEach((btn, idx) => {
		btn.addEventListener('click', () => {
			questionAnswersHome[idx].classList.toggle('open')
			openDropdownButtonsHome[idx].classList.toggle('open')
		})
	})

	function validateInputs(form) {
		form.find('.input-required').each(function () {
			if (!$(this).val() && $(this).parent().is(':visible')) {
				$(this).parents('.input-required-wrapper').addClass('wrapper-error')
				$(this).addClass('input-error')
				$(this).siblings('.icon-error').show()
			} else {
				$(this).parents('.input-required-wrapper').removeClass('wrapper-error')
				$(this).removeClass('input-error')
				$(this).siblings('.icon-error').hide()
			}
		})

		if (form.find('.check-required').not(':checked').length > 0) {
			form.find('.check-required').not(':checked').parents('.checkbox-item').addClass('check-error')
		}

		form.find('.check-required').on('change', function () {
			$(this).parents('.checkbox-item').removeClass('check-error')
		})

		return form.find('.input-error').length <= 0 && form.find('.check-error').not(':checked').length <= 0
	}

	$('#send-contact').click(function () {
		var form = $(this).parents('form:first')

		if (validateInputs(form)) {
			$.ajax({
				type: 'POST',
				data: {
					form: form.serialize(),
				},
				url: BASE_URL + '/ax_contact',
				beforeSend: function () {},
				success: function (response) {
					var data = JSON.parse(response)

					if (data.status === 'success') {
						$('.form-error').hide()
						form.siblings('.form-success').show()
						form[0].reset()
					} else {
						form.siblings('.form-error').show()
					}
				},
			})
		} else {
			$('.form-error').show()
		}
		return false
	})

	$('#send-reservation').click(function () {
		var form = $(this).parents('form:first')

		if (validateInputs(form)) {
			$(this).addClass('disabled')

			$.ajax({
				type: 'POST',
				data: {
					form: form.serialize(),
				},
				url: BASE_URL + '/ax_reservation',
				beforeSend: function () {},
				success: function (response) {
					var data = JSON.parse(response)

					$(this).removeClass('disabled')

					if (data.status === 'success') {
						$('.form-error').hide()
						form.siblings('.form-success').show()
						form[0].reset()
						$('.hide-success').hide()
					} else {
						form.siblings('.form-error').show()
					}
				},
			})
		} else {
			$('.form-error').show()
		}
		return false
	})

	function rwdNavigation() {
		if (window.matchMedia('(max-width: 1260px)').matches) {
			$('.nav-menu-item .link-submenu .submenu-icon').on('click', function (e) {
				e.preventDefault()

				if ($(this).parent().siblings('.submenu').length > 0) {
					$(this).parent().siblings('.submenu').toggle()
					$(this).toggleClass('open')

					// $(this).attr('href', null)
				}
			})
		} else {
			navMenuItem.on('mouseover', function () {
				$(this).children('.submenu').show()
			})

			navMenuItem.on('mouseleave', function () {
				$(this).children('.submenu').hide()
			})

			$('.submenu-item').on('mouseover', function () {
				$(this).children('.submenu').show()
			})

			$('.submenu-item').on('mouseleave', function () {
				$(this).children('.submenu').hide()
			})

			navMenuList.attr('style', '')

			navBtn.removeClass('open')
			navMenu.removeClass('open')
			nav.removeClass('open')
		}
	}

	rwdNavigation()

	window.addEventListener(
		'resize',
		function () {
			rwdNavigation()
		},
		true
	)

	$('.faq-item-btn').on('click', function () {
		$(this).siblings('.text').slideToggle(200)
		$(this).toggleClass('open')
	})

	if (window.matchMedia('(max-width: 968px)').matches) {
		$('.products-sidebar-row.row-head').on('click', function () {
			$(this).siblings('.products-sidebar-sub').toggleClass('open')
			$(this).find('.products-sidebar-icon').toggleClass('open')
		})
	}

	// BLOG BOTTOM
	$('.blog-bottom-nav-item').on('click', function () {
		$('.blog-bottom-nav-item').removeClass('active')
		$(this).addClass('active')

		$('.blog-bottom-list').removeClass('active')
		$('.blog-bottom-list[data-type="' + $(this).data('target') + '"]').addClass('active')

		$('#blog-bottom-link').attr('href', $(this).data('url'))
	})

	// MODAL
	var modal = $('.modal')
	var modalBtn = $('.modal-open')

	$('.modal-close, .modal-bg, .modal-close-btn').on('click', function () {
		modal.removeClass('open')
	})

	modalBtn.on('click', function () {
		var modalName = $(this).attr('data-modal-name')
		$('[data-modal="' + modalName + '"]').addClass('open')

		if (modalName === 'modal-contact-form') {
			$('[data-modal="' + modalName + '"]')
				.find('[name="offer_category"]')
				.val(modalBtn.data('title'))
			$('[data-modal="' + modalName + '"]')
				.find('[name="position"]')
				.val(modalBtn.data('id'))
		}
	})

	$('.form-input, .form-textarea').on('input', function () {
		if ($(this).val().length > 0) {
			$(this).parent().addClass('focus')
		} else {
			$(this).parent().removeClass('focus')
		}
	})

	$('.form-input, .form-textarea').on('focus', function () {
		$(this).parent().addClass('focus')
	})

	$('.form-input, .form-textarea').on('focusout', function () {
		if ($(this).val().length === 0) {
			$(this).parent().removeClass('focus')
		}
	})

	// CLICK OUTSIDE SELECT
	$(document).mouseup(function (e) {
		var container = $('.form-group-select')

		if (!container.is(e.target) && container.has(e.target).length === 0) {
			container.find('.form-select').slideUp(100)
			container.find('.form-select-label').removeClass('open')
		}

		if (!langMenu.is(e.target) && langMenu.has(e.target).length === 0) {
			langDropdown.removeClass('open')
		}
	})

	$('.animate-scroll').each(function () {
		$(this).attr('data-top', $(this).offset().top)
	})

	window.onscroll = function () {
		scrollFunction()
	}

	function scrollFunction() {
		if (document.body.scrollTop > 1 || document.documentElement.scrollTop > 1) {
			// document.getElementById("nav").style.backgroundColor = "rgba(0, 0, 0, 0.6)";
			$('#nav').addClass('shrink')
			$('.nav-home #logo-white').hide()
			$('.nav-home #logo-black').show()
			$('.nav-home #search-icon-white').hide()
			$('.nav-home #search-icon-black').show()
		} else {
			// document.getElementById("nav").style.backgroundColor = "transparent";
			$('#nav').removeClass('shrink')
			$('.nav-home #logo-white').show()
			$('.nav-home #logo-black').hide()
			$('.nav-home #search-icon-white').show()
			$('.nav-home #search-icon-black').hide()
		}
	}

	$('.faq-item-more').on('click', function () {
		console.log($(this).siblings('.faq-item-text'))
		$(this).siblings('.faq-item-text').toggleClass('show')
		$(this).toggleClass('show')
		$(this).find('span').toggleText('Zwiń', 'Rozwiń')
	})

	$('.offer-table-block .head').on('click', function () {
		$(this).toggleClass('open')
		$(this).siblings('.items').toggleClass('open')
	})

	if (!Cookies.get('cookiePolicy')) {
		$('.cookies').show()
	}

	$('.cookies-accept').on('click', function () {
		Cookies.set('cookiePolicy', 1)
		$('.cookies').hide()
	})

	$('.cookies-close').on('click', function () {
		$('.cookies').hide()
	})

	$('.form-select-head').on('click', function () {
		$(this).parent().toggleClass('open')
	})

	$('.form-select-item').on('click', function () {
		let value = $(this).attr('data-value')
		let valueLabel = $(this).text()
		let head = $(this).parents('.form-select').find('.form-select-head')

		head.find('.form-select-placeholder').hide()
		head.find('.form-select-selected').show()
		head.find('.form-select-selected').html(valueLabel)

		$('#room').val(value)

		$(this).parents('.form-select').removeClass('open')
	})

	// const pickerConfig = {
	//   firstDay: 1,
	//   format: "D.MM",
	//   i18n: {
	//     previousMonth: "Poprzedni miesiąc",
	//     nextMonth: "Następny miesiąc",
	//     months: [
	//       "Styczeń",
	//       "Luty",
	//       "Marzec",
	//       "Kwiecień",
	//       "Maj",
	//       "Czerwiec",
	//       "Lipiec",
	//       "Sierpień",
	//       "Wrzesień",
	//       "Październik",
	//       "Listopad",
	//       "Grudzień",
	//     ],
	//     weekdays: [
	//       "Niedziela",
	//       "Poniedziałek",
	//       "Wtorek",
	//       "Środa",
	//       "Czwartek",
	//       "Piątek",
	//       "Sobota",
	//     ],
	//     weekdaysShort: ["Niedz", "Pon", "Wt", "Śr", "Czw", "Pt", "Sb"],
	//   },
	// };

	// var pickerDateFrom = new Pikaday({
	//   ...pickerConfig,
	//   field: document.getElementById("dateFrom"),
	//   minDate: new Date(),
	//   onSelect: function () {
	//     pickerDateTo.setMinDate(this.getDate());

	//     $('[name="dateFrom"]').val(this.getMoment().format("D.MM.YYYY"));
	//   },
	// });
	// var pickerDateTo = new Pikaday({
	//   ...pickerConfig,
	//   field: document.getElementById("dateTo"),
	//   onSelect: function () {
	//     pickerDateFrom.setMaxDate(this.getDate());

	//     $('[name="dateTo"]').val(this.getMoment().format("D.MM.YYYY"));
	//   },
	// });

	// pickerDateFrom.setDate(new Date());

	// $("#dateTo").attr(
	//   "placeholder",
	//   moment(new Date()).add(1, "days").format("D.MM")
	// );

	// $(".init-date-from").html(moment(new Date()).format("D.MM"));
	// $(".init-date-to").html(moment(new Date()).add(1, "days").format("D.MM"));
})

var lazyImages = [].slice.call(document.querySelectorAll('.lazy-load'))

if ('IntersectionObserver' in window) {
	let lazyImageObserver = new IntersectionObserver(function (entries, observer) {
		entries.forEach(function (entry) {
			if (entry.isIntersecting) {
				let lazyImage = entry.target
				if (lazyImage.dataset.src) lazyImage.src = lazyImage.dataset.src
				if (lazyImage.dataset.srcset) lazyImage.srcset = lazyImage.dataset.srcset
				lazyImage.classList.remove('lazy-load')
				lazyImageObserver.unobserve(lazyImage)
			}
		})
	})

	lazyImages.forEach(function (lazyImage) {
		lazyImageObserver.observe(lazyImage)
	})
} else {
}

document.addEventListener('DOMContentLoaded', function (event) {
	var scrollpos = localStorage.getItem('scrollpos')
	if (scrollpos) document.querySelector('body').scrollTo(0, scrollpos)
})

window.onbeforeunload = function (e) {
	localStorage.setItem('scrollpos', document.querySelector('body').scrollTop)
}

//====================== CONSTS ===============================
// MOBILE MENU
const mobileMenuBtnOpen = document.querySelector('.nav__mobile__open')
const mobileMenuBtnClose = document.querySelector('.nav__mobile__popup__close')
const mobileMenuPopup = document.querySelector('.nav__mobile__popup')
// FACEBOOK POPUP
// const facebookAdPopupCloseArrow = document.querySelector('.header__facebook-banner__like__text__close-icon')
// const facebookAdPopup = document.querySelector('.header__facebook-banner')
// SECTION KNOW MORE HINTS
const knowMoreHints = document.querySelectorAll('.know-more__text__table__element')
const knowMoreHintsArrow = document.querySelectorAll('.know-more__text__table__element__arrow')
// SECTION FAQ HINTS
const faqHints = document.querySelectorAll('.faq__table__element')
const faqhintsPlus = document.querySelectorAll('.faq__table__element__plus')

//====================== CONSTS ===============================

//====================== FUNCTIONS ===============================
//============= MOBILE MENU OPEN & CLOSE ====================
const mobileMenuOpen = () => {
	mobileMenuPopup.classList.add('showMenuPopup')
}
const mobileMenuClose = () => {
	mobileMenuPopup.classList.remove('showMenuPopup')
}
mobileMenuBtnOpen.addEventListener('click', mobileMenuOpen)
mobileMenuBtnClose.addEventListener('click', mobileMenuClose)
//============= MOBILE MENU OPEN & CLOSE ====================

//============= SECTION FACEBOOK POPUP CLOSE ====================
// const facebookAdPopupCloseFunc = () => {
// 	facebookAdPopup.classList.add('hide')
// }
// facebookAdPopupCloseArrow.addEventListener('click', facebookAdPopupCloseFunc)
//============= SECTION FASCEBOOK POPUP CLOSE ====================

//============= SECTION KNOW MORE HINTS OPEN & CLOSE ====================
knowMoreHints.forEach(element => {
	const knowMoreOpenHint = () => {
		element.children[1].classList.toggle('show')
		element.children[0].children[1].classList.toggle('spin90')
	}
	element.addEventListener('click', knowMoreOpenHint)
})
//============= SECTION KNOW MORE HINTS OPEN & CLOSE ====================

//============= SECTION FAQ HINTS OPEN & CLOSE ====================
faqHints.forEach(element => {
	const faqOpenHint = () => {
		element.children[1].classList.toggle('show')
		element.children[0].children[1].classList.toggle('spin45')
	}
	element.addEventListener('click', faqOpenHint)
})
//============= SECTION FAQ HINTS OPEN & CLOSE ====================
$("#contact_form button[type='submit']").on('click', function (e) {
	var form = $(this).closest('form')
	var seccessModal = alert('Formularz został wysłany!')
	sendForm('POST', form, '/ax_contact', e, successModal, false)
})
$("contact--page__form button[type='submit']").on('click', function (e) {
	var form = $(this).closest('form')
	var seccessModal = alert('Formularz został wysłany!')
	sendForm('POST', form, '/ax_contact', e, successModal, false)
})
//============= CONTACT FORM TEST====================
