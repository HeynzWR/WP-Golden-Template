<?php
/**
 * Project Card Block Template
 *
 * @package JLBPartners
 *
 * @param array  $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool   $is_preview True during AJAX preview.
 * @param int    $post_id The post ID this block is saved to.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// ACF fields - now everything is directly from fields
$project_card_layout       = get_field('project_card_layout') ?: 'image-left';
$project_card_image        = get_field('project_card_image');
$project_card_mobile_image = get_field('project_card_mobile_image');
$project_card_title        = get_field('project_card_title');
$project_card_subtitle     = get_field('project_card_subtitle');
$project_card_description  = get_field('project_card_description');
$project_card_cta          = get_field('project_card_cta');

// Block ID
$block_id = 'project-card-' . $block['id'];
if ( ! empty($block['anchor']) ) {
  $block_id = $block['anchor'];
}

// Block class with layout modifier
$class_name = 'jlb project-card project-card--' . esc_attr($project_card_layout);
if ( ! empty($block['className']) ) {
  $class_name .= ' ' . $block['className'];
}

// Preview fallback
if ($is_preview && empty($project_card_title)) {
  echo '<div style="padding:20px; background:#f6f6f6; border:1px dashed #00a400;">';
  echo '<strong>Project Card:</strong> Select a project or add content manually';
  echo '</div>';
  return;
}

if (empty($project_card_title)) return;

// Get image data
$image_url    = '';
$image_width  = '';
$image_height = '';
$image_alt    = '';

if ( $project_card_image ) {
	$image_url = wp_get_attachment_image_url( $project_card_image, 'full' );
	$image_meta = wp_get_attachment_metadata( $project_card_image );
	$image_width  = isset( $image_meta['width'] ) ? $image_meta['width'] : 800;
	$image_height = isset( $image_meta['height'] ) ? $image_meta['height'] : 1000;

	// Get alt text
	$image_alt = get_post_meta( $project_card_image, '_wp_attachment_image_alt', true );
	if ( ! $image_alt ) {
		$image_alt = get_the_title( $project_card_image );
	}
} else {
	// Use placeholder image if no image is set
	$image_url = jlbpartners_get_placeholder_image();
	$image_width = 800;
	$image_height = 1000;
	$image_alt = $project_card_title ? $project_card_title : esc_html__( 'Project image', 'jlbpartners' );
}

// Get mobile image data
$mobile_image_url    = '';
$mobile_image_width  = '';
$mobile_image_height = '';

if ( $project_card_mobile_image ) {
	$mobile_image_url = wp_get_attachment_image_url( $project_card_mobile_image, 'full' );
	$mobile_image_meta   = wp_get_attachment_metadata( $project_card_mobile_image );
	$mobile_image_width  = isset( $mobile_image_meta['width'] ) ? $mobile_image_meta['width'] : 768;
	$mobile_image_height = isset( $mobile_image_meta['height'] ) ? $mobile_image_meta['height'] : 1024;
}

// CTA Link details
$cta_url = $project_card_cta ? $project_card_cta['url'] : '';
$cta_title = $project_card_cta ? $project_card_cta['title'] : '';
$cta_target = $project_card_cta && isset($project_card_cta['target']) ? $project_card_cta['target'] : '_self';
?>

<section id="<?php echo esc_attr($block_id); ?>" class="<?php echo esc_attr($class_name); ?>">
    <div class="container">
        <div class="project-card__wrapper">
            
            <!-- Image -->
            <figure class="project-card__figure">
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
						src="<?php echo esc_url($image_url); ?>" 
						alt="<?php echo esc_attr($image_alt); ?>"
						width="<?php echo esc_attr( $image_width ); ?>"
						height="<?php echo esc_attr( $image_height ); ?>"
						loading="lazy"
					/>
				</picture>
            </figure>

            <!-- Content -->
            <div class="project-card__content">
                
                <!-- Header: Title & Subtitle -->
                <header class="project-card__header">
                    <h2 class="project-card__title">
                        <?php echo esc_html($project_card_title); ?>
                    </h2>

                    <?php if ($project_card_subtitle) : ?>
                        <h3 class="project-card__subtitle">
                            <?php echo esc_html($project_card_subtitle); ?>
                        </h3>
                    <?php endif; ?>
                </header>

                <!-- Footer: Description & CTA (with ::before line) -->
                <footer class="project-card__footer">
                    <?php if ($project_card_description) : ?>
                        <p class="project-card__description">
                            <?php echo esc_html($project_card_description); ?>
                        </p>
                    <?php endif; ?>

                    <?php if ($cta_url && $cta_title) : ?>
                        <div class="project-card__cta">
                            <a 
                                href="<?php echo esc_url($cta_url); ?>" 
                                class="project-card__cta-link"
                                target="<?php echo esc_attr($cta_target); ?>"
                            >
                                <?php echo esc_html($cta_title); ?>
                                <img 
                                    src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/icons/small-arrow.svg'); ?>" 
                                    alt="" 
                                    class="project-card__cta-icon"
                                />
                            </a>
                        </div>
                    <?php endif; ?>
                </footer>

            </div>

        </div>
    </div>
</section>