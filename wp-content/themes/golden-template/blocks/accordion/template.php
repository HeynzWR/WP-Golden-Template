<?php
/**
 * Accordion Block Template
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

// Get field values.
$accordion_items = get_field( 'accordion_items' );

// Create block attributes.
$block_id = 'accordion-' . $block['id'];
	if ( ! empty( $block['anchor'] ) ) {
		$block_id = $block['anchor'];
	}

	// Preview mode handling.
if ( $is_preview && empty( $accordion_items ) ) {
	golden_template_show_block_preview( 'accordion', __( 'Accordion Block', 'golden-template' ) );
	return;
}

// Skip rendering if required fields are empty.
if ( empty( $accordion_items ) ) {
	return;
}
?>
<section id="<?php echo esc_attr( $block_id ); ?>" class="section section--accordion<?php echo ! empty( $block['className'] ) ? ' ' . esc_attr( $block['className'] ) : ''; ?>">
	<div class="jlb accordion js-accordion">
		<?php
		if ( have_rows( 'accordion_items' ) ) :
			$item_index = 0;
			while ( have_rows( 'accordion_items' ) ) :
				the_row();

				$item_title = get_sub_field( 'title' );
				$subtitle   = get_sub_field( 'subtitle' );
				$content    = get_sub_field( 'content' );

				if ( empty( $item_title ) ) {
					continue;
				}

				$item_index++;
				$item_id    = $block_id . '-item-' . $item_index;
				$button_id  = $item_id . '-button';
				$content_id = $item_id . '-content';
				$title_id   = $item_id . '-title';
				?>
				<div class="accordion__item js-accordion-item" data-aos="fade-up">
					<button 
						class="accordion__header js-accordion-btn"
						id="<?php echo esc_attr( $button_id ); ?>"
						aria-expanded="false"
						aria-controls="<?php echo esc_attr( $content_id ); ?>"
						aria-labelledby="<?php echo esc_attr( $title_id ); ?>"
					>
						<div class="accordion__header-wrap">
							<i class="accordion__icon" aria-hidden="true">Icon</i>
							<?php if ( $item_title ) : ?>
								<h3 class="accordion__title" id="<?php echo esc_attr( $title_id ); ?>"><?php echo esc_html( $item_title ); ?></h3>
							<?php endif; ?>
							<?php if ( $subtitle ) : ?>
								<span class="accordion__subtitle"><?php echo esc_html( $subtitle ); ?></span>
							<?php endif; ?>
						</div>
					</button>
					<?php if ( $content ) : ?>
						<div 
							class="accordion__content js-accordion-content"
							id="<?php echo esc_attr( $content_id ); ?>"
							role="region"
							aria-labelledby="<?php echo esc_attr( $title_id ); ?>"
							hidden
						>
							<?php
							// Use restricted HTML tags only (bold, links, lists).
							if ( function_exists( 'golden_template_accordion_get_allowed_html' ) ) {
								echo wp_kses( $content, golden_template_accordion_get_allowed_html() );
							} else {
								// Fallback to wp_kses_post if function doesn't exist.
								echo wp_kses_post( $content );
							}
							?>
						</div>
					<?php endif; ?>
				</div>
			<?php endwhile; ?>
		<?php endif; ?>
	</div>
</section>