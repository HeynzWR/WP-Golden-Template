/**
 * Location Cards Block JavaScript
 * Handles accordion functionality for location cards on mobile
 *
 * @package JLBPartners
 */
(function($) {
	'use strict';

	/**
	 * Initialize on document ready
	 */
	$(document).ready(function() {
		locationCardsAccordion();
	});

	/**
	 * Location Cards Accordion Functionality
	 * Only works on screens below 1199px (mobile)
	 */
	function locationCardsAccordion() {
		// Remove any existing handlers to prevent duplicates
		$('.js-location-cards .js-location-header').off('click.locationCards');
		
		// Attach handler scoped to location-cards containers only
		$('.js-location-cards .js-location-header').on('click.locationCards', function(e) {
			// Stop event propagation to prevent other handlers from firing
			e.stopPropagation();
			
			// Disable accordion for screens above 1199px
			if (window.innerWidth > 1199) {
				return;
			}
			
			var $parent = $(this).closest('.js-location-block');
			var $content = $(this).next('.js-location-content');
			
			// If already active, don't do anything
			if ($parent.hasClass('is-active')) {
				return;
			}
			
			// Close all other cards within location-cards container
			$('.js-location-cards .js-location-block').not($parent).removeClass('is-active');
			$('.js-location-cards .js-location-content').not($content).stop(true, true).slideUp();
			
			// Open clicked card
			$parent.addClass('is-active');
			$content.stop(true, true).slideDown();
		});
	}

})(jQuery);
