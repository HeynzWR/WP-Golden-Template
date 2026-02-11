<?php
/**
 * Block Helper Functions
 *
 * Utility functions for ACF Blocks to access branding and handle common tasks.
 *
 * @package GoldenTemplate
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Display block preview image
 *
 * @param string $block_name Block name (e.g., 'hero-section').
 * @param string $block_title Display title for the block.
 * @return void
 */
function golden_template_show_block_preview( $block_name, $block_title ) {
	// Check for both jpg and png extensions.
	$extensions = array( 'jpg', 'png' );
	$preview_image_path = '';
	$preview_image_url = '';
	
	foreach ( $extensions as $ext ) {
		$path = get_template_directory() . '/assets/images/block-previews/' . $block_name . '-preview.' . $ext;
		if ( file_exists( $path ) ) {
			$preview_image_path = $path;
			$preview_image_url = get_template_directory_uri() . '/assets/images/block-previews/' . $block_name . '-preview.' . $ext;
			break;
		}
	}
	
	if ( ! empty( $preview_image_path ) ) {
		// Show image preview.
		?>
		<div class="golden-template-block-preview" style="position: relative; border: 2px dashed #00a400; border-radius: 8px; overflow: hidden;">
			<img 
				src="<?php echo esc_url( $preview_image_url ); ?>" 
				alt="<?php echo esc_attr( $block_title . ' Preview' ); ?>" 
				style="width: 100%; height: auto; display: block;"
				loading="lazy"
			>
			<div style="position: absolute; top: 10px; left: 10px; background: rgba(0, 164, 0, 0.9); color: white; padding: 8px 12px; border-radius: 4px; font-size: 12px; font-weight: 600;">
				<?php echo esc_html( $block_title ); ?>
			</div>
			<div style="position: absolute; bottom: 10px; right: 10px; background: rgba(0, 0, 0, 0.7); color: white; padding: 6px 10px; border-radius: 4px; font-size: 11px;">
				<?php esc_html_e( 'Add content to customize', 'golden-template' ); ?>
			</div>
		</div>
		<?php
	} else {
		// Fallback to text preview if image doesn't exist.
		?>
		<div class="golden-template-block--preview" style="min-height: 300px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%); border: 2px dashed #00a400; border-radius: 8px; padding: 40px; text-align: center;">
			<div class="golden-template-block__preview-placeholder" style="max-width: 500px;">
				<div style="font-size: 48px; margin-bottom: 16px;">🖼️</div>
				<h3 style="margin: 0 0 12px 0; color: #1d2327; font-size: 20px; font-weight: 600;"><?php echo esc_html( $block_title ); ?></h3>
				<p style="margin: 0 0 8px 0; color: #646970; font-size: 14px; line-height: 1.6;">
					<?php esc_html_e( 'Add content to get started with this block.', 'golden-template' ); ?>
				</p>
				<div style="margin-top: 20px; padding: 12px; background: rgba(255, 255, 255, 0.7); border-radius: 4px; font-size: 12px; color: #646970;">
					<strong>⚠️ Preview Image Missing:</strong><br>
					<?php printf( esc_html__( 'Place %s-preview.jpg in:', 'golden-template' ), esc_html( $block_name ) ); ?><br>
					/assets/images/block-previews/
				</div>
			</div>
		</div>
		<?php
	}
}

/**
 * Sanitize text with allowed HTML
 *
 * @param string $text Text to sanitize.
 * @return string Sanitized text.
 */
function golden_template_sanitize_text( $text ) {
	$allowed_tags = array(
		'strong' => array(),
		'em'     => array(),
		'br'     => array(),
		'span'   => array(
			'class' => array(),
		),
	);

	return wp_kses( $text, $allowed_tags );
}

/**
 * Get optimized image attributes
 *
 * @param int    $image_id Image ID.
 * @param string $size Image size.
 * @param array  $args Additional arguments.
 * @return array Image attributes.
 */
function golden_template_get_optimized_image_attrs( $image_id, $size = 'full', $args = array() ) {
	$defaults = array(
		'lazy'    => true,
		'srcset'  => true,
		'sizes'   => '100vw',
	);

	$args = wp_parse_args( $args, $defaults );

	$attrs = array(
		'src'    => wp_get_attachment_image_url( $image_id, $size ),
		'alt'    => get_post_meta( $image_id, '_wp_attachment_image_alt', true ),
		'width'  => '',
		'height' => '',
	);

	// Get image dimensions.
	$image_meta = wp_get_attachment_metadata( $image_id );
	if ( $image_meta ) {
		$attrs['width']  = $image_meta['width'] ?? '';
		$attrs['height'] = $image_meta['height'] ?? '';
	}

	// Add lazy loading.
	if ( $args['lazy'] ) {
		$attrs['loading'] = 'lazy';
	}

	// Add srcset for responsive images.
	if ( $args['srcset'] ) {
		$attrs['srcset'] = wp_get_attachment_image_srcset( $image_id, $size );
		$attrs['sizes']  = $args['sizes'];
	}

	return $attrs;
}
