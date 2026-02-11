<?php
/**
 * About Section Block Template
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
$about_title = get_field( 'about_title' );
$about_stats = get_field( 'about_stats' );
$about_text  = get_field( 'about_text' );
$about_btn   = get_field( 'about_btn' );
$about_image = get_field( 'about_image' );
$about_mobile_image = get_field( 'about_mobile_image' );
$about_content_image_alignment = get_field( 'about_content_image_alignment' ) ?: 'content_left';

// Accessibility fields for image
$about_image_role        = get_field( 'about_image_role' ) ?: 'informational';
$about_image_alt         = get_field( 'about_image_alt' );
$about_image_caption      = get_field( 'about_image_caption' );
$about_image_description  = get_field( 'about_image_description' );

// CREATE BLOCK ATTRIBUTES
$block_id = 'about-section-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}

// Block class - PRESERVE EXISTING CSS CLASSES
$class_name = 'jlb about';
if ( 'image_left' === $about_content_image_alignment ) {
	$class_name .= ' about--reverse';
}
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}

// PREVIEW MODE HANDLING
// Enhanced preview mode - show image preview if no content.
if ( $is_preview && empty( $about_title ) && empty( $about_stats ) && empty( $about_text ) ) {
	golden_template_show_block_preview( 'about-section', __( 'About Section Block', 'golden-template' ) );
	return;
}

// Get image data
$image_url    = '';
$image_width  = '';
$image_height = '';
$image_alt    = '';

if ( $about_image ) {
	$image_url = wp_get_attachment_image_url( $about_image, 'full' );
	$image_meta = wp_get_attachment_metadata( $about_image );
	$image_width  = isset( $image_meta['width'] ) ? $image_meta['width'] : 800;
	$image_height = isset( $image_meta['height'] ) ? $image_meta['height'] : 600;

	// Auto-populate alt text if not provided
	$image_alt = $about_image_alt ? $about_image_alt : get_post_meta( $about_image, '_wp_attachment_image_alt', true );
	if ( ! $image_alt ) {
		$image_alt = get_the_title( $about_image );
	}
} else {
	// Use placeholder image if no image is set
	$image_url = golden_template_get_placeholder_image();
	$image_width = 800;
	$image_height = 600;
	$image_alt = $about_image_alt ? $about_image_alt : esc_html__( 'Placeholder image', 'golden-template' );
}

// Get mobile image data
$mobile_image_url    = '';
$mobile_image_width  = '';
$mobile_image_height = '';

if ( $about_mobile_image ) {
	$mobile_image_url = wp_get_attachment_image_url( $about_mobile_image, 'full' );
	$mobile_image_meta   = wp_get_attachment_metadata( $about_mobile_image );
	$mobile_image_width  = isset( $mobile_image_meta['width'] ) ? $mobile_image_meta['width'] : 768;
	$mobile_image_height = isset( $mobile_image_meta['height'] ) ? $mobile_image_meta['height'] : 1024;
}

// CTA Link details
$cta_url    = '';
$cta_title  = '';
$cta_target = '_self';

if ( $about_btn && is_array( $about_btn ) ) {
	$cta_url    = isset( $about_btn['url'] ) ? $about_btn['url'] : '';
	$cta_title  = isset( $about_btn['title'] ) ? $about_btn['title'] : '';
	$cta_target = isset( $about_btn['target'] ) ? $about_btn['target'] : '_self';
}
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>">
	<div class="container container--lg">
		<?php if ( $about_title ) : ?>
		<div class="about__header">
			<h2 class="about__title" data-aos="fade-up"><?php echo esc_html( $about_title ); ?></h2>
		</div>
		<?php endif; ?>

		<?php if ( $about_stats ) : ?>
		<div class="about__stats" data-aos="fade-up">
			<?php foreach ( $about_stats as $stat ) : ?>
				<div class="about__stat">
					<?php if ( ! empty( $stat['stat_value'] ) ) : ?>
						<span class="about__stat-value"><?php echo esc_html( $stat['stat_value'] ); ?></span>
					<?php endif; ?>
					
					<?php if ( ! empty( $stat['stat_label'] ) ) : ?>
						<span class="about__stat-label"><?php echo esc_html( $stat['stat_label'] ); ?></span>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>

		<div class="about__content">
			
			<div class="about__text" data-aos="fade-up">
				<?php if ( $about_text ) : ?>
					<?php echo wp_kses_post( $about_text ); ?>
				<?php endif; ?>

				<div class="about__btn-container" data-aos="fade-up">
				<?php if ( $about_btn && ! empty( $cta_url ) && ! empty( $cta_title ) ) : ?>
					<a 
						href="<?php echo esc_url( $cta_url ); ?>" 
						class="l-btn l-btn--light-hover"
						<?php if ( ! empty( $cta_target ) ) : ?>
							target="<?php echo esc_attr( $cta_target ); ?>"
							<?php if ( '_blank' === $cta_target ) : ?>
								rel="noopener noreferrer"
							<?php endif; ?>
						<?php endif; ?>
					>
						<?php echo esc_html( $cta_title ); ?>
					</a>
				<?php endif; ?>
				</div>  
			</div>

			<figure class="about__image" <?php echo 'decorative' === $about_image_role ? 'aria-hidden="true"' : ''; ?> data-aos="fade-up">
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
						alt="<?php echo 'decorative' === $about_image_role ? '' : esc_attr( $image_alt ); ?>"
						width="<?php echo esc_attr( $image_width ); ?>"
						height="<?php echo esc_attr( $image_height ); ?>"
						loading="lazy"
						<?php if ( 'decorative' === $about_image_role ) : ?>
							role="presentation"
						<?php endif; ?>
					/>
				</picture>
				<?php if ( $about_image_caption ) : ?>
					<figcaption><?php echo esc_html( $about_image_caption ); ?></figcaption>
				<?php endif; ?>
				<?php if ( 'informational' === $about_image_role && $about_image_description && $about_image_description !== $image_alt ) : ?>
					<figcaption class="visually-hidden">
						<?php echo esc_html( $about_image_description ); ?>
					</figcaption>
				<?php endif; ?>
			</figure>

		</div>
	</div>
</section>
