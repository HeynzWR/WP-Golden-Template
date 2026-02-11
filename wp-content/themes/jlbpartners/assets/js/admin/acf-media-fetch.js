/**
 * ACF Media Metadata Fetcher - Handles Regular Fields & Repeaters
 * Works automatically for standard naming patterns, with fallback mappings
 *
 * @package JLBPartners
 */

(function($) {
	'use strict';

	// Developer mode
	const DEV_MODE = true;
	const log = {
		info: (...args) => DEV_MODE && console.log('ℹ️', ...args),
		success: (...args) => DEV_MODE && console.log('✅', ...args),
		error: (...args) => DEV_MODE && console.error('❌', ...args),
	};

	log.info('ACF Media Fetch: Loading...');

	/**
	 * Initialize fetch buttons
	 */
	function initFetchButtons() {
		if (typeof acf === 'undefined') {
			log.error('ACF not loaded');
			return;
		}

		log.success('ACF loaded, initializing fetch buttons...');

		// Wait for ACF fields to be ready
		acf.addAction('ready', function() {
			setTimeout(addAllFetchButtons, 500);
		});

		// Also add buttons when new blocks are appended
		acf.addAction('append', function($el) {
			setTimeout(() => addAllFetchButtons($el), 300);
		});
	}

	/**
	 * Add all fetch buttons
	 */
	function addAllFetchButtons($scope) {
		log.info('Adding fetch buttons...', $scope ? 'scoped' : 'global');

		let count = 0;
		const fields = $scope ? acf.getFields({parent: $scope}) : acf.getFields();

		fields.forEach(field => {
			const fieldType = field.get('type');
			const fieldName = field.get('name');
			
			// Only process text/textarea fields
			if (fieldType !== 'text' && fieldType !== 'textarea') {
				return;
			}

		// Skip if button already exists
		const $fieldWrap = field.$el;
		if ($fieldWrap.find('.jlbpartners-fetch-btn').length) {
			return;
		}

			// Check if this looks like an accessibility field
			const metadataKey = getMetadataKeyFromFieldName(fieldName);
			if (!metadataKey) {
				return;
			}

			// Find the source media field in the same context
			const sourceFieldName = findSourceMediaField(field, fieldName, metadataKey);
			if (!sourceFieldName) {
				return;
			}

			// Add the fetch button
			if (addFetchButtonToField(field, sourceFieldName, metadataKey)) {
				count++;
			}
		});

		log.success(`Added ${count} fetch buttons`);
	}

	/**
	 * Detect metadata key from field name
	 */
	function getMetadataKeyFromFieldName(fieldName) {
		if (fieldName.endsWith('_alt')) return 'alt';
		if (fieldName.endsWith('_caption')) return 'caption';
		if (fieldName.endsWith('_description')) return 'description';
		if ((fieldName.endsWith('_title') || fieldName === 'video_title') && fieldName !== 'hero_title' && fieldName !== 'banner_title') return 'title';
		return null;
	}

	/**
	 * Find source media field for an accessibility field
	 */
	function findSourceMediaField(targetField, targetFieldName, metadataKey) {
		// Get the parent row/block context
		const $parent = targetField.$el.closest('.acf-row, .acf-block-fields');
		
		// Determine potential source field names based on metadata type
		let potentialSources = [];
		if (metadataKey === 'alt' || metadataKey === 'caption') {
			potentialSources = ['image', 'background_image', 'img', 'photo', 'desktop_image', 'mobile_image'];
		} else if (metadataKey === 'title') {
			potentialSources = ['video', 'background_video', 'video_file', 'desktop_video', 'mobile_video'];
		} else if (metadataKey === 'description') {
			// Description could be for image or video - check field name
			if (targetFieldName.includes('image')) {
				potentialSources = ['image', 'background_image', 'img', 'photo', 'desktop_image', 'mobile_image'];
			} else if (targetFieldName.includes('video')) {
				potentialSources = ['video', 'background_video', 'video_file', 'desktop_video', 'mobile_video'];
			} else {
				// Try both
				potentialSources = ['image', 'background_image', 'video', 'background_video', 'desktop_video', 'mobile_video'];
			}
		}

		// Look for media fields in the same context (row or block)
		const fieldsInContext = $parent.length ? acf.getFields({parent: $parent}) : acf.getFields();
		
		for (let i = 0; i < fieldsInContext.length; i++) {
			const field = fieldsInContext[i];
			const fieldName = field.get('name');
			const fieldType = field.get('type');
			
			// Must be an image or file field
			if (fieldType !== 'image' && fieldType !== 'file') {
				continue;
			}

			// Check if it matches one of our potential sources
			if (potentialSources.includes(fieldName)) {
				log.info(`Found source "${fieldName}" for "${targetFieldName}" in same context`);
				return fieldName;
			}
		}

		return null;
	}

	/**
	 * Add fetch button to a specific field
	 */
	function addFetchButtonToField(targetField, sourceFieldName, metadataKey) {
		const $targetField = targetField.$el;
		const targetFieldName = targetField.get('name');
		
		// Don't add if button already exists
		if ($targetField.find('.jlbpartners-fetch-btn').length) {
			return false;
		}

		// Find source field in the same context
		const $parent = $targetField.closest('.acf-row, .acf-block-fields');
		const fieldsInContext = $parent.length ? acf.getFields({parent: $parent}) : acf.getFields();
		
		let sourceField = null;
		for (let i = 0; i < fieldsInContext.length; i++) {
			if (fieldsInContext[i].get('name') === sourceFieldName) {
				sourceField = fieldsInContext[i];
				break;
			}
		}

		if (!sourceField) {
			return false;
		}

		// Create fetch button
		const $button = $('<button>', {
			type: 'button',
			class: 'jlbpartners-fetch-btn',
			html: '<span class="dashicons dashicons-download"></span>',
			'data-source': sourceFieldName,
			'data-metadata': metadataKey,
			title: 'Fetch ' + formatMetadataKey(metadataKey) + ' from media library'
		});

		// Add click handler
		$button.on('click', function(e) {
			e.preventDefault();
			fetchAndShowMetadata(targetField, sourceField, metadataKey, $button);
		});

		// Insert button next to the field label
		const $label = $targetField.find('.acf-label label');
		if ($label.length) {
			$label.append($button);
			log.info(`Added fetch button: ${targetFieldName} ← ${sourceFieldName} (${metadataKey})`);
			return true;
		}

		return false;
	}

	/**
	 * Fetch metadata and show preview modal
	 */
	function fetchAndShowMetadata(targetField, sourceField, metadataKey, $button) {
		log.info(`Fetching ${metadataKey} from ${sourceField.get('name')}...`);

		const mediaId = sourceField.val();
		
		if (!mediaId) {
			showError('Please select an image or video first before fetching metadata.');
			return;
		}

		log.info('Media ID:', mediaId);

		// Show loading state
		$button.prop('disabled', true).html('<span class="dashicons dashicons-update dashicons-spin"></span>');

		// Fetch attachment data
		if (typeof wp === 'undefined' || !wp.media) {
			showError('WordPress media library not available');
			$button.prop('disabled', false).html('<span class="dashicons dashicons-download"></span>');
			return;
		}

		const attachment = wp.media.attachment(mediaId);
		
		attachment.fetch().then(() => {
			const data = attachment.toJSON();
			log.success('Fetched attachment:', data);

			// Reset button
			$button.prop('disabled', false).html('<span class="dashicons dashicons-download"></span>');

			// Show preview modal
			showMetadataPreview(targetField, metadataKey, data);

		}).catch(error => {
			log.error('Fetch failed:', error);
			showError('Failed to fetch media metadata');
			$button.prop('disabled', false).html('<span class="dashicons dashicons-download"></span>');
		});
	}

	/**
	 * Show metadata preview modal
	 */
	function showMetadataPreview(targetField, metadataKey, attachmentData) {
		const metadataValue = getMetadataValue(attachmentData, metadataKey);
		const isEmpty = !metadataValue || metadataValue.trim() === '';
		const statusClass = isEmpty ? 'empty' : 'has-data';
		const statusIcon = isEmpty ? '❌' : '✅';
		const statusText = isEmpty ? 'No data available' : 'Data available';

		const modalHtml = `
			<div class="jlbpartners-metadata-modal">
				<div class="jlbpartners-metadata-modal__overlay"></div>
				<div class="jlbpartners-metadata-modal__content">
					<div class="jlbpartners-metadata-modal__header">
						<h3>📥 Media Metadata: ${formatMetadataKey(metadataKey)}</h3>
						<button class="jlbpartners-metadata-modal__close" aria-label="Close">×</button>
					</div>
					<div class="jlbpartners-metadata-modal__body">
						<div class="jlbpartners-metadata-status ${statusClass}">
							<span class="status-icon">${statusIcon}</span>
							<span class="status-text">${statusText}</span>
						</div>
						${!isEmpty ? `
							<div class="jlbpartners-metadata-preview">
								<label>Media Library ${formatMetadataKey(metadataKey)}:</label>
								<div class="metadata-value">${escapeHtml(metadataValue)}</div>
							</div>
						` : `
							<div class="jlbpartners-metadata-empty">
								<p>ℹ️ This image/video doesn't have ${formatMetadataKey(metadataKey).toLowerCase()} in the media library.</p>
								<p>You can add it by:</p>
								<ol>
									<li>Going to <strong>Media Library</strong></li>
									<li>Editing the image/video</li>
									<li>Adding the ${formatMetadataKey(metadataKey).toLowerCase()}</li>
									<li>Coming back and clicking the fetch button again</li>
								</ol>
							</div>
						`}
					</div>
					<div class="jlbpartners-metadata-modal__footer">
						${!isEmpty ? `
							<button class="button button-primary jlbpartners-metadata-apply" data-value="${escapeHtml(metadataValue)}">
								✅ Use This Data
							</button>
						` : ''}
						<button class="button jlbpartners-metadata-cancel">Cancel</button>
					</div>
				</div>
			</div>
		`;

		$('body').append(modalHtml);

		$('.jlbpartners-metadata-apply').on('click', function() {
			const value = $(this).data('value');
			applyMetadata(targetField, value);
			closeModal();
		});

		$('.jlbpartners-metadata-cancel, .jlbpartners-metadata-modal__close, .jlbpartners-metadata-modal__overlay').on('click', closeModal);
	}

	/**
	 * Get metadata value from attachment
	 */
	function getMetadataValue(attachment, key) {
		switch(key) {
			case 'alt':
				return attachment.alt || '';
			case 'caption':
				return attachment.caption || '';
			case 'description':
				return attachment.description || '';
			case 'title':
				return attachment.title || '';
			default:
				return '';
		}
	}

	/**
	 * Format metadata key for display
	 */
	function formatMetadataKey(key) {
		const labels = {
			'alt': 'ALT Text',
			'caption': 'Caption',
			'description': 'Description',
			'title': 'Title'
		};
		return labels[key] || key;
	}

	/**
	 * Escape HTML
	 */
	function escapeHtml(text) {
		const div = document.createElement('div');
		div.textContent = text;
		return div.innerHTML;
	}

	/**
	 * Apply metadata to field
	 */
	function applyMetadata(field, value) {
		log.info(`Applying metadata to ${field.get('name')}:`, value);

		field.val(value);
		log.success('Applied metadata via ACF API');
		showSuccess('✅ Metadata applied successfully!');
	}

	/**
	 * Close modal
	 */
	function closeModal() {
		$('.jlbpartners-metadata-modal').fadeOut(200, function() {
			$(this).remove();
		});
	}

	/**
	 * Show success message
	 */
	function showSuccess(message) {
		if (typeof wp !== 'undefined' && wp.data && wp.data.dispatch) {
			wp.data.dispatch('core/notices').createNotice('success', message, {
				isDismissible: true,
				type: 'snackbar'
			});
		}
	}

	/**
	 * Show error message
	 */
	function showError(message) {
		if (typeof wp !== 'undefined' && wp.data && wp.data.dispatch) {
			wp.data.dispatch('core/notices').createNotice('error', message, {
				isDismissible: true,
				type: 'snackbar'
			});
		}
		log.error(message);
	}

	/**
	 * Initialize on ACF ready
	 */
	if (typeof acf !== 'undefined') {
		acf.addAction('ready', initFetchButtons);
	}

	$(document).ready(function() {
		setTimeout(initFetchButtons, 1000);
	});

	log.success('ACF Media Fetch: Ready');

})(jQuery);
