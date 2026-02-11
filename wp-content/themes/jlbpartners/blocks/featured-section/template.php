<?php
/**
 * Featured Section Block Template
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
$section_title     = get_field( 'featured_projects_title' );
$display_mode      = get_field( 'display_mode' ) ?: 'all_featured';
$selected_projects = get_field( 'selected_projects' );

// Retrieve Projects based on mode
$projects = array();

if ( 'manual' === $display_mode && ! empty( $selected_projects ) ) {
	$projects = $selected_projects;
} else {
	// Default to 'all_featured' - query 5 latest published projects
	$args = array(
		'post_type'        => 'projects',
		'posts_per_page'   => 5,
		'post_status'      => 'publish',
		'orderby'          => 'date',
		'order'            => 'DESC',
		'no_found_rows'    => true,
		'suppress_filters' => false, // Enable caching for VIP compatibility.
	);
	// phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_posts_get_posts -- suppress_filters is set to false for proper caching.
	$projects = get_posts( $args );
}

// Set default title if empty
if ( empty( $section_title ) ) {
	$section_title = 'Featured Projects';
}

// Preview mode handling.
if ( $is_preview && empty( $projects ) ) {
	jlbpartners_show_block_preview( 'featured-section', __( 'Featured Section Block', 'jlbpartners' ) );
	return;
}
?>

<?php if ( $projects ) : ?>
<section class="jlb featured-projects">
    <div class="container">
        <h2 class="featured-projects__title" data-aos="fade-up"><?php echo esc_html( $section_title ); ?></h2>
    </div>
    <div class="featured-projects__wrapper">
        <div class="featured-projects__panels js-featured-panel"  data-aos="fade-up">
            <?php foreach ( $projects as $index => $project ) :
                $project_id = $project->ID;
                $name       = get_the_title( $project_id );
                $location   = get_field('project_location', $project_id);
                $units      = get_field('project_units', $project_id);
                $image_url  = get_the_post_thumbnail_url( $project_id, 'full' );
                
                if ( ! $image_url ) {
                    $image_url = jlbpartners_get_placeholder_image();
                }

                // Link logic: Override or permalink
                $external_link = get_field('project_external_link', $project_id);
                $link_url      = $external_link ? $external_link['url'] : get_permalink( $project_id );
                $link_target   = $external_link ? $external_link['target'] : '';
                $link_title    = $external_link ? $external_link['title'] : 'View Project';

                // Set first item as active
                $active_class = ( $index === 0 ) ? 'active' : '';
            ?>

            <!-- Card <?php echo esc_html( $index + 1 ); ?> -->
            <div class="featured-projects__card js-featured-panel-card <?php echo esc_attr( $active_class ); ?>">
                <a href="<?php echo esc_url( $link_url ); ?>" class="featured-projects__link-mobile"
                    <?php if ( !empty( $link_target ) ) : ?> target="<?php echo esc_attr( $link_target ); ?>" <?php endif; ?>>
                    <span><?php echo esc_html( $link_title ); ?></span>
                </a>
                <!-- Article (Always Visible) -->
                <article class="featured-projects__article js-featured-panel-article">
                    <button class="featured-projects__btn" type="button" aria-label="View <?php echo esc_attr( $name ); ?>">

                        <!-- Description -->
                        <?php if ( $location || $units ) : ?>
                        <div class="featured-projects__description">
                            <?php if ( $units ) : ?>
                                <span class="featured-projects__units"><?php echo esc_html( $units ); ?></span>
                            <?php endif; ?>

                            <?php if ( $location ) : ?>
                                <span class="featured-projects__location"><?php echo esc_html( $location ); ?></span>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Heading -->
                        <div class="featured-projects__heading">
                            <h3 class="featured-projects__name"><?php echo esc_html( $name ); ?></h3>
                        </div>

                    </button>
                </article>

                <!-- Image (Shows on Active) -->
                <figure class="featured-projects__figure js-featured-panel-figure" style="--width:100%">
                    <a href="<?php echo esc_url( $link_url ); ?>" class="fill-link"
                        <?php if ( !empty( $link_target ) ) : ?> target="<?php echo esc_attr( $link_target ); ?>" <?php endif; ?>>
                        <?php echo esc_html( $link_title ); ?>
                    </a>
                    <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $name ); ?>">
                </figure>
                <a href="<?php echo esc_url( $link_url ); ?>" class="featured-projects__link"
                    <?php if ( !empty( $link_target ) ) : ?> target="<?php echo esc_attr( $link_target ); ?>" <?php endif; ?>>
                    <span><?php echo esc_html( $link_title ); ?></span>
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_4949_1535)">
                        <path d="M1.5 16.5L16.5 1.5M16.5 1.5V15M16.5 1.5H3" stroke="#EBE8DA" style="stroke:#EBE8DA;stroke:color(display-p3 0.9200 0.9117 0.8534);stroke-opacity:1;" stroke-width="1.75"/>
                        </g>
                        <defs>
                        <clipPath id="clip0_4949_1535">
                        <rect width="18" height="18" fill="white" style="fill:white;fill-opacity:1;"/>
                        </clipPath>
                        </defs>
                    </svg>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>