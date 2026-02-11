<?php
/**
 * Rich Text Editor Block Template
 *
 * @package GoldenTemplate
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

// GET FIELD VALUES
$content          = get_field( 'content' );
$show_top_line    = get_field( 'show_top_line' );
$show_bottom_line = get_field( 'show_bottom_line' );

// CREATE BLOCK ATTRIBUTES
$block_id = 'rich-text-editor-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}

$class_name = 'jlb rich-text-editor';

if ( $show_top_line ) {
	$class_name .= ' rich-text-editor--has-top-line';
}

if ( $show_bottom_line ) {
	$class_name .= ' rich-text-editor--has-bottom-line';
}

if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}

// PREVIEW MODE HANDLING
if ( $is_preview && empty( $content ) ) {
	golden_template_show_block_preview( 'rich-text-editor', __( 'Rich Text Editor Block', 'golden-template' ) );
	return;
}

// SKIP RENDERING IF REQUIRED FIELDS EMPTY
if ( empty( $content ) ) {
	return;
}

// RENDER OUTPUT
?>
<section class="section section--page-header section--light" id="<?php echo esc_attr( $block_id ); ?>">
	<div class="<?php echo esc_attr( $class_name ); ?>">
		<div class="rich-text-editor__wrapper" data-aos="fade-up">
			<?php if ( $show_top_line ) : ?>
				<div class="rich-text-editor__line rich-text-editor__line--top" aria-hidden="true"></div>
			<?php endif; ?>

			<div class="rich-text-editor__content" data-aos="fade-up">
				<?php 
				// Process shortcodes first
				$processed_content = do_shortcode( $content );
				
				// Check if content contains Contact Form 7 shortcode
				// CF7 outputs its own sanitized HTML, so we trust its sanitization
				$is_cf7_form = has_shortcode( $content, 'contact-form-7' ) || false !== strpos( $processed_content, 'wpcf7' );
				
				if ( $is_cf7_form ) {
					// Contact Form 7 already sanitizes its output, so we output it directly
					// This ensures form elements are not stripped by wp_kses_post
					echo $processed_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CF7 handles its own sanitization
				} else {
					// For regular content, use standard wp_kses_post
					echo wp_kses_post( $processed_content );
				}
				?>
			</div>

			<?php if ( $show_bottom_line ) : ?>
				<div class="rich-text-editor__line rich-text-editor__line--bottom" aria-hidden="true"></div>
			<?php endif; ?>
		</div>
	</div>
</section>
