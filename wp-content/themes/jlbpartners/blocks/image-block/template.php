<?php
/**
 * Image Block Template
 *
 * @package JLBPartners
 *
 * @param array  $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool   $is_preview True during AJAX preview.
 * @param int    $post_id The post ID this block is saved to.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get field values.
$image        = get_field( 'image' );
$mobile_image = get_field( 'mobile_image' );

// Accessibility fields.
$image_role        = get_field( 'image_role' ) ?: 'informational';
$image_alt         = get_field( 'image_alt' );
$image_caption     = get_field( 'image_caption' );
$image_description = get_field( 'image_description' );

// Create block attributes.
$block_id = 'image-block-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}

$class_name = 'section section--light';
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}

// Preview mode handling.
if ( $is_preview && empty( $image ) ) {
	jlbpartners_show_block_preview( 'image-block', __( 'Image Block', 'jlbpartners' ) );
	return;
}

// Skip rendering if required fields are empty.
if ( empty( $image ) ) {
	return;
}

// Get image data.
$image_url    = wp_get_attachment_image_url( $image, 'large' );
$image_meta   = wp_get_attachment_metadata( $image );
$image_width  = isset( $image_meta['width'] ) ? $image_meta['width'] : 1200;
$image_height = isset( $image_meta['height'] ) ? $image_meta['height'] : 800;

// Get mobile image data.
$mobile_image_url    = '';
$mobile_image_width  = '';
$mobile_image_height = '';

if ( $mobile_image ) {
	$mobile_image_url = wp_get_attachment_image_url( $mobile_image, 'large' );
	$mobile_image_meta   = wp_get_attachment_metadata( $mobile_image );
	$mobile_image_width  = isset( $mobile_image_meta['width'] ) ? $mobile_image_meta['width'] : 768;
	$mobile_image_height = isset( $mobile_image_meta['height'] ) ? $mobile_image_meta['height'] : 1024;
}

// Auto-populate alt text if not provided.
$alt_text = $image_alt ? $image_alt : get_post_meta( $image, '_wp_attachment_image_alt', true );
if ( ! $alt_text ) {
	$alt_text = get_the_title( $image );
}

// Determine if image is decorative.
$is_decorative = ( 'decorative' === $image_role );
?>
<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>" role="region" aria-label="<?php echo esc_attr__( 'Image Block', 'jlbpartners' ); ?>">
	<div class="container">
		<div class="jlb image-block js-image-mask-block">
			<figure class="image-block__figure js-image-mask" <?php echo $is_decorative ? 'aria-hidden="true"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Boolean attribute, safe to output ?>>
				<picture>
					<?php if ( $mobile_image_url ) : ?>
						<source 
							srcset="<?php echo esc_url( $mobile_image_url ); ?>"
							media="(max-width: 767px)"
							width="<?php echo esc_attr( $mobile_image_width ); ?>"
							height="<?php echo esc_attr( $mobile_image_height ); ?>"
						/>
					<?php endif; ?>
					<img
						class="image-block__image"
						src="<?php echo esc_url( $image_url ); ?>"
						alt="<?php echo $is_decorative ? '' : esc_attr( $alt_text ); ?>"
						width="<?php echo esc_attr( $image_width ); ?>"
						height="<?php echo esc_attr( $image_height ); ?>"
						loading="lazy"
						<?php if ( $is_decorative ) : ?>
							role="presentation"
						<?php endif; ?>
					/>
				</picture>
				<?php if ( $image_caption ) : ?>
					<figcaption class="image-block__caption">
						<?php echo esc_html( $image_caption ); ?>
					</figcaption>
				<?php endif; ?>
				<?php if ( ! $is_decorative && $image_description ) : ?>
					<div class="image-block__description visually-hidden">
						<?php echo esc_html( $image_description ); ?>
					</div>
				<?php endif; ?>
			</figure>
		</div>
		
	</div>
</section>
