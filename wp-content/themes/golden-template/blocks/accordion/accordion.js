/**
 * Accordion Block JavaScript
 *
 * @package JLBPartners
 */
(function($) {
	'use strict';

	/**
	 * Initialize accordion functionality
	 */
	function initAccordion() {
		$(".js-accordion-btn").click(function () {
			const $button = $(this);
			const $item = $button.parent();
			const $content = $button.next(".js-accordion-content");
			const isExpanded = $button.attr("aria-expanded") === "true";

			// Toggle current item
			if (isExpanded) {
				$item.removeClass("is-active");
				$button.attr("aria-expanded", "false");
				$content.attr("hidden", "hidden").slideUp();
			} else {
				// Close all other items (accordion behavior)
				$item.siblings()
					.removeClass("is-active")
					.find(".js-accordion-btn")
					.attr("aria-expanded", "false")
					.next(".js-accordion-content")
					.attr("aria-hidden", "true")
					.slideUp();

				// Open current item
				$item.addClass("is-active");
				$button.attr("aria-expanded", "true");
				$content.removeAttr("hidden").slideDown();
			}
		});
	}

	// Initialize on DOM ready
	$(document).ready(function() {
		initAccordion();
	});
})(jQuery);
