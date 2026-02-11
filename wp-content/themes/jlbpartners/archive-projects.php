<?php
/**
 * The template for displaying project archives
 *
 * @package JLBPartners
 */

get_header();

// Get project listing settings
$projects_page_title = get_option( 'jlbpartners_projects_page_title', 'All Projects' );

// Get current filters from URL - sanitize input
// phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reading URL parameters, sanitized below
$selected_states_raw = isset( $_GET['states'] ) ? wp_unslash( $_GET['states'] ) : '';
// phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reading URL parameters, sanitized below
$selected_types_raw  = isset( $_GET['types'] ) ? wp_unslash( $_GET['types'] ) : '';

$selected_states = ! empty( $selected_states_raw ) ? array_map( 'sanitize_text_field', explode( ',', $selected_states_raw ) ) : array();
$selected_types  = ! empty( $selected_types_raw ) ? array_map( 'sanitize_text_field', explode( ',', $selected_types_raw ) ) : array();

// Get all available filter terms
$states = get_terms(
	array(
		'taxonomy'   => 'project_state',
		'hide_empty' => true,
	)
);
$types = get_terms(
	array(
		'taxonomy'   => 'project_type',
		'hide_empty' => true,
	)
);

// Build selected filters display text - optimized
$selected_filters_display = array();
if ( ! empty( $selected_states ) && ! is_wp_error( $states ) ) {
	foreach ( $states as $state_term ) {
		if ( in_array( $state_term->slug, $selected_states, true ) ) {
			$selected_filters_display[] = $state_term->name;
		}
	}
}
if ( ! empty( $selected_types ) && ! is_wp_error( $types ) ) {
	foreach ( $types as $type_term ) {
		if ( in_array( $type_term->slug, $selected_types, true ) ) {
			$selected_filters_display[] = $type_term->name;
		}
	}
}
// Note: $selected_filters_text variable is intentionally kept for potential future use.
$selected_filters_text = ! empty( $selected_filters_display ) ? implode( ', ', $selected_filters_display ) : '';

// Use the global query (already modified by pre_get_posts)
global $wp_query;
$projects_query = $wp_query;
?>

<main id="main" class="site-main">
	<section class="section section--projects-listing">
		<div class="container container--lg">
			<div class="jlb projects-listing">
				
				<!-- Page Title -->
				<header class="projects-listing__header" data-aos="fade-up" data-aos-delay="100"    >
					<h1 class="projects-listing__title"><?php echo esc_html( $projects_page_title ); ?></h1>
				</header>

				<!-- Filter Section -->
				<?php if ( ! empty( $selected_states ) || ! empty( $selected_types ) ) : ?>
					<div class="projects-listing__filters" data-aos="fade-up" data-aos-delay="200"    >
						<div class="projects-listing__filter-tags-wrapper">
							<?php
							// Display state filter tags
							if ( ! empty( $selected_states ) && ! is_wp_error( $states ) ) :
								foreach ( $states as $state_term ) :
									if ( in_array( $state_term->slug, $selected_states, true ) ) :
										?>
										<div class="projects-listing__filter-tag" data-filter-type="state" data-value="<?php echo esc_attr( $state_term->slug ); ?>">
											<button type="button" class="projects-listing__filter-tag-remove js-remove-filter-tag" aria-label="<?php echo esc_attr( 'Remove ' . $state_term->name ); ?>">
												<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M1.4 14L0 12.6L5.6 7L0 1.4L1.4 0L7 5.6L12.6 0L14 1.4L8.4 7L14 12.6L12.6 14L7 8.4L1.4 14Z" fill="#013D62" style="fill:#013D62;fill:color(display-p3 0.0039 0.2392 0.3843);fill-opacity:1;"/>
												</svg>
											</button>
											<span class="projects-listing__filter-tag-label"><?php echo esc_html( $state_term->name ); ?></span>
										</div>
										<?php
									endif;
								endforeach;
							endif;
							
							// Display type filter tags
							if ( ! empty( $selected_types ) && ! is_wp_error( $types ) ) :
								foreach ( $types as $type_term ) :
									if ( in_array( $type_term->slug, $selected_types, true ) ) :
										?>
										<div class="projects-listing__filter-tag" data-filter-type="type" data-value="<?php echo esc_attr( $type_term->slug ); ?>">
											<button type="button" class="projects-listing__filter-tag-remove js-remove-filter-tag" aria-label="<?php echo esc_attr( 'Remove ' . $type_term->name ); ?>">
												<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M1.4 14L0 12.6L5.6 7L0 1.4L1.4 0L7 5.6L12.6 0L14 1.4L8.4 7L14 12.6L12.6 14L7 8.4L1.4 14Z" fill="#013D62" style="fill:#013D62;fill:color(display-p3 0.0039 0.2392 0.3843);fill-opacity:1;"/>
												</svg>
											</button>
											<span class="projects-listing__filter-tag-label"><?php echo esc_html( $type_term->name ); ?></span>
										</div>
										<?php
									endif;
								endforeach;
							endif;
							?>
						</div>
					</div>
				<?php endif; ?>

				<!-- Projects Grid Container -->
				<div class="projects-listing-container">
					<?php if ( have_posts() ) : ?>
						<div class="projects-grid js-projects-grid" data-aos="fade-up" data-aos-delay="300"    >
							<?php
							// Cache template directory URI and setup loop
							$template_uri   = get_template_directory_uri();
							$arrow_icon_url = $template_uri . '/assets/images/icons/small-arrow.svg';
							$index          = 0;
							
							while ( have_posts() ) :
								the_post();
								
								// Display project card
								get_template_part(
									'template-parts/projects/card',
									null,
									array(
										'index'          => $index,
										'arrow_icon_url' => $arrow_icon_url,
									)
								);
								
								$index++;
							endwhile;
							?>
						</div>

						<!-- Pagination -->
						<?php if ( $projects_query->max_num_pages > 1 ) : ?>
							<?php
							// Build base URL with filter parameters
							$base_url = get_post_type_archive_link( 'projects' );
							if ( ! $base_url ) {
								$base_url = home_url( '/projects/' );
							}
							
							// Add filter parameters to base URL
							$query_args = array();
							if ( ! empty( $selected_states ) ) {
								$query_args['states'] = implode( ',', $selected_states );
							}
							if ( ! empty( $selected_types ) ) {
								$query_args['types'] = implode( ',', $selected_types );
							}
							
							// Determine pagination format based on whether we have query args
							$has_query_args = ! empty( $query_args );
							
							if ( $has_query_args ) {
								$base_url = add_query_arg( $query_args, $base_url );
							}
							
							// Build pagination arguments
							$pagination_args = array(
								'total'     => $projects_query->max_num_pages,
								'current'   => max( 1, get_query_var( 'paged' ) ),
								'prev_text' => '← Previous',
								'next_text' => 'Next →',
								'type'      => 'list',
								'base'      => $base_url . '%_%',
								'format'    => $has_query_args ? '&paged=%#%' : '/page/%#%/',
								'add_args'  => false, // Prevent WordPress from adding extra query args
							);
							?>
							<nav class="projects-pagination" aria-label="<?php esc_attr_e( 'Projects pagination', 'jlbpartners' ); ?>" data-aos="fade-up"    >
								<?php
								$pagination_output = paginate_links( $pagination_args );
								if ( $pagination_output ) {
									echo wp_kses_post( $pagination_output );
								}
								?>
							</nav>
						<?php endif; ?>

						<!-- Filter Button (Bottom Center) -->
						<?php
						get_template_part(
							'template-parts/projects/filter-dropdown',
							null,
							array(
								'states'          => $states,
								'types'           => $types,
								'selected_states' => $selected_states,
								'selected_types'  => $selected_types,
							)
						);
						?>

					<?php else : ?>
						<div class="no-projects-found" data-aos="fade-up" data-aos-delay="100"    >
							<p><?php esc_html_e( 'No projects found.', 'jlbpartners' ); ?></p>
						</div>

						<!-- Filter Button (Bottom Center) -->
						<?php
						get_template_part(
							'template-parts/projects/filter-dropdown',
							null,
							array(
								'states'          => $states,
								'types'           => $types,
								'selected_states' => $selected_states,
								'selected_types'  => $selected_types,
							)
						);
						?>
						
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>

<?php
get_footer();
