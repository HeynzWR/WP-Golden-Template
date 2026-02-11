/**
 * Latest Posts Auto-Fill (Based on Featured Categories)
 *
 * Auto-populates title, image, description, and link when a post is selected
 *
 * @package JLBPartners
 */

(function($) {
	'use strict';

	// Developer mode
	const DEV_MODE = true;
	const log = {
		info: (...args) => DEV_MODE && console.log('ℹ️ Latest Posts:', ...args),
		success: (...args) => DEV_MODE && console.log('✅ Latest Posts:', ...args),
		error: (...args) => DEV_MODE && console.error('❌ Latest Posts:', ...args),
	};

	log.info('Loading...');

	/**
	 * Initialize auto-fill functionality
	 */
	function initAutoFill() {
		if (typeof acf === 'undefined') {
			log.error('ACF not loaded');
			return;
		}

		log.success('ACF loaded, initializing auto-fill...');

		// Listen for changes on post_object fields within featured_items repeater
		acf.addAction('ready', function() {
			setupPostSelectListeners();
			// Update post choices on initial load
			setTimeout(updatePostChoices, 500);
		});

		// Also setup listeners when new repeater rows are added
		acf.addAction('append', function($el) {
			setupPostSelectListeners($el);
			// Update post choices when new row added
			setTimeout(updatePostChoices, 300);
		});
	}

	/**
	 * Setup listeners for post selection
	 */
	function setupPostSelectListeners($scope) {
		const $context = $scope || $('body');
		
		// Find all post_object fields with name 'post' within featured_items repeater
		let $postFields;
		if ($scope) {
			// If scope provided (new row), find field within that scope
			$postFields = $scope.find('[data-name="post"]');
			if ($postFields.length === 0) {
				// Try direct search
				$postFields = acf.getFields({ name: 'post', parent: $scope });
			}
		} else {
			// Search globally for Latest Posts featured_items
			$postFields = $('[data-name="featured_items"] [data-name="post"]');
		}
		
		if ($postFields.length === 0) {
			log.info('No post selection fields found');
			return;
		}

		log.info(`Found ${$postFields.length} post selection field(s)`);

		// Use ACF's getFields if we got jQuery objects
		if ($postFields.jquery) {
			$postFields.each(function() {
				const field = acf.getField($(this));
				if (field) {
					attachChangeListener(field);
				}
			});
		} else {
			// Already ACF field objects
			$postFields.forEach(field => {
				attachChangeListener(field);
			});
		}
	}

	/**
	 * Attach change listener to a field
	 */
	function attachChangeListener(field) {
		// Remove existing listener to avoid duplicates
		field.off('change');
		
		// Attach new listener
		field.on('change', function() {
			const postId = field.val();
			if (postId) {
				log.info('Post selected:', postId);
				autoFillFields(field, postId);
				updatePostChoices();
			}
		});
		
		log.success('Listener attached to field');
	}

	/**
	 * Update post choices to hide already-selected posts
	 */
	function updatePostChoices() {
		// Get all selected post IDs
		const selectedPostIds = [];
		const $postFields = $('[data-name="featured_items"] [data-name="post"]');
		
		$postFields.each(function() {
			const field = acf.getField($(this));
			if (field && field.val()) {
				selectedPostIds.push(parseInt(field.val()));
			}
		});

		log.info('Selected post IDs:', selectedPostIds);

		// Update each post field's choices
		$postFields.each(function() {
			const field = acf.getField($(this));
			if (!field) return;

			const currentValue = field.val();
			const $select = field.$input();

			// Hide options that are selected in other rows
			$select.find('option').each(function() {
				const optionValue = parseInt($(this).val());
				if (optionValue && selectedPostIds.includes(optionValue) && optionValue !== parseInt(currentValue)) {
					$(this).prop('disabled', true).hide();
				} else if (optionValue) {
					$(this).prop('disabled', false).show();
				}
			});

			// Trigger Select2 update if it exists
			if ($select.data('select2')) {
				$select.trigger('change.select2');
			}
		});
	}

	/**
	 * Auto-fill fields based on selected post
	 */
	function autoFillFields(postField, postId) {
		// Get the repeater row
		const $row = postField.$el.closest('.acf-row');
		if (!$row.length) {
			log.error('Could not find repeater row');
			return;
		}

		log.info('Fetching post data for ID:', postId);

		// Show loading state
		showLoadingState($row);

		// Fetch post data via REST API
		$.ajax({
			url: wpApiSettings.root + 'wp/v2/posts/' + postId + '?_embed',
			method: 'GET',
			beforeSend: function(xhr) {
				xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
			},
			success: function(post) {
				log.success('Post data fetched:', post);
				populateFields($row, post);
			},
			error: function(xhr) {
				// Try other post types
				tryOtherPostTypes($row, postId);
			}
		});
	}

	/**
	 * Try fetching from other post types
	 */
	function tryOtherPostTypes($row, postId) {
		const postTypes = ['pages'];
		let currentIndex = 0;

		function tryNext() {
			if (currentIndex >= postTypes.length) {
				log.error('Post not found in any post type');
				hideLoadingState($row);
				showError('Could not fetch post data');
				return;
			}

			const postType = postTypes[currentIndex];
			const endpoint = postType;

			$.ajax({
				url: wpApiSettings.root + 'wp/v2/' + endpoint + '/' + postId + '?_embed',
				method: 'GET',
				beforeSend: function(xhr) {
					xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
				},
				success: function(post) {
					log.success('Post data fetched from ' + postType + ':', post);
					populateFields($row, post);
				},
				error: function() {
					currentIndex++;
					tryNext();
				}
			});
		}

		tryNext();
	}

	/**
	 * Populate fields with post data
	 * 
	 * Image Handling:
	 * - If post has a featured image: Auto-populate it
	 * - If post has NO featured image: Leave blank (template will use default placeholder from theme settings)
	 * - User can always manually select a different image from the media library
	 */
	function populateFields($row, post) {
		// Get fields in this row
		const titleField = acf.getFields({ name: 'title', parent: $row })[0];
		const imageField = acf.getFields({ name: 'image', parent: $row })[0];
		const descriptionField = acf.getFields({ name: 'description', parent: $row })[0];
		const linkField = acf.getFields({ name: 'link', parent: $row })[0];
		const linkTextField = acf.getFields({ name: 'link_text', parent: $row })[0];

		// Populate title
		if (titleField && post.title && post.title.rendered) {
			const title = post.title.rendered.replace(/<[^>]*>/g, ''); // Strip HTML tags safely
			titleField.val(title);
			log.info('Title populated:', title);
		}

		if (imageField) {
			if (post._embedded && post._embedded['wp:featuredmedia']) {
				const featuredMedia = post._embedded['wp:featuredmedia'][0];
				log.info('Featured media found:', featuredMedia);
				
				if (featuredMedia && featuredMedia.id) {
					log.info('Setting image field to ID:', featuredMedia.id);
					
					// Method 1: Set the value directly (this is what saves it)
					try {
						imageField.val(featuredMedia.id);
						log.info('✓ Set value via ACF val()');
					} catch (error) {
						log.error('Error setting via ACF val():', error);
					}
					
					// Method 2: Set via jQuery input (redundant but ensures it's set)
					try {
						const $input = imageField.$input();
						if ($input && $input.length) {
							$input.val(featuredMedia.id);
							log.info('✓ Set value via jQuery input');
						}
					} catch (error) {
						log.error('Error setting via jQuery:', error);
					}
					
					// Method 3: Trigger ACF to update the preview (without opening modal)
					setTimeout(() => {
						try {
							// Get the preview container
							const $field = imageField.$el;
							const $preview = $field.find('.acf-image-uploader');
							
							if ($preview.length) {
								// Add the image class to show the preview
								$preview.addClass('has-value');
								
								// Update the preview image if we have the URL
								if (featuredMedia.source_url) {
									const $img = $preview.find('img');
									if ($img.length) {
										$img.attr('src', featuredMedia.source_url);
										$img.attr('alt', featuredMedia.alt_text || '');
									} else {
										// Create preview image if it doesn't exist
										const imgHtml = '<img src="' + featuredMedia.source_url + '" alt="' + (featuredMedia.alt_text || '') + '" />';
										$preview.find('.image-wrap').html(imgHtml);
									}
									log.info('✓ Updated preview image');
								}
								
								// Update the hidden input that stores the ID
								$preview.find('input[type="hidden"]').val(featuredMedia.id);
								
								// Trigger change to let ACF know the value changed
								$field.trigger('change');
								log.info('✓ Triggered change event');
							}
							
							log.success('✅ Image field updated and preview displayed');
						} catch (error) {
							log.error('Error updating preview:', error);
						}
					}, 200);
					
					log.success('✅ Image populated with ID:', featuredMedia.id);
				} else {
					log.error('Featured media found but no ID:', featuredMedia);
				}
			} else {
				// No featured image in the post
				log.info('ℹ️ Post has no featured image. Leaving image field blank (user can select manually or use default placeholder).');
				
				// Don't set anything - leave the field as is
				// User can either:
				// 1. Manually select an image from media library
				// 2. Leave it blank and template will use default placeholder
			}
		} else {
			log.error('Image field not found in row!');
		}

		// Populate description (excerpt)
		if (descriptionField && post.excerpt && post.excerpt.rendered) {
			let excerpt = post.excerpt.rendered.replace(/<[^>]*>/g, ''); // Strip HTML tags safely
			excerpt = excerpt.trim();
			// Limit to 300 characters
			if (excerpt.length > 300) {
				excerpt = excerpt.substring(0, 297) + '...';
			}
			descriptionField.val(excerpt);
			log.info('Description populated:', excerpt.substring(0, 50) + '...');
		}

		// Populate link
		if (linkField && post.link) {
			linkField.val(post.link);
			log.info('Link populated:', post.link);
		}

		// Populate link text
		if (linkTextField && !linkTextField.val()) {
			linkTextField.val('Read More');
			log.info('Link text populated: Read More');
		}

		hideLoadingState($row);
		showSuccess('✅ Fields auto-populated from post!');
	}

	/**
	 * Show loading state
	 */
	function showLoadingState($row) {
		if (!$row.find('.latest-posts-autofill-loading').length) {
			$row.prepend('<div class="latest-posts-autofill-loading" style="padding: 10px; background: #f0f0f1; border-left: 4px solid #2271b1; margin-bottom: 10px;">🔄 Loading post data...</div>');
		}
	}

	/**
	 * Hide loading state
	 */
	function hideLoadingState($row) {
		$row.find('.latest-posts-autofill-loading').remove();
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
		acf.addAction('ready', initAutoFill);
	}

	$(document).ready(function() {
		setTimeout(initAutoFill, 1000);
	});

	log.success('Ready');

})(jQuery);

