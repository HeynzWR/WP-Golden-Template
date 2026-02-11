<?php
/**
 * Service Slider Block Template
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

// GET FIELD VALUES
$service_slider_title       = get_field( 'service_slider_title' );
$service_slider_description = get_field( 'service_slider_description' );
$service_items              = get_field( 'service_items' );
$learn_more_link            = get_field( 'learn_more_link' );
$remove_padding             = get_field( 'remove_padding' );

// CREATE BLOCK ATTRIBUTES
$block_id = 'service-slider-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}

$class_name = 'services';
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}

// PREVIEW MODE HANDLING
// Enhanced preview mode - show image preview if no content.
if ( $is_preview && empty( $service_items ) ) {
	jlbpartners_show_block_preview( 'service-slider', __( 'Service Slider Block', 'jlbpartners' ) );
	return;
}

if ( empty( $service_items ) ) {
	return;
}

// CTA Link details
$cta_url    = '';
$cta_title  = '';
$cta_target = '_self';

if ( $learn_more_link && is_array( $learn_more_link ) ) {
	$cta_url    = isset( $learn_more_link['url'] ) ? $learn_more_link['url'] : '';
	$cta_title  = isset( $learn_more_link['title'] ) ? $learn_more_link['title'] : 'Learn More';
	$cta_target = isset( $learn_more_link['target'] ) ? $learn_more_link['target'] : '_self';
}
?>

<div id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>">
	<section class="section section--light section--service<?php echo $remove_padding ? ' section--service-secondary' : ''; ?>">
		<div class="service-block<?php echo $remove_padding ? ' service-block--secondary' : ''; ?>" id="about-services">
			<header data-aos="fade-up">
				<div class="container">
					<?php if ( $service_slider_title ) : ?>
						<h2><?php echo esc_html( $service_slider_title ); ?></h2>
					<?php endif; ?>
					<?php if ( $service_slider_description ) : ?>
						<div class="service-block__description">
							<?php echo wp_kses_post( $service_slider_description ); ?>
						</div>
					<?php endif; ?>
				</div>
			</header>
			<div class="service-block__wrapper js-service-wrap">
				<div class="service-block__images" data-aos="fade-up">
					<div class="service-block__image-slider js-service-image-slider">
						<?php foreach ( $service_items as $item ) : ?>
							<?php
							$service_image_id = isset( $item['service_image'] ) ? $item['service_image'] : '';
							$service_mobile_image_id = isset( $item['service_mobile_image'] ) ? $item['service_mobile_image'] : '';
							$service_title    = isset( $item['service_title'] ) ? $item['service_title'] : '';
							$service_description = isset( $item['service_description'] ) ? $item['service_description'] : '';
							
							// Get image data
							$image_url    = '';
							$image_width  = '';
							$image_height = '';
							$image_alt    = '';
							$image_role   = isset( $item['service_image_role'] ) ? $item['service_image_role'] : 'informational';
							$image_alt_text = isset( $item['service_image_alt'] ) ? $item['service_image_alt'] : '';
							
							if ( $service_image_id ) {
								$image_url = wp_get_attachment_image_url( $service_image_id, 'full' );
								$image_meta = wp_get_attachment_metadata( $service_image_id );
								$image_width  = isset( $image_meta['width'] ) ? $image_meta['width'] : 800;
								$image_height = isset( $image_meta['height'] ) ? $image_meta['height'] : 1200;
								
								// Auto-populate alt text if not provided
								$alt_text = $image_alt_text ? $image_alt_text : get_post_meta( $service_image_id, '_wp_attachment_image_alt', true );
								if ( ! $alt_text ) {
									$alt_text = $service_title ? $service_title : get_the_title( $service_image_id );
								}
								$image_alt = $alt_text;
							} else {
								// Use placeholder if no image
								$image_url = jlbpartners_get_placeholder_image();
								$image_width = 800;
								$image_height = 1200;
								$image_alt = $service_title ? $service_title : esc_html__( 'Placeholder image', 'jlbpartners' );
							}
							
							// Get mobile image data
							$mobile_image_url    = '';
							$mobile_image_width  = '';
							$mobile_image_height = '';
							
							if ( $service_mobile_image_id ) {
								$mobile_image_url = wp_get_attachment_image_url( $service_mobile_image_id, 'full' );
								$mobile_image_meta   = wp_get_attachment_metadata( $service_mobile_image_id );
								$mobile_image_width  = isset( $mobile_image_meta['width'] ) ? $mobile_image_meta['width'] : 768;
								$mobile_image_height = isset( $mobile_image_meta['height'] ) ? $mobile_image_meta['height'] : 1024;
							}
							?>
							<div class="service-block__image-item">
								<figure>
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
											src="<?php echo esc_url( $image_url ); ?>" 
											alt="<?php echo 'decorative' === $image_role ? '' : esc_attr( $image_alt ); ?>"
											width="<?php echo esc_attr( $image_width ); ?>"
											height="<?php echo esc_attr( $image_height ); ?>"
											loading="lazy"
											<?php if ( 'decorative' === $image_role ) : ?>
												role="presentation"
											<?php endif; ?>
										/>
									</picture>
								</figure>
								<article>
									<?php if ( $service_title ) : ?>
										<h3><?php echo esc_html( $service_title ); ?></h3>
									<?php endif; ?>
									<?php if ( $service_description ) : ?>
										<?php echo wp_kses_post( $service_description ); ?>
									<?php endif; ?>
								</article>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="service-block__content">
					<div class="service-block__content-slider js-service-content-slider" data-aos="fade-up">
						<?php foreach ( $service_items as $item ) : ?>
							<?php
							$service_title       = isset( $item['service_title'] ) ? $item['service_title'] : '';
							$service_description = isset( $item['service_description'] ) ? $item['service_description'] : '';
							?>
							<div class="service-block__content-item">
								<article data-aos="fade-in">
									<?php if ( $service_title ) : ?>
										<h3><?php echo esc_html( $service_title ); ?></h3>
									<?php endif; ?>
									<?php if ( $service_description ) : ?>
										<?php echo wp_kses_post( $service_description ); ?>
									<?php endif; ?>
								</article>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
			<?php if ( $learn_more_link && ! empty( $cta_url ) && ! empty( $cta_title ) ) : ?>
				<footer class="service-block__cta" data-aos="fade-up">
					<a 
						class="l-btn l-btn--secondary l-btn--sm" 
						href="<?php echo esc_url( $cta_url ); ?>" 
						title="<?php echo esc_attr( $cta_title ); ?>"
						<?php if ( ! empty( $cta_target ) ) : ?>
							target="<?php echo esc_attr( $cta_target ); ?>"
							<?php if ( '_blank' === $cta_target ) : ?>
								rel="noopener noreferrer"
							<?php endif; ?>
						<?php endif; ?>
					>
						<?php echo esc_html( $cta_title ); ?>
					</a>
				</footer>
			<?php endif; ?>
		</div>
	</section>
</div>
