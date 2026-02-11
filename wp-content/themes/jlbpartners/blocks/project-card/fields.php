<?php
/**
 * Project Card Section - ACF Fields
 *
 * @package JLBPartners
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register ACF fields for Project Card block
 */
function jlbpartners_project_card_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_project_card',
			'title'                 => 'Project Card',
			'fields'                => array(
				// Select Project (Optional - to prefill data)
				array(
					'key'               => 'field_project_card_select',
					'label'             => 'Select Project (Optional)',
					'name'              => 'project_card_select',
					'type'              => 'post_object',
					'instructions'      => 'Optional. Select a project to auto-fill the fields below. You can still edit any field after selecting.',
					'required'          => 0,
					'post_type'         => array( 'projects' ),
					'return_format'     => 'object',
					'allow_null'        => 1,
				),

				// Layout Toggle
				array(
					'key'               => 'field_project_card_layout',
					'label'             => 'Layout',
					'name'              => 'project_card_layout',
					'type'              => 'button_group',
					'instructions'      => 'Optional. Choose the layout direction',
					'required'          => 0,
					'choices'           => array(
						'image-left'  => 'Image Left',
						'image-right' => 'Image Right',
					),
					'default_value'     => 'image-left',
					'layout'            => 'horizontal',
					'return_format'     => 'value',
				),

				// Project Image
				array(
					'key'               => 'field_project_card_image',
					'label'             => 'Project Image',
					'name'              => 'project_card_image',
					'type'              => 'image',
					'instructions'      => 'Required. Upload or select a project image. Recommended size: 800×1000 px.',
					'required'          => 1,
					'return_format'     => 'id',
					'preview_size'      => 'large',
					'library'           => 'all',
				),
				
				// Mobile Project Image
				array(
					'key'               => 'field_project_card_mobile_image',
					'label'             => 'Mobile Image',
					'name'              => 'project_card_mobile_image',
					'type'              => 'image',
					'instructions'      => 'Optional: Upload a different image optimized for mobile devices. Recommended size: 768×1024 px. If not provided, the desktop image will be used.',
					'required'          => 0,
					'return_format'     => 'id',
					'preview_size'      => 'medium',
					'library'           => 'all',
				),
				
				// Title
				array(
					'key'               => 'field_project_card_title',
					'label'             => 'Title',
					'name'              => 'project_card_title',
					'type'              => 'text',
					'instructions'      => 'Required. Main title for the project',
					'required'          => 1,
					'maxlength'         => 100,
					'placeholder'       => 'Enter project title...',
				),

				// Subtitle
				array(
					'key'               => 'field_project_card_subtitle',
					'label'             => 'Subtitle',
					'name'              => 'project_card_subtitle',
					'type'              => 'text',
					'instructions'      => 'Optional. Subtitle for the project (e.g., Location | Units)',
					'required'          => 0,
					'maxlength'         => 150,
					'placeholder'       => 'Enter project subtitle...',
				),
				
				// Description
				array(
					'key'               => 'field_project_card_description',
					'label'             => 'Description',
					'name'              => 'project_card_description',
					'type'              => 'textarea',
					'instructions'      => 'Optional. Description text for the project',
					'required'          => 0,
					'rows'              => 4,
					'maxlength'         => 500,
					'placeholder'       => 'Enter project description...',
				),

				// CTA Link
				array(
					'key'               => 'field_project_card_cta',
					'label'             => 'CTA Button',
					'name'              => 'project_card_cta',
					'type'              => 'link',
					'instructions'      => 'Optional. Add call-to-action button with text and URL',
					'required'          => 0,
					'return_format'     => 'array',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/project-card',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
		)
	);
}
add_action( 'acf/init', 'jlbpartners_project_card_fields' );

/**
 * Auto-fill fields when project is selected
 */
add_action( 'acf/input/admin_footer', 'jlbpartners_autofill_project_fields' );
function jlbpartners_autofill_project_fields() {
	$nonce = wp_create_nonce( 'jlbpartners_get_project_data' );
	?>
	<script>
	jQuery(document).ready(function($) {
		// Listen for project selection
		$(document).on('change', '[data-name="project_card_select"] select', function() {
			var projectId = $(this).val();
			
			if (!projectId) return;
			
			// AJAX call to get project data
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'get_project_data',
					project_id: projectId,
					nonce: '<?php echo esc_js( $nonce ); ?>'
				},
				success: function(response) {
					if (response.success) {
						var data = response.data;
						
						// Fill title
						if (data.title) {
							$('[data-name="project_card_title"] input').val(data.title);
						}
						
						// Fill subtitle
						if (data.subtitle) {
							$('[data-name="project_card_subtitle"] input').val(data.subtitle);
						}
						
						// Fill description
						if (data.description) {
							$('[data-name="project_card_description"] textarea').val(data.description);
						}
						
						// Fill image (ACF image field is complex, so we'll set the ID)
						if (data.image_id) {
							var $imageField = $('[data-name="project_card_image"]');
							var $imageInput = $imageField.find('input[type="hidden"]');
							
							if ($imageInput.length) {
								$imageInput.val(data.image_id).trigger('change');
								
								// Trigger ACF to update the preview
								if (data.image_url) {
									$imageField.find('.acf-image-uploader img').attr('src', data.image_url);
									$imageField.find('.acf-image-uploader').addClass('has-value');
									$imageField.find('.show-if-value').show();
									$imageField.find('.hide-if-value').hide();
								}
							}
						}
						
						// Fill CTA link
						if (data.cta_url) {
							$('[data-name="project_card_cta"] .link-url').val(data.cta_url);
							$('[data-name="project_card_cta"] .link-text').val(data.cta_title || 'View Project');
							$('[data-name="project_card_cta"] .link-target').prop('checked', data.cta_target === '_blank');
						}
					}
				}
			});
		});
	});
	</script>
	<?php
}

/**
 * AJAX handler to get project data
 */
add_action( 'wp_ajax_get_project_data', 'jlbpartners_get_project_data_ajax' );
function jlbpartners_get_project_data_ajax() {
	// Verify nonce for security
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'jlbpartners_get_project_data' ) ) {
		wp_send_json_error( 'Security check failed' );
		return;
	}

	// Check user capabilities (only allow logged-in users with edit permissions)
	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_send_json_error( 'Insufficient permissions' );
		return;
	}

	$project_id = isset( $_POST['project_id'] ) ? intval( $_POST['project_id'] ) : 0;
	
	if ( ! $project_id ) {
		wp_send_json_error( 'No project ID provided' );
		return;
	}
	
	// Get project data
	$title = get_the_title($project_id);
	
	// Build subtitle from location and units
	$location = get_field('project_location', $project_id);
	$units = get_field('project_units', $project_id);
	$subtitle = '';
	if ($location && $units) {
		$subtitle = $location . ' | ' . $units;
	} elseif ($location) {
		$subtitle = $location;
	} elseif ($units) {
		$subtitle = $units;
	}
	
	// Get description
	$description = get_field('project_description', $project_id);
	
	// Get featured image
	$image_id = get_post_thumbnail_id($project_id);
	$image_url = get_the_post_thumbnail_url($project_id, 'medium');
	
	// Get CTA
	$external_link = get_field('project_external_link', $project_id);
	$cta_url = $external_link ? $external_link['url'] : get_permalink($project_id);
	$cta_title = $external_link ? $external_link['title'] : 'View Project';
	$cta_target = $external_link && isset($external_link['target']) ? $external_link['target'] : '';
	
	wp_send_json_success(array(
		'title' => $title,
		'subtitle' => $subtitle,
		'description' => $description,
		'image_id' => $image_id,
		'image_url' => $image_url,
		'cta_url' => $cta_url,
		'cta_title' => $cta_title,
		'cta_target' => $cta_target,
	));
}