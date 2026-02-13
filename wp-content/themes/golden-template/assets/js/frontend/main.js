/**
 * Golden Template Main Scripts
 *
 * @package GoldenTemplate
 */

(function($) {
	'use strict';

	/**
	 * Initialize on document ready
	 */
	$(document).ready(function() {
		// Initialize AOS
		AOS.init({
			duration: 800,
			once: true,
			disable: 'mobile',
		});

		// Smooth scroll for anchor links
		initSmoothScroll();
		
		// Lazy load images
		initLazyLoad();

		serviceSlider();

		milestonesSlider();
	});

	/**
	 * Smooth scroll for anchor links
	 */
	function initSmoothScroll() {
		$('a[href^="#"]').on('click', function(e) {
			// Skip smooth scroll for skip links (accessibility)
			if ($(this).hasClass('skip-link')) {
				return;
			}
			
			var target = $(this.getAttribute('href'));
			
			if (target.length) {
				e.preventDefault();
				$('html, body').stop().animate({
					scrollTop: target.offset().top - 80
				}, 600);
			}
		});
	}

	/**
	 * Lazy load images
	 */
	function initLazyLoad() {
		if ('loading' in HTMLImageElement.prototype) {
			// Browser supports native lazy loading
			const images = document.querySelectorAll('img[loading="lazy"]');
			images.forEach(img => {
				img.src = img.dataset.src || img.src;
			});
		}
	}

	function serviceSlider() {
		const $ThumbSlider = $('.js-service-image-slider');
		const $ContentSlider = $('.js-service-content-slider');
		const $ServiceWrap = $('.js-service-wrap');

		// Check if elements exist before initializing sliders
		if ($ThumbSlider.length === 0 || $ContentSlider.length === 0) return;

		$ThumbSlider.slick({
			slidesToShow: 1,
			slidesToScroll: 1,
			arrows: false,
			dots: false,
			fade: true,
			infinite: false,
			speed: 0,
			asNavFor: '.js-service-content-slider',
			responsive: [
				{
					breakpoint: 768,
					settings: {
						infinite: true,
						fade: false,
						speed: 600,
					}
				}
			]
		});
		$ContentSlider.slick({
			slidesToShow: 3,
			slidesToScroll: 3,
			asNavFor: '.js-service-image-slider',
			dots: false,
			focusOnSelect: true,
			vertical: true,
			infinite: false,
			arrows: false,
			speed: 0,
			responsive: [
				{
					breakpoint: 768,
					settings: {
						slidesToShow: 1,
						slidesToScroll: 1,
						speed: 600,
						infinite: true,
						vertical: false,
						autoplay: true,
						autoplaySpeed: 4500,
					}
				}
			]

		});
	}

	function milestonesSlider() {
		const $ThumbSlider = $('.js-milestones-data-slide');
		const $dateSlider = $('.js-milestones-date-slide');
		const $MilestonesWrapper = $('.js-milestones-wrapper');

		// Check if elements exist before initializing sliders
		if ($ThumbSlider.length === 0 || $dateSlider.length === 0) return;

		$ThumbSlider.slick({
			slidesToShow: 1,
			slidesToScroll: 1,
			arrows: false,
			dots: true,
			fade: true,
			infinite: false,
			asNavFor: '.js-milestones-date-slide',
			speed:0,
			responsive: [
				{
					breakpoint: 1199,
					settings: {
						slidesToShow: 2,
						infinite: true,
						autoplay: true,
						autoplaySpeed: 4500,
						fade: false,
						speed: 600,
					}
				},
				{
					breakpoint: 768,
					settings: {
						slidesToShow: 1,
						infinite: true,
						autoplay: true,
						autoplaySpeed: 4500,
						fade: false,
						speed: 600,
					}
				}
			]
		});
		// Calculate slidesToScroll based on total slides and screen width
		const totalSlides = $dateSlider.find('.slick-slide').length || $dateSlider.children().length;
		const slidesToScroll = window.innerWidth > 1199 
			? (totalSlides > 4 ? totalSlides - 1 : 4)
			: 1;
		
		$dateSlider.slick({
			slidesToShow: 4,
			slidesToScroll: slidesToScroll,
			asNavFor: '.js-milestones-data-slide',
			dots: false,
			vertical: true,
			infinite: false,
			arrows: false,
			speed: 0,
			responsive: [
				{
					breakpoint: 1199,
					settings: {
						slidesToShow: 2,
						slidesToScroll: 1,
						infinite: true,
						vertical: false,
						speed: 600,
					}
				},
				{
					breakpoint: 768,
					settings: {
						slidesToShow: 1,
						infinite: true,
						vertical: false,
						speed: 600,
					}
				}
			]

		});
	}

	function imageMask() {
		// Image mask functionality removed (was GSAP-dependent)
		// Can be reimplemented with CSS animations if needed
	}

})(jQuery);
