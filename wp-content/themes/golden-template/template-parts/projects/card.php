<?php
/**
 * Template part for displaying a single project card
 *
 * @package GoldenTemplate
 * 
 * Expected variables:
 * @var int $index Card index for alternating layout
 * @var string $arrow_icon_url URL to the arrow icon
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Extract variables from args
$index          = isset( $args['index'] ) ? $args['index'] : 0;
$arrow_icon_url = isset( $args['arrow_icon_url'] ) ? $args['arrow_icon_url'] : '';

// Get project data
$project_id  = get_the_ID();
$location    = get_field( 'project_location', $project_id ) ?: '';
$units       = get_field( 'project_units', $project_id ) ?: '';
$image_url   = get_the_post_thumbnail_url( $project_id, 'full' ) ?: '';
$project_title = get_the_title() ?: '';
// Only use excerpt if explicitly set, otherwise leave empty
$description = has_excerpt( $project_id ) ? get_the_excerpt( $project_id ) : '';

// Link logic: Override or permalink
$external_link = get_field( 'project_external_link', $project_id );
$link_url      = ( $external_link && ! empty( $external_link['url'] ) ) ? $external_link['url'] : get_permalink( $project_id );
$link_target   = ( $external_link && ! empty( $external_link['target'] ) ) ? $external_link['target'] : '_self';
$link_title    = ( $external_link && ! empty( $external_link['title'] ) ) ? $external_link['title'] : 'View project';

// Alternate layout: even index = image left, odd index = image right
$layout_class = ( $index % 2 === 0 ) ? 'image-right' : 'image-left';


?>

<div class="projects-grid__item projects-grid__item--<?php echo esc_attr( $layout_class ); ?>" data-aos="fade-up">
	<div class="projects-grid__wrapper">
		<?php if ( $link_url ) : ?>
			<a 
				href="<?php echo esc_url( $link_url ); ?>" 
				class="fill-link"
				target="<?php echo esc_attr( $link_target ); ?>"
				aria-label="<?php echo esc_attr( $link_title . ' - ' . $project_title ); ?>"
			>
				<?php echo esc_html( $link_title ); ?>
			</a>
		<?php endif; ?>
		
		<!-- Image Column -->
		<figure class="projects-grid__figure">
			<?php if ( $image_url ) : ?>
				<img 
					src="<?php echo esc_url( $image_url ); ?>" 
					alt="<?php echo esc_attr( $project_title ); ?>"
					loading="lazy"
				>
			<?php else : ?>
				<div class="projects-grid__placeholder">
					<span><?php esc_html_e( 'No image', 'golden-template' ); ?></span>
				</div>
			<?php endif; ?>
		</figure>

		<!-- Content Column -->
		<div class="projects-grid__content">
			<header class="projects-grid__header" data-aos="fade-up">
				<h2 class="projects-grid__title">
					<?php echo esc_html( $project_title ); ?>
				</h2>
				
				<?php if ( $location || $units ) : ?>
					<div class="projects-grid__meta">
						<?php if ( $location ) : ?>
							<span class="projects-grid__location"><?php echo esc_html( $location ); ?></span>
						<?php endif; ?>
						<?php if ( $units ) : ?>
							<span class="projects-grid__units"><?php echo esc_html( $units ); ?> Units</span>
						<?php endif; ?>
					</div>
					<span class="projects-grid__line" data-aos=""></span>
				<?php endif; ?>
			</header>

			<div class="projects-grid__footer" data-aos="fade-up">
				<?php if ( $description ) : ?>
					<p class="projects-grid__description">
						<?php echo esc_html( $description ); ?>
					</p>
				<?php endif; ?>

				<?php if ( $link_url ) : ?>
					<div class="projects-grid__cta">
						<a 
							href="<?php echo esc_url( $link_url ); ?>" 
							class="projects-grid__cta-link"
							target="<?php echo esc_attr( $link_target ); ?>"
							aria-label="<?php echo esc_attr( $link_title . ' - ' . $project_title ); ?>"
						>
							<?php echo esc_html( $link_title ); ?>
							<img 
								src="<?php echo esc_url( $arrow_icon_url ); ?>" 
								alt="" 
								class="projects-grid__cta-icon"
							/>
						</a>
					</div>
				<?php endif; ?>
			</div>
		</div>

	</div>
</div>
