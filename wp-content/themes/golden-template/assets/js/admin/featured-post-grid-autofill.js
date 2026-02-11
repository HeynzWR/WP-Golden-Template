/**
 * Featured Post Grid Block - Auto-fill functionality
 * 
 * Auto-populates title, image, and description when a post is selected
 * 
 * @package GoldenTemplate
 */

(function($) {
    'use strict';

    // Wait for ACF to be ready
    if (typeof acf === 'undefined') {
        return;
    }

    /**
     * Auto-fill fields when post is selected
     */
    function autoFillPostData($field) {
        const $postField = $field;
        const $repeaterRow = $postField.closest('.acf-row');
        
        if (!$repeaterRow.length) {
            return;
        }

        const postId = $postField.val();
        
        if (!postId) {
            return;
        }

        // Show loading state
        const $titleField = $repeaterRow.find('[data-name="title"] input');
        const $descriptionField = $repeaterRow.find('[data-name="description"] textarea');
        const $linkField = $repeaterRow.find('[data-name="link"] input');
        
        if ($titleField.length) {
            $titleField.prop('disabled', true).val('Loading...');
        }
        
        if ($descriptionField.length) {
            $descriptionField.prop('disabled', true).val('Loading...');
        }
        
        if ($linkField.length) {
            $linkField.prop('disabled', true).val('Loading...');
        }

        // Fetch post data
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'get_post_data_for_autofill',
                post_id: postId,
                nonce: acf.get('nonce')
            },
            success: function(response) {
                if (response.success && response.data) {
                    const data = response.data;
                    
                    // Auto-fill title (only if empty or was loading)
                    if ($titleField.length && (!$titleField.val() || $titleField.val() === 'Loading...')) {
                        $titleField.val(data.title || '');
                    }
                    
                    // Auto-fill description (only if empty or was loading)
                    if ($descriptionField.length && (!$descriptionField.val() || $descriptionField.val() === 'Loading...')) {
                        $descriptionField.val(data.excerpt || '');
                    }
                    
                    // Auto-fill link (only if empty or was loading)
                    if ($linkField.length && (!$linkField.val() || $linkField.val() === 'Loading...')) {
                        $linkField.val(data.permalink || '');
                    }
                    
                    // Auto-fill featured image
                    const $imageField = $repeaterRow.find('[data-name="image"]');
                    if ($imageField.length && data.featured_image_id) {
                        // Only set if no image is currently selected
                        const currentImageId = $imageField.find('input[type="hidden"]').val();
                        if (!currentImageId) {
                            acf.getField($imageField.attr('data-key')).val(data.featured_image_id);
                        }
                    }
                }
            },
            error: function() {
                console.log('Error fetching post data');
            },
            complete: function() {
                // Remove loading state
                $titleField.prop('disabled', false);
                $descriptionField.prop('disabled', false);
                $linkField.prop('disabled', false);
                
                // Clear loading text if it's still there
                if ($titleField.val() === 'Loading...') {
                    $titleField.val('');
                }
                if ($descriptionField.val() === 'Loading...') {
                    $descriptionField.val('');
                }
                if ($linkField.val() === 'Loading...') {
                    $linkField.val('');
                }
            }
        });
    }

    /**
     * Initialize auto-fill functionality
     */
    function initAutoFill() {
        // Handle post selection in featured items repeater
        acf.addAction('change', function($field) {
            // Check if this is a post field in the featured-post-grid block
            if ($field.data.type === 'post_object' && 
                $field.data.name === 'post' && 
                $field.$el.closest('[data-type="featured-post-grid"]').length) {
                
                autoFillPostData($field.$input());
            }
        });
    }

    // Initialize when ACF is ready
    acf.addAction('ready', initAutoFill);
    acf.addAction('append', initAutoFill);

})(jQuery);