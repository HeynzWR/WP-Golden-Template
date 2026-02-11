<?php
/**
 * Line Block Template
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

$text      = get_field( 'text' );
$show_line = get_field( 'show_line' ) ?? 1;

$block_id = 'line-block-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}

$class_name = 'jlb line-block js-scroll-block';
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}

// Add class if line should be shown
if ( $show_line ) {
	$class_name .= ' line-block--with-line';
}

// PREVIEW MODE HANDLING
// Enhanced preview mode - show image preview if no content.
if ( $is_preview && empty( $text ) ) {
	golden_template_show_block_preview( 'line-block', __( 'Line Block', 'golden-template' ) );
	return;
}

if ( empty( $text ) ) {
	return;
}
?>

<section data-aos="fade-up" data-aos-delay="800" data-duration="800" data-aos-offset="-50">
	<div id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>">
		<div class="container">
			<div class="line-block__content">
				<div class="line-block__text" data-aos="fade-up" data-aos-delay="100">
					<?php echo wp_kses_post( $text ); ?>
					<span class="line-block__line" data-aos="">Line</span>
				</div>
			</div>
		</div>
	</div>
</section>
