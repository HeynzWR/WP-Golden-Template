<?php
/**
 * Hero Section Block Template
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
$hero_title              = get_field( 'hero_title' );
$hero_heading_level      = get_field( 'hero_heading_level' ) ?: 'h1';
$hero_cta_link           = get_field( 'hero_cta_link' );
$background_type         = get_field( 'background_type' ) ?: 'image';
$background_image         = get_field( 'background_image' );
$mobile_background_image  = get_field( 'mobile_background_image' );
$background_video         = get_field( 'background_video' );
$background_video_poster  = get_field( 'background_video_poster' );
$hero_cta_size           = get_field( 'hero_cta_size' ) ?: 'large';

// Accessibility fields for background image
$background_image_role        = get_field( 'background_image_role' ) ?: 'decorative';
$background_image_alt         = get_field( 'background_image_alt' );
$background_image_caption     = get_field( 'background_image_caption' );
$background_image_description = get_field( 'background_image_description' );

// Accessibility fields for background video
$background_video_title        = get_field( 'background_video_title' );
$background_video_description  = get_field( 'background_video_description' );
$background_video_transcript_url = get_field( 'background_video_transcript_url' );

// CREATE BLOCK ATTRIBUTES
$block_id = 'hero-section-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}

$class_name = 'section section--hero';
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}

// PREVIEW MODE HANDLING
// Enhanced preview mode - show image preview if no content.
if ( $is_preview && empty( $hero_title ) ) {
	golden_template_show_block_preview( 'hero-section', __( 'Hero Section Block', 'golden-template' ) );
	return;
}

// SKIP RENDERING IF REQUIRED FIELDS EMPTY
if ( empty( $hero_title ) ) {
	return;
}

// Get background image data
$bg_image_url    = '';
$bg_image_width  = '';
$bg_image_height = '';
$bg_image_alt    = '';

if ( $background_image ) {
	$bg_image_url = wp_get_attachment_image_url( $background_image, 'full' );
	$image_meta   = wp_get_attachment_metadata( $background_image );
	$bg_image_width  = isset( $image_meta['width'] ) ? $image_meta['width'] : 1920;
	$bg_image_height = isset( $image_meta['height'] ) ? $image_meta['height'] : 1080;

	// Auto-populate alt text if not provided
	$bg_image_alt = $background_image_alt ? $background_image_alt : get_post_meta( $background_image, '_wp_attachment_image_alt', true );
	if ( ! $bg_image_alt ) {
		$bg_image_alt = get_the_title( $background_image );
	}
} else {
	// Use placeholder image if no background image is set
	$bg_image_url = golden_template_get_placeholder_image();
	$bg_image_width = 1920;
	$bg_image_height = 1080;
	$bg_image_alt = esc_html__( 'Placeholder image', 'golden-template' );
}

// Get mobile background image data
$mobile_bg_image_url    = '';
$mobile_bg_image_width  = '';
$mobile_bg_image_height = '';

if ( $mobile_background_image ) {
	$mobile_bg_image_url = wp_get_attachment_image_url( $mobile_background_image, 'full' );
	$mobile_image_meta   = wp_get_attachment_metadata( $mobile_background_image );
	$mobile_bg_image_width  = isset( $mobile_image_meta['width'] ) ? $mobile_image_meta['width'] : 768;
	$mobile_bg_image_height = isset( $mobile_image_meta['height'] ) ? $mobile_image_meta['height'] : 1024;
}

// Get background video data
$bg_video_url         = '';
$bg_video_poster_url  = '';
$bg_video_poster_alt  = '';

if ( $background_video ) {
	if ( is_array( $background_video ) ) {
		$bg_video_url = isset( $background_video['url'] ) ? $background_video['url'] : '';
	} else {
		// Backward compatibility: handle string URL format
		$bg_video_url = $background_video;
	}
}

if ( $background_video_poster ) {
	$bg_video_poster_url = wp_get_attachment_image_url( $background_video_poster, 'full' );
	$bg_video_poster_alt = get_post_meta( $background_video_poster, '_wp_attachment_image_alt', true );
	if ( ! $bg_video_poster_alt ) {
		$bg_video_poster_alt = get_the_title( $background_video_poster );
	}
}

// CTA Link details
$cta_url    = '';
$cta_title  = '';
$cta_target = '_self';

if ( $hero_cta_link && is_array( $hero_cta_link ) ) {
	$cta_url    = isset( $hero_cta_link['url'] ) ? $hero_cta_link['url'] : '';
	$cta_title  = isset( $hero_cta_link['title'] ) ? $hero_cta_link['title'] : '';
	$cta_target = isset( $hero_cta_link['target'] ) ? $hero_cta_link['target'] : '_self';
}

// Arrow icon based on size
$arrow_icon = $hero_cta_size === 'small'
	? get_template_directory_uri() . '/assets/images/icons/arrow-small.svg'
	: get_template_directory_uri() . '/assets/images/icons/arrow-large.svg';

// Check if the block immediately below the hero has js-scroll-block class
$has_scroll_block_next = false;
$post_content = get_the_content();
$blocks = parse_blocks( $post_content );

// Find the first non-empty block after the hero (starting from index 1)
$next_block_index = null;
for ( $i = 1; $i < count( $blocks ); $i++ ) {
	$block_name = $blocks[ $i ]['blockName'] ?? '';
	if ( ! empty( $block_name ) ) {
		$next_block_index = $i;
		break;
	}
}

// Check if that block has js-scroll-block class
if ( $next_block_index !== null ) {
	$next_block_html = render_block( $blocks[ $next_block_index ] );
	if ( strpos( $next_block_html, 'js-scroll-block' ) !== false ) {
		$has_scroll_block_next = true;
	}
}

// Build hero wrapper classes
$hero_wrapper_class = 'golden-template hero';
if ( $has_scroll_block_next ) {
	$hero_wrapper_class .= ' js-hero';
}
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>">
	<div class="<?php echo esc_attr( $hero_wrapper_class ); ?>">
		<div class="hero__wrapper">
			<div class="hero__container">
				<?php if ( 'video' === $background_type ) : ?>
					<?php if ( $bg_video_url ) : ?>
						<figure class="hero__figure">
							<video
								class="hero__video"
								autoplay
								muted
								loop
								playsinline
								<?php if ( $bg_video_poster_url ) : ?>
									poster="<?php echo esc_url( $bg_video_poster_url ); ?>"
								<?php endif; ?>
								<?php if ( $background_video_title ) : ?>
									aria-label="<?php echo esc_attr( $background_video_title ); ?>"
								<?php endif; ?>
							>
								<source src="<?php echo esc_url( $bg_video_url ); ?>" type="video/mp4">
								<?php if ( $bg_video_poster_url ) : ?>
									<img 
										class="hero__video-fallback"
										src="<?php echo esc_url( $bg_video_poster_url ); ?>" 
										alt="<?php echo esc_attr( $bg_video_poster_alt ); ?>"
										loading="lazy"
									>
								<?php endif; ?>
							</video>
							<?php if ( $background_video_description ) : ?>
								<figcaption class="visually-hidden">
									<?php echo esc_html( $background_video_description ); ?>
									<?php if ( $background_video_transcript_url ) : ?>
										<a href="<?php echo esc_url( $background_video_transcript_url ); ?>" target="_blank" rel="noopener noreferrer">
											<?php esc_html_e( 'View transcript', 'golden-template' ); ?>
										</a>
									<?php endif; ?>
								</figcaption>
							<?php endif; ?>
						</figure>
					<?php elseif ( $bg_video_poster_url ) : ?>
						<figure class="hero__figure">
							<img 
								src="<?php echo esc_url( $bg_video_poster_url ); ?>" 
								alt="<?php echo esc_attr( $bg_video_poster_alt ); ?>"
								loading="lazy"
								aria-hidden="true"
							>
							<?php if ( $background_video_description ) : ?>
								<figcaption class="visually-hidden">
									<?php echo esc_html( $background_video_description ); ?>
									<?php if ( $background_video_transcript_url ) : ?>
										<a href="<?php echo esc_url( $background_video_transcript_url ); ?>" target="_blank" rel="noopener noreferrer">
											<?php esc_html_e( 'View transcript', 'golden-template' ); ?>
										</a>
									<?php endif; ?>
								</figcaption>
							<?php endif; ?>
						</figure>
					<?php else : ?>
						<?php
						// Fallback to placeholder image if video is required but missing (safety check)
						$placeholder_url = golden_template_get_placeholder_image();
						?>
						<figure class="hero__figure" aria-hidden="true">
							<img 
								src="<?php echo esc_url( $placeholder_url ); ?>" 
								alt="<?php esc_attr_e( 'Placeholder image', 'golden-template' ); ?>"
								loading="lazy"
								role="presentation"
							>
						</figure>
					<?php endif; ?>

				<?php elseif ( 'image' === $background_type ) : ?>
					<figure class="hero__figure" <?php echo 'decorative' === $background_image_role ? 'aria-hidden="true"' : ''; ?>>
						<picture>
							<?php if ( $mobile_bg_image_url ) : ?>
								<source 
									srcset="<?php echo esc_url( $mobile_bg_image_url ); ?>"
									media="(max-width: 767px)"
									width="<?php echo esc_attr( $mobile_bg_image_width ); ?>"
									height="<?php echo esc_attr( $mobile_bg_image_height ); ?>"
								/>
							<?php endif; ?>
							<img
								src="<?php echo esc_url( $bg_image_url ); ?>"
								alt="<?php echo 'decorative' === $background_image_role ? '' : esc_attr( $bg_image_alt ); ?>"
								width="<?php echo esc_attr( $bg_image_width ); ?>"
								height="<?php echo esc_attr( $bg_image_height ); ?>"
								class="hero__image"
								loading="eager"
								<?php if ( 'decorative' === $background_image_role ) : ?>
									role="presentation"
								<?php endif; ?>
							/>
						</picture>
						<?php if ( $background_image_caption ) : ?>
							<figcaption><?php echo esc_html( $background_image_caption ); ?></figcaption>
						<?php endif; ?>
						<?php if ( 'informational' === $background_image_role && $background_image_description && $background_image_description !== $bg_image_alt ) : ?>
							<figcaption class="visually-hidden">
								<?php echo esc_html( $background_image_description ); ?>
							</figcaption>
						<?php endif; ?>
					</figure>
				<?php endif; ?>

				<article>
					<div class="container">

						<?php
						$heading_level = in_array( $hero_heading_level, array( 'h1', 'h2' ), true ) ? $hero_heading_level : 'h1';
						?>
						<<?php echo esc_attr( $heading_level ); ?> class="hero__title" data-aos="fade-up" data-aos-delay="200">
							<?php echo esc_html( $hero_title ); ?>
						</<?php echo esc_attr( $heading_level ); ?>>

						<?php if ( $hero_cta_link && ! empty( $cta_url ) && ! empty( $cta_title ) ) : ?>
							<div class="hero__cta" data-aos="fade-up" data-aos-delay="400">
								<a
									href="<?php echo esc_url( $cta_url ); ?>"
									class="hero__banner-link hero__banner-link--<?php echo esc_attr( $hero_cta_size ); ?>"
									target="<?php echo esc_attr( $cta_target ); ?>"
									<?php if ( '_blank' === $cta_target ) : ?>
										rel="noopener noreferrer"
									<?php endif; ?>
								>
									<?php echo esc_html( $cta_title ); ?>
									<img
										src="<?php echo esc_url( $arrow_icon ); ?>"
										alt=""
										class="hero__icon"
										aria-hidden="true"
									/>
								</a>
							</div>
						<?php endif; ?>

					</div>
				</article>
			</div>
		</div>
	</div>
</section>
