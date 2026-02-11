/**
 * Gallery Lightbox with Captions
 * Displays images in a lightbox with captions when clicked
 */

(function ($) {
    'use strict';

    // Initialize gallery lightbox on document ready
    $(document).ready(function () {
        initGalleryLightbox();
    });

    // Also initialize after Barba.js page transitions
    if (typeof barba !== 'undefined') {
        barba.hooks.afterEnter(({ next }) => {
            initGalleryLightbox();
        });
    }

    function initGalleryLightbox() {
        // Remove existing lightbox if it exists
        $('.gallery-lightbox').remove();

        // Create lightbox HTML
        const lightboxHTML = `
			<div class="gallery-lightbox" style="display: none;">
				<div class="gallery-lightbox__overlay"></div>
				<div class="gallery-lightbox__content">
					<button class="gallery-lightbox__close" aria-label="Close lightbox">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</button>
					<button class="gallery-lightbox__prev" aria-label="Previous image">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</button>
					<button class="gallery-lightbox__next" aria-label="Next image">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</button>
					<div class="gallery-lightbox__image-container">
						<img class="gallery-lightbox__image" src="" alt="">
						<div class="gallery-lightbox__caption"></div>
						<div class="gallery-lightbox__counter"></div>
					</div>
				</div>
			</div>
		`;

        // Append lightbox to body
        $('body').append(lightboxHTML);

        const $lightbox = $('.gallery-lightbox');
        const $lightboxImage = $('.gallery-lightbox__image');
        const $lightboxCaption = $('.gallery-lightbox__caption');
        const $lightboxCounter = $('.gallery-lightbox__counter');
        const $closeBtn = $('.gallery-lightbox__close');
        const $prevBtn = $('.gallery-lightbox__prev');
        const $nextBtn = $('.gallery-lightbox__next');
        const $overlay = $('.gallery-lightbox__overlay');

        let currentIndex = 0;
        let galleryImages = [];

        // Click handler for gallery images
        $(document).on('click', '.js-gallery-images', function (e) {
            e.preventDefault();

            // Get all gallery images
            galleryImages = $('.js-gallery-images').toArray();
            currentIndex = galleryImages.indexOf(this);

            // Show lightbox
            showLightbox(currentIndex);
        });

        // Close lightbox
        $closeBtn.on('click', closeLightbox);
        $overlay.on('click', closeLightbox);

        // Navigation
        $prevBtn.on('click', showPrevImage);
        $nextBtn.on('click', showNextImage);

        // Keyboard navigation
        $(document).on('keydown', function (e) {
            if (!$lightbox.is(':visible')) return;

            switch (e.key) {
                case 'Escape':
                    closeLightbox();
                    break;
                case 'ArrowLeft':
                    showPrevImage();
                    break;
                case 'ArrowRight':
                    showNextImage();
                    break;
            }
        });

        function showLightbox(index) {
            const $figure = $(galleryImages[index]);
            const $img = $figure.find('.image-gallery__image');
            const imageSrc = $img.attr('src');
            const imageAlt = $img.attr('alt');
            const caption = $figure.attr('data-caption') || '';

            // Set image
            $lightboxImage.attr('src', imageSrc);
            $lightboxImage.attr('alt', imageAlt);

            // Set caption
            if (caption) {
                $lightboxCaption.html(caption).show();
            } else {
                $lightboxCaption.hide();
            }

            // Set counter
            $lightboxCounter.text(`${index + 1} / ${galleryImages.length}`);

            // Show/hide navigation buttons
            $prevBtn.toggle(index > 0);
            $nextBtn.toggle(index < galleryImages.length - 1);

            // Show lightbox
            $lightbox.fadeIn(300);
            $('body').addClass('gallery-lightbox-open');
        }

        function closeLightbox() {
            $lightbox.fadeOut(300);
            $('body').removeClass('gallery-lightbox-open');
        }

        function showPrevImage() {
            if (currentIndex > 0) {
                currentIndex--;
                showLightbox(currentIndex);
            }
        }

        function showNextImage() {
            if (currentIndex < galleryImages.length - 1) {
                currentIndex++;
                showLightbox(currentIndex);
            }
        }
    }

})(jQuery);
