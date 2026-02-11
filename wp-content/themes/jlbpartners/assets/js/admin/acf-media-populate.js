/**
 * ACF Media Auto-Populate
 * Automatically populate accessibility fields from WordPress media library metadata
 *
 * @package JLBPartners
 */

(function($) {
	'use strict';

	/**
	 * Initialize media auto-population
	 */
	function initMediaAutoPopulate() {
		if (typeof acf === 'undefined') {
			return;
		}

		// Listen for image field changes
		acf.addAction('select_attachment', function(attachment, field) {
			handleImageSelection(attachment, field);
		});

		// Also handle file (video) selections
		acf.addAction('select_attachment', function(attachment, field) {
			handleVideoSelection(attachment, field);
		});
	}

	/**
	 * Handle image selection and auto-populate accessibility fields
	 */
	function handleImageSelection(attachment, field) {
		const fieldName = field.get('name');
		
		// Check if this is a background image field
		if (fieldName === 'background_image') {
			console.log('Background image selected:', attachment);
			
			// Get the form/repeater context
			const $field = field.$el;
			const $form = $field.closest('.acf-fields');
			
			// Auto-populate ALT text
			if (attachment.alt) {
				const altField = acf.getField('field_hero_background_image_alt');
				if (altField) {
					altField.val(attachment.alt);
				}
			}
			
			// Auto-populate Caption
			if (attachment.caption) {
				const captionField = acf.getField('field_hero_background_image_caption');
				if (captionField) {
					captionField.val(attachment.caption);
				}
			}
			
			// Auto-populate Description
			if (attachment.description) {
				const descField = acf.getField('field_hero_background_image_description');
				if (descField) {
					descField.val(attachment.description);
				}
			}
			
			console.log('Auto-populated image accessibility fields');
		}
		
		// Check if this is a video poster image
		if (fieldName === 'video_poster') {
			console.log('Video poster selected:', attachment);
			
			// Auto-populate poster ALT text
			if (attachment.alt) {
				const posterAltField = acf.getField('field_hero_video_poster_alt');
				if (posterAltField) {
					posterAltField.val(attachment.alt);
				}
			}
			
			console.log('Auto-populated poster ALT text');
		}
	}

	/**
	 * Handle video selection and auto-populate accessibility fields
	 */
	function handleVideoSelection(attachment, field) {
		const fieldName = field.get('name');
		
		// Check if this is a background video field
		if (fieldName === 'background_video') {
			console.log('Background video selected:', attachment);
			
			// Auto-populate Video Title
			if (attachment.title) {
				const titleField = acf.getField('field_hero_video_title');
				if (titleField) {
					titleField.val(attachment.title);
				}
			}
			
			// Auto-populate Video Description
			if (attachment.description) {
				const descField = acf.getField('field_hero_video_description');
				if (descField) {
					descField.val(attachment.description);
				}
			}
			
			// Auto-populate Caption if available
			if (attachment.caption) {
				const captionField = acf.getField('field_hero_video_caption');
				if (captionField) {
					captionField.val(attachment.caption);
				}
			}
			
			console.log('Auto-populated video accessibility fields');
		}
	}

	/**
	 * Initialize on document ready and ACF ready
	 */
	if (typeof acf !== 'undefined') {
		acf.addAction('ready', function() {
			initMediaAutoPopulate();
		});
	}

	// Fallback for older ACF versions
	$(document).ready(function() {
		if (typeof acf !== 'undefined') {
			initMediaAutoPopulate();
		}
	});

})(jQuery);

