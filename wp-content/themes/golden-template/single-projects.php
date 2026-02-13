<?php
/**
 * The template for displaying single project details
 *
 * @package GoldenTemplate
 */

get_header();

// Get project data
$project_id       = get_the_ID();
$project_title    = get_the_title();
$external_link    = get_field( 'project_external_link', $project_id );
$website_url      = ( $external_link && ! empty( $external_link['url'] ) ) ? $external_link['url'] : '';
$project_tags     = get_the_tags( $project_id );

// Get next project for bottom hero
// Use WP_Query instead of get_next_post() for VIP compatibility
$current_post_id = get_the_ID();
$next_post       = null;

// Query for next post
// phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn -- Necessary for navigation, limited to single post
$next_args = array(
	'post_type'      => 'projects',
	'posts_per_page' => 1,
	'post_status'    => 'publish',
	'post__not_in'   => array( $current_post_id ),
	'date_query'     => array(
		array(
			'after' => get_the_date( 'Y-m-d H:i:s', $current_post_id ),
		),
	),
	'order'          => 'ASC',
	'orderby'        => 'date',
);

$next_query = new WP_Query( $next_args );
if ( $next_query->have_posts() ) {
	$next_query->the_post();
	$next_post = get_post();
	wp_reset_postdata();
}

// If no next post, get first post
if ( ! $next_post ) {
	$first_args = array(
		'post_type'      => 'projects',
		'posts_per_page' => 1,
		'post_status'    => 'publish',
		'order'          => 'ASC',
		'orderby'        => 'date',
	);
	$first_post_query = new WP_Query( $first_args );
	if ( $first_post_query->have_posts() ) {
		$first_post_query->the_post();
		$next_post = get_post();
		wp_reset_postdata();
	}
}

$next_project_title   = $next_post ? get_the_title( $next_post->ID ) : '';
$next_project_url     = $next_post ? get_permalink( $next_post->ID ) : '';
$next_project_image   = $next_post ? get_the_post_thumbnail_url( $next_post->ID, 'full' ) : '';
?>

<main id="main" class="site-main">

<div data-barba="wrapper">
	<article id="project-<?php echo esc_attr( $project_id ); ?>" <?php post_class( 'golden-template single-project' ); ?> data-barba="container" data-barba-namespace="project">
		<section class="section section--hero">
			<div class="golden-template hero hero--static hero--detail js-detail-hero">
				<div class="hero__wrapper">
					<div class="hero__container">
						<?php if ( has_post_thumbnail() ) : ?>
							<figure class="hero__figure" aria-hidden="true">
								<picture>
									<?php the_post_thumbnail( 'full' ); ?>
								</picture>
							</figure>
						<?php endif; ?>
					</div>
				
					<article>
						<div class="container container--xxl">
							<h1 class="hero__title"><?php echo esc_html( $project_title ); ?></h1>
							<div class="hero__cta">
								<?php
								$link_url = $website_url ? $website_url : get_permalink( $project_id );
								$link_target = $website_url ? '_blank' : '';
								$link_rel = $website_url ? 'noopener noreferrer' : '';
								?>
								<a href="<?php echo esc_url( $link_url ); ?>" class="hero__banner-link hero__banner-link--large" <?php echo $link_target ? 'target="' . esc_attr( $link_target ) . '"' : ''; ?> <?php echo $link_rel ? 'rel="' . esc_attr( $link_rel ) . '"' : ''; ?>>Website								
								<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/icons/arrow-large.svg' ); ?>" alt="" class="hero__icon" aria-hidden="true">
								</a>
							</div>
						</div>
					</article>
				</div>
			</div>
		</section>

		<section class="section section--project-details section--light">
			<div class="container container--xxl">
				<!-- Project Tags Section -->
				<?php if ( $project_tags && ! is_wp_error( $project_tags ) ) : ?>
					<ul class="golden-template tag-list">
						<?php foreach ( $project_tags as $project_tag ) : ?>
							<li>
								<span class="tag"><?php echo esc_html( $project_tag->name ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<div class="golden-template image-gallery js-image-gallery">
					<?php
					// Get the content and extract gallery shortcode
					$content = get_the_content();
					$gallery_ids = array();
					
					// Parse gallery shortcode to get image IDs
					if ( preg_match( '/\[gallery ids="([^"]+)"\]/', $content, $matches ) ) {
						$gallery_ids = array_map( 'intval', explode( ',', $matches[1] ) );
					}
					
					// If we have gallery images, render them in repeating pattern
					if ( ! empty( $gallery_ids ) ) :
						$image_count = count( $gallery_ids );
						$current_index = 0;
						
						// Pattern: full-width, 2 half-width, full-width (repeats)
						while ( $current_index < $image_count ) :
							$pattern_position = $current_index % 4;
							
							// Position 0 or 3: Full-width image
							if ( $pattern_position === 0 || $pattern_position === 3 ) :
								$image_id = $gallery_ids[ $current_index ];
								$image_url = wp_get_attachment_image_url( $image_id, 'full' );
								$image_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
								$image_caption = wp_get_attachment_caption( $image_id );
								if ( $image_url ) :
					?>
					<div class="row">
						<div class="col">
							<figure class="image-gallery__figure js-gallery-images" <?php echo $image_caption ? 'data-caption="' . esc_attr( $image_caption ) . '"' : ''; ?>>
								<picture>
									<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" width="1728" height="1033" class="image-gallery__image" role="presentation">
								</picture>
							</figure>
						</div>
					</div>
					<?php
								endif;
								$current_index++;
							
							// Position 1: Start half-width row (2 images)
							elseif ( $pattern_position === 1 ) :
					?>
					<div class="row">
						<?php
						// Render up to 2 images in this row
						for ( $i = 0; $i < 2 && $current_index < $image_count; $i++, $current_index++ ) :
							$image_id = $gallery_ids[ $current_index ];
							$image_url = wp_get_attachment_image_url( $image_id, 'full' );
							$image_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
							$image_caption = wp_get_attachment_caption( $image_id );
							if ( $image_url ) :
						?>
						<div class="col">
							<figure class="image-gallery__figure js-gallery-images">
								<picture>
									<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" width="1728" height="1033" class="image-gallery__image" role="presentation">
								</picture>
							</figure>
						</div>
						<?php
							endif;
						endfor;
						?>
					</div>
					<?php
							endif;
							
						endwhile;
						
					endif; // End if gallery_ids not empty
					?>
				</div>
			</div>
		</section>
		
		<!-- Bottom Hero Section - Next Project -->
		<?php if ( $next_post ) : ?>

			<section class="section section--next-hero section--light">
				<div class="golden-template hero hero--static hero--preview js-hero-preview">
					<span class="hero-preview__overlay hero-preview__overlay--gradient hero-preview__overlay--gradient--top js-hero-preview-overlay" style="--alpha: 0.01;" aria-hidden="true">Overlay</span>

					<span class="hero-preview__overlay hero-preview__overlay--gradient hero-preview__overlay--gradient--bottom js-hero-preview-overlay" style="--alpha: 0.01;" aria-hidden="true">Overlay</span>

					<span class="hero-preview__overlay hero-preview__overlay--default js-hero-preview-overlay-default" style="--alpha: 0.01;" aria-hidden="true">Overlay</span>
					<div class="hero__wrapper">
						<div class="hero__container">
							<figure class="hero__figure" aria-hidden="true">
								<picture>
									<img src="<?php echo esc_url( $next_project_image ); ?>" alt="<?php echo esc_attr( $next_project_title ); ?>" width="1728" height="1033" role="presentation">
								</picture>
							</figure>
						</div>
					
						<article class="js-hero-preview-text">
							<div class="container container--xxl">
								<h1 class="hero__title"><?php echo esc_html( $next_project_title ); ?></h1>
								<div class="hero__cta">
									<a href="<?php echo esc_url( $next_project_url ); ?>" class="hero__banner-link hero__banner-link--large js-next-project-link"><?php esc_html_e( 'Next Project', 'golden-template' ); ?>								
									</a>
								</div>
							</div>
						</article>
					</div>
				</div>
			</section>
		<?php endif; ?>

	</article>

<?php
get_footer();
?>