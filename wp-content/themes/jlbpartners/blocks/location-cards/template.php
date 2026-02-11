<?php
/**
 * Location Cards Block Template
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
$locations = get_field( 'locations' );
$block_title = get_field( 'title' );

// Create block attributes.
$block_id = 'location-cards-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}

$class_name = 'section jlb location-cards js-location-cards';
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}

// Preview mode handling.
if ( $is_preview && empty( $locations ) ) {
	jlbpartners_show_block_preview( 'location-cards', __( 'Location Cards Block', 'jlbpartners' ) );
	return;
}

// Skip rendering if required fields are empty.
if ( empty( $locations ) ) {
	return;	
}
?>
<section id="<?php echo esc_attr( $block_id ); ?>" class="section section--location-cards" data-aos="fade-up" data-aos-delay="800" data-aos-offset="-50" role="region" aria-label="<?php echo esc_attr__( 'Location Cards', 'jlbpartners' ); ?>">
	<div class="container">
		<div class="<?php echo esc_attr( $class_name ); ?>">
			<?php if ( ! empty( $block_title ) ) : ?>
				<header class="location-cards__title-header" data-aos="fade-up" data-aos-delay="100">
					<h2><?php echo esc_html( $block_title ); ?></h2>
				</header>
			<?php endif; ?>

			<ul class="location-cards__list" role="list">
				<?php
				$card_index = 0;
				while ( have_rows( 'locations' ) ) :
					the_row();
					$card_index++;

					$card_title   = get_sub_field( 'title' );
					$card_content = get_sub_field( 'content' );

					if ( empty( $card_title ) || empty( $card_content ) ) {
						continue;
					}

					$card_id = 'location-card-' . $block['id'] . '-' . $card_index;
					// Calculate delay for staggered animation (100ms increments)
					$aos_delay = 200 + ( $card_index * 100 );
					?>
					<li class="location-cards__card js-location-block" data-index="<?php echo esc_attr( $card_index ); ?>" data-aos="fade-up" data-aos-delay="<?php echo esc_attr( $aos_delay ); ?>" role="listitem">
						<article id="<?php echo esc_attr( $card_id ); ?>" class="location-cards__article">
							<header class="location-cards__header js-location-header">
								<h3 class="location-cards__title" id="<?php echo esc_attr( $card_id . '-title' ); ?>">
									<?php echo esc_html( $card_title ); ?>
								</h3>
								<i class="location-cards__toggle"></i>
							</header>
							<div class="location-cards__content js-location-content" aria-labelledby="<?php echo esc_attr( $card_id . '-title' ); ?>">
								<?php echo wp_kses_post( $card_content ); ?>
							</div>
						</article>
					</li>
				<?php endwhile; ?>
			</ul>
		</div>
	</div>
</section>
