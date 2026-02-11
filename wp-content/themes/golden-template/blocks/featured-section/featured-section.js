// Featured Section - Accordion & Carousel
(function($) {
    'use strict';

    // Elements
    const cards = $('.featured-projects__card');
    const buttons = $('.featured-projects__btn');

    // Desktop Check
    function isDesktop() {
        return window.innerWidth >= 1199;
    }

    // Initialize Accordion (Desktop Only - Click Based)
    function initAccordion() {
        buttons.off('click').on('click', function () {
            if (!isDesktop()) return;

            const card = $(this).closest('.featured-projects__card');
            const index = cards.index(card);

            cards.removeClass('active');
            cards.eq(index).addClass('active');
        });
    }

    // Initialize Slick Carousel (Mobile Only)
    function initSlickCarousel() {
        if ($(".js-featured-panel").length > 0) {
            if(!isDesktop()) {
                // Mobile: Initialize or reinitialize slider
                destroySlickSlider(".js-featured-panel");
        
                $('.js-featured-panel').slick({
                    mobileFirst: true,
                    infinite: true,
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    dots: true,
                    arrows: false,
                    autoplay: true,
                    autoplaySpeed: 4500,
                    responsive: [
                        {
                            breakpoint: 720,
                            settings: {
                                slidesToShow: 2,
                            }
                        },
                        {
                            breakpoint: 1200,
                            settings: "unslick"
                        },

                    ]
                });
            } else {
                // Desktop: Destroy slider if it exists
                destroySlickSlider(".js-featured-panel");
            }
        }
    }

    function removeSlickClasses(slider) {
        $(slider)
          .removeClass("slick-initialized slick-slider")
          .find(".slick-list, .slick-track, .slick-slide")
          .remove();
    }

    // Destroy existing Slick slider if it exists
    function destroySlickSlider(slider) {
        if ($(slider).hasClass("slick-initialized")) {
            $(slider).slick("unslick");
        }
        removeSlickClasses(slider);
    }

    $(document).ready(function() {
        // Run Accordion
        initAccordion();
        initSlickCarousel();
        featuredPanelSize();
    });

    let windowWidth = window.innerWidth;
    let isDesktopMode = isDesktop();
    
    // Handle window resize
    $(window).on('resize', function() {
        if(windowWidth !== window.innerWidth) {
            const wasDesktop = isDesktopMode;
            windowWidth = window.innerWidth;
            isDesktopMode = isDesktop();
            
            // Reinitialize slider if switching between mobile/desktop modes
            if(wasDesktop !== isDesktopMode) {
                initSlickCarousel();
            }
        }
        featuredPanelSize();
    });
    
    // Handle orientation change (important for mobile devices)
    $(window).on('orientationchange', function() {
        // Small delay to ensure window dimensions are updated
        setTimeout(function() {
            const wasDesktop = isDesktopMode;
            windowWidth = window.innerWidth;
            isDesktopMode = isDesktop();
            
            // Reinitialize slider if switching between mobile/desktop modes
            if(wasDesktop !== isDesktopMode) {
                initSlickCarousel();
            }
            featuredPanelSize();
        }, 100);
    });

    function featuredPanelSize() {
		// Disable script below 1024px
		if (window.innerWidth < 1024) {
			return;
		}
		
		const featuredPanels = document.querySelector('.js-featured-panel');
		
		if (!featuredPanels) {
			return;
		}
		
		const featuredPanelCards = featuredPanels.querySelectorAll('.js-featured-panel-card');
		const featuredPanelArticles = featuredPanels.querySelectorAll('.js-featured-panel-article');
		const featuredPanelFigures = featuredPanels.querySelectorAll('.js-featured-panel-figure');
		
		// Calculate width of wrapper js-featured-panel
		const featuredPanelsWidth = featuredPanels.offsetWidth;
		
		// Calculate total number of items js-featured-panel-card
		const panelCount = featuredPanelCards.length;
		
		// Calculate width of panel header js-featured-panel-article (using first article if available)
		const articleCount = featuredPanelArticles.length;
		const panelHeaderWidth = articleCount > 0 ? featuredPanelArticles[0].offsetWidth : 0;
		
		// Calculate --width: (width of js-featured-panel) - (width of js-featured-panel-article * its length)
		const calculatedWidth = featuredPanelsWidth - (panelHeaderWidth * articleCount);
		
		// Update --width property in js-featured-panel-figure
		featuredPanelFigures.forEach(figure => {
			figure.style.setProperty('--width', `${calculatedWidth}px`);
		});		
	}

})(jQuery);
