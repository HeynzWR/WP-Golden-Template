/**
 * Golden Template Core Admin Scripts
 *
 * @package GoldenTemplate_Core
 */

(function($) {
	'use strict';

	/**
	 * Handle image uploads using WordPress Media Library
	 */
		function initImageUploads() {
		$('.golden-template-upload-btn').on('click', function(e) {
			e.preventDefault();

			var button = $(this);
			var target = button.data('target');
			var inputField = $('#golden_template_' + target);
			var previewContainer = button.siblings('.golden-template-image-preview');

			// Create a new media uploader instance for each upload
			var mediaUploader = wp.media({
				title: 'Choose Image',
				button: {
					text: 'Choose Image'
				},
				multiple: false
			});

			// When a file is selected, grab the URL and set it as the hidden input value
			mediaUploader.on('select', function() {
				var attachment = mediaUploader.state().get('selection').first().toJSON();
				
				// Update the input field
				inputField.val(attachment.id);

				// Update the preview using DOM methods
				var img = $('<img>').attr({
					'src': attachment.url,
					'alt': attachment.alt || '',
					'style': 'max-width: 300px; height: auto;'
				});
				previewContainer.empty().append(img);

				// Show remove button if it doesn't exist
				if (!button.siblings('.golden-template-remove-btn').length) {
					var removeBtn = $('<button>')
						.attr('type', 'button')
						.addClass('button button-link-delete golden-template-remove-btn')
						.attr('data-target', target)
						.text('Remove');
					button.after(removeBtn);
				}
				
				// Update logo preview in sidebar if this is a logo
				if (target === 'logo_desktop' || target === 'logo_mobile') {
					updateLogoPreview();
				}
			});

			// Open the uploader dialog
			mediaUploader.open();
		});

		// Handle image removal
		$(document).on('click', '.golden-template-remove-btn', function(e) {
			e.preventDefault();

			var button = $(this);
			var target = button.data('target');
			var inputField = $('#golden_template_' + target);
			var previewContainer = button.siblings('.golden-template-image-preview');

			// Clear the input field
			inputField.val('');

			// Clear the preview
			previewContainer.html('<span class="description">No image uploaded</span>');

			// Remove the button
			button.remove();
			
			// Update logo preview if needed
			if (target === 'logo_desktop' || target === 'logo_mobile') {
				updateLogoPreview();
			}
		});
	}

	/**
	 * Initialize color pickers (disabled for now)
	 */
	function initColorPickers() {
		// Color picker functionality disabled
	}

	/**
	 * Handle tab switching
	 */
	function initTabs() {
		$('.golden-template-tab').on('click', function() {
			var target = $(this).data('tab');
			
			// Remove active class from all tabs and content
			$('.golden-template-tab').removeClass('active');
			$('.golden-template-tab-content').removeClass('active');
			
			// Add active class to clicked tab and corresponding content
			$(this).addClass('active');
			$('#tab-' + target).addClass('active');
			
			// Save active tab to localStorage
			localStorage.setItem('golden-template_active_tab', target);
		});

		// Restore last active tab
		var activeTab = localStorage.getItem('golden-template_active_tab') || 'branding';
		$('.golden-template-tab[data-tab="' + activeTab + '"]').trigger('click');
	}

	/**
	 * Live preview updates
	 */
	function initLivePreviews() {
		updateLogoPreview();
	}

	/**
	 * Update logo preview in sidebar
	 */
		function updateLogoPreview() {
		// Update desktop logo preview
		var logoDesktopId = $('#golden_template_logo_desktop').val();
		var desktopPreviewContainer = $('#logo-desktop-preview-display');
		
		if (!logoDesktopId) {
			desktopPreviewContainer.html('<span style="color: #999;">No desktop logo</span>');
		} else {
			// Get the image from the main upload preview
			var desktopMainPreview = $('#golden_template_logo_desktop').siblings('.golden-template-image-preview').find('img');
			if (desktopMainPreview.length) {
				var clonedDesktopImg = desktopMainPreview.clone();
				clonedDesktopImg.css({
					'max-height': '60px',
					'width': 'auto',
					'max-width': '180px'
				});
				desktopPreviewContainer.empty().append(clonedDesktopImg);
			}
		}
		
		// Update mobile logo preview
		var logoMobileId = $('#golden_template_logo_mobile').val();
		var mobilePreviewContainer = $('#logo-mobile-preview-display');
		
		if (!logoMobileId) {
			mobilePreviewContainer.html('<span style="color: #999;">No mobile logo</span>');
		} else {
			// Get the image from the main upload preview
			var mobileMainPreview = $('#golden_template_logo_mobile').siblings('.golden-template-image-preview').find('img');
			if (mobileMainPreview.length) {
				var clonedMobileImg = mobileMainPreview.clone();
				clonedMobileImg.css({
					'max-height': '50px',
					'width': 'auto',
					'max-width': '150px'
				});
				mobilePreviewContainer.empty().append(clonedMobileImg);
			}
		}
	}

	/**
	 * Initialize sponsor repeater functionality
	 */
	function initSponsorRepeater() {
		var container = $('#golden-template-sponsors-container');
		var addButton = $('#golden-template-add-sponsor-btn');
		
		// Function to reindex all sponsor rows
		function reindexSponsors() {
			container.find('.golden-template-sponsor-row').each(function(index) {
				$(this).attr('data-index', index);
				$(this).find('h4').text('Sponsor ' + (index + 1));
				$(this).find('.golden-template-sponsor-image').attr('name', 'golden-template_sponsors[' + index + '][image_id]');
				$(this).find('.golden-template-sponsor-url').attr('name', 'golden-template_sponsors[' + index + '][url]');
			});
		}
		
		// Function to serialize sponsors to JSON before form submission
		function serializeSponsors() {
			var sponsors = [];
			container.find('.golden-template-sponsor-row').each(function() {
				var imageId = $(this).find('.golden-template-sponsor-image').val();
				var url = $(this).find('.golden-template-sponsor-url').val();
				
				// Only add if image is uploaded
				if (imageId && imageId != '0') {
					sponsors.push({
						image_id: parseInt(imageId),
						url: url
					});
				}
			});
			
			$('#golden-template_sponsors').val(JSON.stringify(sponsors));
		}
		
		// Add new sponsor row
		addButton.on('click', function(e) {
			e.preventDefault();
			
			var currentIndex = container.find('.golden-template-sponsor-row').length;
			
			var newRow = $('<div class="golden-template-sponsor-row" data-index="' + currentIndex + '">' +
				'<h4>Sponsor ' + (currentIndex + 1) + '</h4>' +
				'<table class="form-table">' +
					'<tr>' +
						'<th scope="row">Logo</th>' +
						'<td>' +
							'<div class="golden-template-image-upload">' +
								'<div class="golden-template-image-preview">' +
									'<span class="description">No logo uploaded</span>' +
								'</div>' +
								'<input type="hidden" class="golden-template-sponsor-image" name="golden-template_sponsors[' + currentIndex + '][image_id]" value="0" />' +
								'<button type="button" class="button golden-template-sponsor-upload-btn">Upload Logo</button>' +
								'<p class="description">Recommended size: 200x80px PNG with transparency.</p>' +
							'</div>' +
						'</td>' +
					'</tr>' +
					'<tr>' +
						'<th scope="row"><label>Website URL</label></th>' +
						'<td>' +
							'<input type="url" class="regular-text golden-template-sponsor-url" name="golden-template_sponsors[' + currentIndex + '][url]" value="" placeholder="https://sponsorwebsite.com" />' +
							'<p class="description">Website URL for this sponsor (opens in new tab).</p>' +
						'</td>' +
					'</tr>' +
				'</table>' +
				'<button type="button" class="button button-link-delete golden-template-remove-sponsor-btn">Remove Sponsor</button>' +
				'<hr style="margin: 20px 0;">' +
			'</div>');
			
			container.append(newRow);
		});
		
		// Remove sponsor row
		container.on('click', '.golden-template-remove-sponsor-btn', function(e) {
			e.preventDefault();
			
			if (confirm('Are you sure you want to remove this sponsor?')) {
				$(this).closest('.golden-template-sponsor-row').remove();
				reindexSponsors();
			}
		});
		
		// Handle sponsor image upload
		container.on('click', '.golden-template-sponsor-upload-btn', function(e) {
			e.preventDefault();
			
			var button = $(this);
			var row = button.closest('.golden-template-sponsor-row');
			var inputField = row.find('.golden-template-sponsor-image');
			var previewContainer = row.find('.golden-template-image-preview');
			
			var mediaUploader = wp.media({
				title: 'Choose Sponsor Logo',
				button: {
					text: 'Choose Logo'
				},
				multiple: false
			});
			
			mediaUploader.on('select', function() {
				var attachment = mediaUploader.state().get('selection').first().toJSON();
				
				inputField.val(attachment.id);
				
				var img = $('<img>').attr({
					'src': attachment.url,
					'alt': attachment.alt || 'Sponsor logo',
					'style': 'max-width: 200px; height: auto;'
				});
				previewContainer.empty().append(img);
				
				// Add remove image button if it doesn't exist
				if (!button.siblings('.golden-template-sponsor-remove-image-btn').length) {
					var removeBtn = $('<button>')
						.attr('type', 'button')
						.addClass('button button-link-delete golden-template-sponsor-remove-image-btn')
						.text('Remove Image');
					button.after(removeBtn);
				}
			});
			
			mediaUploader.open();
		});
		
		// Handle sponsor image removal
		container.on('click', '.golden-template-sponsor-remove-image-btn', function(e) {
			e.preventDefault();
			
			var button = $(this);
			var row = button.closest('.golden-template-sponsor-row');
			var inputField = row.find('.golden-template-sponsor-image');
			var previewContainer = row.find('.golden-template-image-preview');
			
			inputField.val('0');
			previewContainer.html('<span class="description">No logo uploaded</span>');
			button.remove();
		});
		
		// Serialize sponsors before form submission
		$('form').on('submit', function() {
			serializeSponsors();
		});
	}

	/**
	 * Initialize preview hover tooltip functionality
	 */
	function initPreviewTooltip() {
		// Preview image paths
		var previewImages = {
			'footer-settings': [
				GOLDEN_TEMPLATE_CORE_URL + 'assets/images/preview-footer-settings.png'
			],
			'subscription-area': [
				GOLDEN_TEMPLATE_CORE_URL + 'assets/images/preview-subscription-area.png'
			]
		};
		
		var previewCaptions = {
			'footer-settings': 'Address and page links in your footer',
			'subscription-area': 'Newsletter subscription heading and description text in your footer'
		};
		
		// Create backdrop and tooltip HTML if they don't exist
		if ($('#golden-template-preview-backdrop').length === 0) {
			$('body').append('<div id="golden-template-preview-backdrop" class="golden-template-preview-backdrop"></div>');
		}
		
		if ($('#golden-template-preview-tooltip').length === 0) {
			$('body').append(
				'<div id="golden-template-preview-tooltip" class="golden-template-preview-tooltip">' +
					'<img src="" alt="Preview" />' +
					'<div class="golden-template-preview-tooltip-caption"></div>' +
				'</div>'
			);
		}
		
		var backdrop = $('#golden-template-preview-backdrop');
		var tooltip = $('#golden-template-preview-tooltip');
		var tooltipImg = tooltip.find('img');
		var tooltipCaption = tooltip.find('.golden-template-preview-tooltip-caption');
		
		// Function to try loading image with fallback
		function loadImageWithFallback(urls, index, callback) {
			if (index >= urls.length) {
				return;
			}
			
			var img = new Image();
			img.onload = function() {
				if (callback) callback(urls[index]);
			};
			img.onerror = function() {
				loadImageWithFallback(urls, index + 1, callback);
			};
			img.src = urls[index];
		}
		
		// Handle preview icon hover
		$(document).on('mouseenter', '.golden-template-preview-icon', function(e) {
			var $icon = $(this);
			var previewType = $icon.data('preview');
			
			if (previewImages[previewType]) {
				var urls = Array.isArray(previewImages[previewType]) ? previewImages[previewType] : [previewImages[previewType]];
				
				loadImageWithFallback(urls, 0, function(imageUrl) {
					tooltipImg.attr('src', imageUrl);
					tooltipCaption.text(previewCaptions[previewType] || '');
					
					backdrop.addClass('active');
					tooltip.addClass('active');
				});
			}
		});
		
		// Hide tooltip on mouse leave
		$(document).on('mouseleave', '.golden-template-preview-icon', function() {
			backdrop.removeClass('active');
			tooltip.removeClass('active');
		});
	}

	/**
	 * Initialize everything on document ready
	 */
	$(document).ready(function() {
		initImageUploads();
		initColorPickers();
		initTabs();
		initLivePreviews();
		initSponsorRepeater();
		initPreviewTooltip();
	});

})(jQuery);
