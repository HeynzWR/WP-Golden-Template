/**
 * ACF Blocks Enhancement
 *
 * Auto-populate media fields from WordPress media library
 * Provides better UX for managing block images and videos
 *
 * @package GoldenTemplate
 */

(function($) {
	'use strict';

	/**
	 * Initialize ACF block enhancements
	 */
	function initACFBlockEnhancements() {
		console.log('Golden Template: ACF Block Enhancements initialized');

		// Monitor image field changes
		acf.addAction('load_field/type=image', initImageFieldAutoPopulation);
		acf.addAction('change_field/type=image', handleImageFieldChange);
		
		// Monitor file (video) field changes
		acf.addAction('load_field/type=file', initFileFieldAutoPopulation);
		acf.addAction('change_field/type=file', handleFileFieldChange);
	}

	/**
	 * Initialize image field auto-population on load
	 */
	function initImageFieldAutoPopulation(field) {
		// Only auto-populate fields with the special class
		var $field = field.$el;
		var $parentFields = $field.closest('.acf-fields');
		
		// Check if this is a background image or video poster field
		var fieldName = field.get('name');
		if (fieldName === 'background_image' || fieldName === 'video_poster') {
			// If the image is already set, try to populate related fields
			var imageID = field.val();
			if (imageID) {
				populateImageMetadata(imageID, $parentFields);
			}
		}
	}

	/**
	 * Handle image field change
	 */
	function handleImageFieldChange(field) {
		var imageID = field.val();
		var fieldName = field.get('name');
		
		// Only handle background_image and video_poster
		if (fieldName !== 'background_image' && fieldName !== 'video_poster') {
			return;
		}

		if (imageID) {
			var $parentFields = field.$el.closest('.acf-fields');
			populateImageMetadata(imageID, $parentFields);
		}
	}

	/**
	 * Initialize file (video) field auto-population on load
	 */
	function initFileFieldAutoPopulation(field) {
		var fieldName = field.get('name');
		if (fieldName === 'background_video') {
			var fileID = field.val();
			if (fileID) {
				var $parentFields = field.$el.closest('.acf-fields');
				populateVideoMetadata(fileID, $parentFields);
			}
		}
	}

	/**
	 * Handle file (video) field change
	 */
	function handleFileFieldChange(field) {
		var fileID = field.val();
		var fieldName = field.get('name');
		
		if (fieldName !== 'background_video') {
			return;
		}

		if (fileID) {
			var $parentFields = field.$el.closest('.acf-fields');
			populateVideoMetadata(fileID, $parentFields);
		}
	}

	/**
	 * Populate image metadata fields from media library
	 */
	function populateImageMetadata(imageID, $container) {
		console.log('Auto-populating image metadata for ID:', imageID);

		// Use WordPress media library to get attachment data
		var attachment = wp.media.attachment(imageID);
		
		// Fetch full attachment details
		attachment.fetch().done(function() {
			var alt = attachment.get('alt') || '';
			var caption = attachment.get('caption') || '';
			var description = attachment.get('description') || '';
			var title = attachment.get('title') || '';

			// Find and populate ALT text field (background_image_alt or video_poster_alt)
			var $altField = $container.find('[data-name="background_image_alt"], [data-name="video_poster_alt"]').find('input[type="text"]');
			if ($altField.length && !$altField.val()) {
				$altField.val(alt || title).trigger('change');
				console.log('Populated ALT text:', alt || title);
			}

			// Find and populate caption field (background_image_caption)
			var $captionField = $container.find('[data-name="background_image_caption"]').find('input[type="text"]');
			if ($captionField.length && !$captionField.val()) {
				$captionField.val(caption).trigger('change');
				console.log('Populated caption:', caption);
			}

			// Find and populate description field (background_image_description)
			var $descriptionField = $container.find('[data-name="background_image_description"]').find('textarea');
			if ($descriptionField.length && !$descriptionField.val()) {
				$descriptionField.val(description).trigger('change');
				console.log('Populated description:', description);
			}

			// Show notification
			showNotification('📸 Image metadata auto-populated from media library', 'success');
		});
	}

	/**
	 * Populate video metadata fields from media library
	 */
	function populateVideoMetadata(videoID, $container) {
		console.log('Auto-populating video metadata for ID:', videoID);

		// Use WordPress media library to get attachment data
		var attachment = wp.media.attachment(videoID);
		
		// Fetch full attachment details
		attachment.fetch().done(function() {
			var title = attachment.get('title') || '';
			var description = attachment.get('description') || '';
			var caption = attachment.get('caption') || '';

			// Find and populate video title field
			var $titleField = $container.find('[data-name="video_title"]').find('input[type="text"]');
			if ($titleField.length && !$titleField.val()) {
				$titleField.val(title).trigger('change');
				console.log('Populated video title:', title);
			}

			// Find and populate video description field
			var $descriptionField = $container.find('[data-name="video_description"]').find('textarea');
			if ($descriptionField.length && !$descriptionField.val()) {
				$descriptionField.val(description || caption).trigger('change');
				console.log('Populated video description:', description || caption);
			}

			// Show notification
			showNotification('🎬 Video metadata auto-populated from media library', 'success');
		});
	}

	/**
	 * Show notification to user
	 */
	function showNotification(message, type) {
		type = type || 'info';
		
		// Create notification element safely
		var $notification = $('<div></div>')
			.addClass('golden_template-notification')
			.addClass('golden_template-notification--' + type)
			.text(message);
		
		// Append to body
		$('body').append($notification);
		
		// Fade in
		setTimeout(function() {
			$notification.addClass('golden_template-notification--visible');
		}, 10);
		
		// Fade out and remove after 3 seconds
		setTimeout(function() {
			$notification.removeClass('golden_template-notification--visible');
			setTimeout(function() {
				$notification.remove();
			}, 300);
		}, 3000);
	}

	/**
	 * Initialize when ACF is ready
	 */
	if (typeof acf !== 'undefined') {
		acf.addAction('ready', initACFBlockEnhancements);
	} else {
		console.warn('ACF not found - block enhancements disabled');
	}

})(jQuery);

