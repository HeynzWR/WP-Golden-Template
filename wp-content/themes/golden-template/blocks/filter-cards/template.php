<?php
/**
 * Filter Cards Block Template
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
$heading         = get_field( 'heading' );
$leadership_cards = get_field( 'leadership_cards' );

// Create block attributes.
$block_id = 'filter-cards-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}

$class_name = 'section section--filter-cards jsFilterCards';
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}

// Preview mode handling.
if ( $is_preview && empty( $leadership_cards ) ) {
	golden_template_show_block_preview( 'filter-cards', __( 'Filter Cards Block', 'golden-template' ) );
	return;
}

// Skip rendering if required fields are empty.
if ( empty( $leadership_cards ) ) {
	return;
}

// Extract unique tags from cards and sort alphabetically.
$tags = array();
foreach ( $leadership_cards as $card ) {
	if ( ! empty( $card['tag'] ) ) {
		$tags[] = $card['tag'];
	}
}
$tags = array_unique( $tags );
sort( $tags );

// Get tags that actually have cards (filter out empty tags).
$active_tags = array();
foreach ( $tags as $filter_tag ) {
	foreach ( $leadership_cards as $card ) {
		if ( ! empty( $card['tag'] ) && $card['tag'] === $filter_tag ) {
			$active_tags[] = $filter_tag;
			break;
		}
	}
}
$active_tags = array_unique( $active_tags );
sort( $active_tags );

// No tag is active by default - show all cards.
$default_active_tag = '';
?>
<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>" role="region" aria-label="<?php echo esc_attr__( 'Filter Cards', 'golden-template' ); ?>">
	<div class="container">
		<div class="jlb filter-blocks">
			<?php if ( ! empty( $heading ) ) : ?>
				<h2 class="filter-blocks__heading" data-aos="fade-up"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>

			<?php if ( ! empty( $active_tags ) ) : ?>
				<ul class="filter-blocks__nav" aria-label="<?php echo esc_attr__( 'Filter options', 'golden-template' ); ?>" data-aos="fade-up">
					<?php
					$filter_index = 0;
					foreach ( $active_tags as $filter_tag ) :
						$filter_index++;
						$filter_id     = 'filter-' . $block['id'] . '-' . $filter_index;
						$is_active     = ( $filter_tag === $default_active_tag );
						$active_class  = $is_active ? ' filter-blocks__nav-button--active' : '';
						?>
						<li>
							<button
								type="button"
								id="<?php echo esc_attr( $filter_id ); ?>"
								class="filter-blocks__nav-button jsFilterCardsFilter<?php echo esc_attr( $active_class ); ?>"
								data-filter="<?php echo esc_attr( $filter_tag ); ?>"
								aria-pressed="<?php echo $is_active ? 'true' : 'false'; ?>"
							>
								<?php echo esc_html( $filter_tag ); ?>
							</button>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<div class="filter-blocks__grid" role="list">
				<?php
				$card_index = 0;
				foreach ( $leadership_cards as $card ) :
					$card_index++;

					$card_title    = ! empty( $card['title'] ) ? $card['title'] : '';
					$card_subtitle = ! empty( $card['subtitle'] ) ? $card['subtitle'] : '';
					$card_content  = ! empty( $card['content'] ) ? $card['content'] : '';
					$card_tag      = ! empty( $card['tag'] ) ? $card['tag'] : '';

				// Skip cards with missing required fields.
				if ( empty( $card_title ) || empty( $card_subtitle ) || empty( $card_tag ) ) {
					continue;
				}

				// All cards are visible by default (no filter active).
				$visible_class = '';

					$card_id = 'filter-card-' . $block['id'] . '-' . $card_index;
					?>
					<div
						class="filter-blocks__col jsFilterCardsCard<?php echo esc_attr( $visible_class ); ?>"
						data-tag="<?php echo esc_attr( $card_tag ); ?>"
					>
						<article
							id="<?php echo esc_attr( $card_id ); ?>"
							class="filter-blocks__card jsCardHover"
		
							data-card-title="<?php echo esc_attr( $card_title ); ?>"
							data-card-subtitle="<?php echo esc_attr( $card_subtitle ); ?>"
							data-card-content="<?php echo esc_attr( wp_strip_all_tags( $card_content ) ); ?>"
							data-aos="fade-up"
							tabindex="0"
						>
							<h3 class="filter-blocks__title" id="<?php echo esc_attr( $card_id . '-title' ); ?>">
								<?php echo esc_html( $card_title ); ?>
							</h3>
							<span class="filter-blocks__subtitle" id="<?php echo esc_attr( $card_id . '-subtitle' ); ?>">
								<?php echo esc_html( $card_subtitle ); ?>
							</span>
						</article>
					</div>
				<?php endforeach; ?>
			</div>

			<!-- Hover Popup Modal -->
			<span class="overlay-popup js-popup-overlay" aria-hidden="true">Popup Overlay</span>
			<div class="jlb popup jsCardPopup" role="tooltip" aria-hidden="true">
				<button class="popup__close jsPopupClose" type="button" aria-label="Close popup">
					<i>
						<svg width="35" height="35" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M33.4141 1.41418L1.41406 33.4142M1.41406 1.41418L33.4141 33.4142" stroke="#EBE8DA" stroke-width="4" stroke-linejoin="round"/>
						</svg>
					</i>
				</button>
				<div class="popup__content">
					<h3 class="popup__title jsPopupTitle">John Doe</h3>
					<p class="popup__subtitle jsPopupSubtitle">Role</p>
					<span class="popup__line">Line</span>
					<article class="popup__text jsPopupContent">
						<p>Lorem ipsum dolor sit amet consectetur. Leo in scelerisque vestibulum sed. Tempus ut blandit id sagittis cursus etiam velit commodo. Arcu pulvinar a lacus amet et at at elementum non. Nibh ornare ac nibh viverra porta in leo morbi. Urna felis tempus aliquam facilisis sed id porttitor a. Amet eget massa platea aliquet sit nulla nisl aliquam. In consectetur ac nunc placerat mauris. Fusce sit pulvinar mauris egestas.
					</article>
				</div>
			</div>
		</div>
	</div>
</section>
