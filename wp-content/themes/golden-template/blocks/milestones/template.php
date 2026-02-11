<?php
/**
 * Milestones Block Template
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
$milestone_items = get_field( 'milestone_items' );

// Create block attributes.
$block_id = 'milestones-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}

$class_name = 'section section--light section--milestones';
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}

// Preview mode handling.
if ( $is_preview && empty( $milestone_items ) ) {
	golden_template_show_block_preview( 'milestones', __( 'Milestones Block', 'golden-template' ) );
	return;
}

// Skip rendering if required fields are empty.
if ( empty( $milestone_items ) ) {
	return;
}
?>
<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>" role="region" aria-label="<?php esc_attr_e( 'Milestones Timeline', 'golden-template' ); ?>">
	<div class="jlb milestones">
		<div class="milestones__container">
			<div class="milestones__wrapper js-milestones-wrapper">
				<div class="milestones__col milestones__col--date" data-aos="fade-up">
					<div class="milestones__date-slide js-milestones-date-slide">
						<?php
						if ( have_rows( 'milestone_items' ) ) :
							while ( have_rows( 'milestone_items' ) ) :
								the_row();
								$milestone_label = get_sub_field( 'label' );
								if ( ! empty( $milestone_label ) ) :
									?>
									<div class="milestones__date-slide-item">
										<h3><?php echo esc_html( $milestone_label ); ?></h3>
									</div>
									<?php
								endif;
							endwhile;
						endif;
						?>
					</div>
				</div>
				<div class="milestones__col milestones__col--data" data-aos="fade-up">
					<div class="milestones__data-slide js-milestones-data-slide">
						<?php
						if ( have_rows( 'milestone_items' ) ) :
							while ( have_rows( 'milestone_items' ) ) :
								the_row();
								$milestone_title = get_sub_field( 'title' );
								if ( ! empty( $milestone_title ) ) :
									?>
									<div class="milestones__data-slide-item">
										<?php echo wp_kses_post( $milestone_title ); ?>
									</div>
									<?php
								endif;
							endwhile;
						endif;
						?>
					</div>
				</div>
			</div>
		</div>
		
	</div>

	<ul class="jlb-milestones jsMilestones" role="list" style="display: none;">
		<?php
		$item_index     = 0;
		$first_item     = true;
		while ( have_rows( 'milestone_items' ) ) :
			the_row();
			$item_index++;

			$milestone_label = get_sub_field( 'label' );
			$milestone_title = get_sub_field( 'title' );

			if ( empty( $milestone_label ) || empty( $milestone_title ) ) {
				continue;
			}

			// First item is always active by default.
			$is_active = $first_item;
			$first_item = false;

			// Store title content in data attribute for JavaScript to use.
			$title_content = wp_kses_post( $milestone_title );

			$item_class = 'jlb-milestones__item jsMilestoneItem';
			if ( $is_active ) {
				$item_class .= ' jlb-milestones__item--active';
			}
			?>
			<li class="<?php echo esc_attr( $item_class ); ?>" data-index="<?php echo esc_attr( $item_index ); ?>" data-title="<?php echo esc_attr( $title_content ); ?>" role="listitem">
				<button 
					class="jlb-milestones__trigger jsMilestoneTrigger" 
					type="button"
					aria-expanded="<?php echo $is_active ? 'true' : 'false'; ?>"
					aria-controls="milestone-content-<?php echo esc_attr( $block['id'] . '-' . $item_index ); ?>"
					id="milestone-trigger-<?php echo esc_attr( $block['id'] . '-' . $item_index ); ?>"
				>
					<div class="jlb-milestones__label">
						<span class="jlb-milestones__label-text"><?php echo esc_html( $milestone_label ); ?></span>
					</div>
					<?php if ( $is_active ) : ?>
						<div class="jlb-milestones__title jsMilestoneTitle">
							<?php echo wp_kses_post( $milestone_title ); ?>
						</div>
					<?php endif; ?>
				</button>
			</li>
		<?php endwhile; ?>
	</ul>
</section>
