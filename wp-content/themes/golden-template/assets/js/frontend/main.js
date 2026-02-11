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

		// Hero scroll sticky (wait for GSAP and ScrollTrigger to load)
		waitForGSAPAndInit();

		serviceSlider();

		milestonesSlider();

		imageMask();

		const initialContainer = document.querySelector('[data-barba="container"]');
		if (initialContainer) {
			previewExpandAnimation(initialContainer);
			initHeroPreviewTextScroll(initialContainer);
			initStickyHeader(initialContainer);
			initGalleryParallax(initialContainer);
		}

		function previewExpandAnimation(container) {
			const previewHero = container.querySelector('.js-hero-preview');
			if (!previewHero) return;

			const previewHeroText = previewHero.querySelector('.js-hero-preview-text');
			const previewOverlay = previewHero.querySelectorAll('.js-hero-preview-overlay');
			const previewOverlayDefault = previewHero.querySelector('.js-hero-preview-overlay-default');

			if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;
		  		  
			gsap.to(previewHero, {
			  transform: "scaleX(1)",
			  ease: "none",
			  scrollTrigger: {
				trigger: previewHero,
				start: 'top 75%',
				end: 'top top',
				scrub: true,
				scroller: container, // ✅ VERY IMPORTANT
			  }
			});

			if (previewHeroText) {
				gsap.to(previewHeroText, {
					opacity: 0,
					scrollTrigger: {
					  trigger: previewHero,
					  start: 'top 25%',
					  end: 'top top',
					  scrub: true,
					}
				});
			}

			if (previewOverlay && previewOverlay.length > 0) {
				gsap.to(previewOverlay, {
					'--alpha': 1,
					scrollTrigger: {
					  trigger: previewHero,
					  start: 'top 75%',
					  end: 'top top',
					  scrub: true,
					}
				});
			}

			if (previewOverlayDefault) {
				gsap.to(previewOverlayDefault, {
					opacity: 0,
					scrollTrigger: {
					  trigger: previewHero,
					  start: 'top 75%',
					  end: 'top top',
					  scrub: true,
					}
				});
			}
		}

		function pageLeaveAnimation(current) {
			const tl = gsap.timeline();
		
			tl.to(current.container, {
				opacity: 0,
				duration: 0.3,
			});
		
			return tl;
		}
		
		function pageEnterAnimation(next) {
			const heroText = next.container.querySelector('.hero--detail article');
			const tl = gsap.timeline();
		
			// Initial state
			tl.set(next.container, {
				y: 0,
				opacity: 1,
			});
		
			if (heroText) {
				tl.set(heroText, {
					opacity: 0,
					y: 10,
				});
			}
		
			// Page fade in
			tl.to(next.container, {
				opacity: 1,
				duration: 0.1,
			});
		
			// Hero text animation
			if (heroText) {
				tl.to(
					heroText,
					{
						opacity: 1,
						y: 0,
						duration: 0.6,
						ease: 'power2.out',
					},
					'+=0.2'
				);
			}
		
			return tl;
		}

		function delay(n) {
			n = n || 2000;
		
			return new Promise(done => {
				setTimeout(() => {
					done();
				}, n);
			})
		};
		



		// Initialize Barba.js only if it's loaded
		if (typeof barba !== 'undefined') {
			barba.init({
				prevent: ({ el }) => {
					const container = document.querySelector('[data-barba="container"]');
					return container && !container.contains(el);
				},
				transitions: [
					{
						name: 'slide-up',
						sync: true,
			
						async leave({ current }) {
							var done = this.async()
							pageLeaveAnimation(current);
							await delay(-1);
							done()
						},
			
						async enter({ next }) {
							pageEnterAnimation(next);
						},
						async once({next}) {
							pageEnterAnimation(next);
						}
					},
				],
			});
			
			
			barba.hooks.beforeLeave(({ current }) => {
				current.container.classList.add('is-transitioning');
			});

			barba.hooks.afterEnter(({ next }) => {
				next.container.classList.remove('is-transitioning');
				// reset scroll INSIDE the container
				next.container.scrollTop = 0;
			  
				// kill old triggers
				if (typeof ScrollTrigger !== 'undefined') {
					ScrollTrigger.getAll().forEach(st => st.kill());
				  
					// set correct scroller
					ScrollTrigger.defaults({
					  scroller: next.container
					});
				  
					// refresh AFTER layout is stable
					ScrollTrigger.refresh(true);
				}
			  
				previewExpandAnimation(next.container);
				initNextProjectTrigger(next.container);
				initHeroPreviewTextScroll(next.container);
				initStickyHeader(next.container);
				initGalleryParallax(next.container);
			});
		}

		function initNextProjectTrigger(container) {
			if (typeof ScrollTrigger === 'undefined') return;

			// ScrollTrigger.getAll().forEach(st => st.kill());

			const hero = container.querySelector('.js-hero-preview');
			const triggerLink = container.querySelector('.js-next-project-link');

		  
			if (!hero || !triggerLink) return;
		  
			const nextUrl = triggerLink.getAttribute('href');
			if (!nextUrl) return;

			let triggered = false;
		  
			ScrollTrigger.create({
			  trigger: hero,
			  start: 'bottom bottom+=1px', // 👈 key line
			  scroller: container, // ✅ Explicitly set the scroller
			  onEnter: () => {
				if (triggered) return;
				triggered = true;
				if (typeof barba !== 'undefined') {
					barba.go(nextUrl);
				}
			  }
			});
		}

		function initHeroPreviewTextScroll(container) {
			const heroPreviewText = container.querySelector('.js-hero-preview-text');
			
			if (!heroPreviewText) return;
			if (typeof gsap === 'undefined') return;

			// Add click handler to scroll container to bottom smoothly
			heroPreviewText.addEventListener('click', function(e) {
				// Prevent default behavior if it's a link or button
				e.preventDefault();
				e.stopPropagation();
				
				// Scroll container to bottom smoothly using GSAP
				if (container) {
					gsap.to(container, {
						scrollTop: container.scrollHeight - container.clientHeight + 1,
						duration: 0.6, // 2000ms = 2 seconds
						ease: 'power2.inOut'
					});
				}
			});
		}

	function initStickyHeader(container) {
		// Only initialize for single-project pages
		const isSingleProject = container.classList.contains('single-project') || container.querySelector('.js-detail-hero');
		if (!isSingleProject) return;
		if (typeof ScrollTrigger === 'undefined') return;

		const header = document.querySelector('.js-header');
		if (!header) return;

		const detailHero = container.querySelector('.js-detail-hero');
		if (!detailHero) return;

		// Create ScrollTrigger to add/remove is-sticky class
		ScrollTrigger.create({
			trigger: detailHero,
			start: '20px top',
			scroller: container,
			onEnter: () => {
				header.classList.add('is-sticky');
			},
			onLeaveBack: () => {
				header.classList.remove('is-sticky');
			}
		});
	}

	function initGalleryParallax(container) {
		// Only initialize for single-project pages
		const isSingleProject = container.classList.contains('single-project') || container.querySelector('.js-detail-hero');
		if (!isSingleProject) return;
		if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;

		const imageGallery = container.querySelector('.js-image-gallery');
		if (!imageGallery) return;

		const galleryImages = imageGallery.querySelectorAll('.js-gallery-images');
		if (!galleryImages || galleryImages.length === 0) return;

		// Set initial state for each figure
		gsap.set(galleryImages, {
			y: 40,
			alpha: 0
		});

		// Create ScrollTrigger animation for each figure
		galleryImages.forEach((figure) => {
			gsap.to(figure, {
				y: 0,
				alpha: 1,
				duration: 0.6,
				ease: 'none',
				scrollTrigger: {
					trigger: figure,
					start: 'top 90%',
					end: 'bottom top',
					scroller: container
				}
			});
		});
	}
	});

	/**
	 * Wait for GSAP and ScrollTrigger to load before initializing animations
	 */
	function waitForGSAPAndInit(attempts = 0) {
		const maxAttempts = 50; // Maximum 5 seconds (50 * 100ms)
		
		if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
			// Both are loaded, register and initialize
			gsap.registerPlugin(ScrollTrigger);
			heroScrollAnimation();
		} else if (attempts < maxAttempts) {
			// Wait a bit and try again
			setTimeout(function() {
				waitForGSAPAndInit(attempts + 1);
			}, 100);
		} else {
			console.warn('GSAP or ScrollTrigger failed to load after waiting');
		}
	}

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
	/**
	 * Hero scroll sticky
	 */
	function heroScrollAnimation() {
		if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;

		const lineBlock = document.querySelector('.js-scroll-block');

		const hero = document.querySelector('.js-hero');

		
		if (!lineBlock) {
			return;
		}

		// Add/remove class on js-hero when js-scroll-block touches top of screen
		if (hero) {
			// GSAP animations only on screens above 1199px
		gsap.matchMedia().add("(min-width: 1199px)", () => {
			const scrollTrigger = ScrollTrigger.create({
				trigger: lineBlock,
				start: 'top top',
				onEnter: () => {
					hero.classList.add('is-hidden');
				},
				onLeaveBack: () => {
					hero.classList.remove('is-hidden');
				},
			});

			// Check initial state in case page loads with element already at trigger point
			if (scrollTrigger.isActive) {
				hero.classList.add('is-scroll-block-touching');
			}

		});
		}

		// GSAP animations only on screens above 1199px
		gsap.matchMedia().add("(min-width: 1199px)", () => {
			// Create timeline with ScrollTrigger
			const tl = gsap.timeline({
				scrollTrigger: {
					trigger: lineBlock,
					start: 'top bottom',
					end: 'top 75%', // End when center of element hits center of viewport
					scrub: true, // Smooth scrubbing based on scroll position
				}
			});

			// Animate from initial state to final state
			tl.to(lineBlock, {
				transform: "scaleX(1)",
				ease: "none",
			});
		});
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
		// Create the ScrollTrigger only for screens >= 1199px using gsap.matchMedia
		if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined' && $ServiceWrap.length > 0) {
			gsap.matchMedia().add("(min-width: 768px)", () => {
				ScrollTrigger.create({
					trigger: ".js-service-wrap",
					start: "top top",
					end: "+=100%", // Adjust this value to control scroll duration
					pin: true,
					scrub: true,
					onUpdate: (self) => {
						// Calculate which slide should be active based on scroll progress
						const slickInstance = $ThumbSlider.slick('getSlick');
						if (slickInstance) {
							const slideCount = slickInstance.slideCount;
							const progress = self.progress; 
							const targetSlide = Math.floor(progress * (slideCount));

							// Tell slick to go to that slide
							$ThumbSlider.slick('slickGoTo', targetSlide);
						}
					}
				});
			});
		}
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
		// Create the ScrollTrigger only for screens >= 1199px using gsap.matchMedia
		if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined' && $MilestonesWrapper.length > 0) {
			gsap.matchMedia().add("(min-width: 1199px)", () => {
				ScrollTrigger.create({
					trigger: ".js-milestones-wrapper",
					start: "top top",
					end: "+=100%", // Adjust this value to control scroll duration
					pin: true,
					scrub: true,
					pinSpacing: false,
					onUpdate: (self) => {
						// Calculate which slide should be active based on scroll progress
						const slickInstance = $ThumbSlider.slick('getSlick');
						if (slickInstance) {
							const slideCount = slickInstance.slideCount;
							const progress = self.progress; 
							const targetSlide = Math.floor(progress * (slideCount));

							// Tell slick to go to that slide
							$ThumbSlider.slick('slickGoTo', targetSlide);
						}
					}
				});
			});
		}
	}

	function imageMask() {
		const $imageMaskBlock = $('.js-image-mask-block');
		const $imageMask = $imageMaskBlock.find('.js-image-mask');

		// Check if elements exist before initializing animation
		if ($imageMaskBlock.length === 0 || $imageMask.length === 0) return;
		if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;

		gsap.matchMedia().add("(min-width: 768px)", () => {
			// Create timeline with ScrollTrigger
			const maskTl = gsap.timeline({
				scrollTrigger: {
					trigger: $imageMaskBlock[0],
					start: "top 65%",   // when block enters viewport
					end: "bottom top",  // adjust for animation length
					scrub: true,
				}
			});

			// Add animation to timeline
			maskTl.from($imageMask, {
				clipPath: "inset(150px 350px)",
				ease: "power2.out"
			});
		});
	}

})(jQuery);
