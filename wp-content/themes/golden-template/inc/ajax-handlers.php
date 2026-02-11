<?php
/**
 * AJAX Handlers for Block Auto-fill Functionality
 *
 * @package GoldenTemplate
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AJAX handler to get post data for auto-fill
 */
function golden_template_get_post_data_for_autofill() {
	check_ajax_referer( 'golden_template_autofill_nonce', 'nonce' );

	$post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;

	if ( ! $post_id ) {
		wp_send_json_error( array( 'message' => 'Invalid post ID' ) );
	}

	$post = get_post( $post_id );

	if ( ! $post || 'attachment' === $post->post_type ) {
		wp_send_json_error( array( 'message' => 'Post not found or is an attachment' ) );
	}

	$featured_image_id = get_post_thumbnail_id( $post_id );

	$excerpt = '';
	if ( ! empty( $post->post_excerpt ) ) {
		$excerpt = $post->post_excerpt;
	} elseif ( ! empty( $post->post_content ) ) {
		$excerpt = wp_trim_words( wp_strip_all_tags( $post->post_content ), 30, '...' );
	}

	$response = array(
		'title'           => get_the_title( $post_id ),
		'description'     => $excerpt,
		'featuredImageId' => $featured_image_id,
		'permalink'       => get_permalink( $post_id ),
		'cta_text'        => 'Read More',
		'cta_aria'        => 'Read ' . get_the_title( $post_id ) . ' online',
	);

	wp_send_json_success( $response );
}
add_action( 'wp_ajax_golden_template_get_post_data', 'golden_template_get_post_data_for_autofill' );

/**
 * AJAX handler to get featured image for a post
 */
function golden_template_get_featured_image_for_autofill() {
	check_ajax_referer( 'golden_template_autofill_nonce', 'nonce' );

	$post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;

	if ( ! $post_id ) {
		wp_send_json_error( array( 'message' => 'Invalid post ID' ) );
	}

	$featured_image_id = get_post_thumbnail_id( $post_id );

	if ( ! $featured_image_id ) {
		wp_send_json_error( array( 'message' => 'No featured image found' ) );
	}

	$response = array(
		'image_id' => $featured_image_id,
	);

	wp_send_json_success( $response );
}
add_action( 'wp_ajax_golden_template_get_featured_image', 'golden_template_get_featured_image_for_autofill' );

/**
 * AJAX handler to filter projects
 */
function golden_template_filter_projects() {
	check_ajax_referer( 'golden_template_nonce', 'nonce' );

	// Get selected filters - sanitize input
	// phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Nonce verified above, sanitized below
	$selected_states_raw = isset( $_POST['states'] ) ? wp_unslash( $_POST['states'] ) : '';
	// phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Nonce verified above, sanitized below
	$selected_types_raw  = isset( $_POST['types'] ) ? wp_unslash( $_POST['types'] ) : '';
	
	$selected_states = ! empty( $selected_states_raw ) ? array_map( 'sanitize_text_field', explode( ',', $selected_states_raw ) ) : array();
	$selected_types  = ! empty( $selected_types_raw ) ? array_map( 'sanitize_text_field', explode( ',', $selected_types_raw ) ) : array();
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified above
	$page            = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;

	// Remove empty values
	$selected_states = array_filter( $selected_states );
	$selected_types  = array_filter( $selected_types );

	// Query projects
	$projects_per_page = absint( get_option( 'golden_template_projects_per_page', 2 ) );
	
	$args = array(
		'post_type'      => 'projects',
		'posts_per_page' => $projects_per_page,
		'post_status'    => 'publish',
		'paged'          => $page,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	// Apply taxonomy filters
	if ( ! empty( $selected_states ) || ! empty( $selected_types ) ) {
		$tax_query = array( 'relation' => 'AND' );
		
		if ( ! empty( $selected_states ) ) {
			$tax_query[] = array(
				'taxonomy' => 'project_state',
				'field'    => 'slug',
				'terms'    => $selected_states,
			);
		}
		
		if ( ! empty( $selected_types ) ) {
			$tax_query[] = array(
				'taxonomy' => 'project_type',
				'field'    => 'slug',
				'terms'    => $selected_types,
			);
		}
		
		$args['tax_query'] = $tax_query;
	}

	$selected_filters_html = golden_template_get_filter_tags_html( $selected_states, $selected_types );

	$projects_query = new WP_Query( $args );

	ob_start();

	if ( $projects_query->have_posts() ) :
		?>
		<div class="projects-grid js-projects-grid" data-aos="fade-up" data-aos-delay="300">
			<?php
			// Cache template directory URI and arrow icon outside loop
			$template_uri   = get_template_directory_uri();
			$arrow_icon_url = $template_uri . '/assets/images/icons/small-arrow.svg';
			$index          = 0;
			
			while ( $projects_query->have_posts() ) :
				$projects_query->the_post();
				
				// Use template part for consistency and maintainability
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
			wp_reset_postdata();
			?>
		</div>
		<?php
	else :
		?>
		<div class="no-projects-found">
			<p><?php esc_html_e( 'No projects found.', 'golden-template' ); ?></p>
		</div>
		<?php
	endif;

	$posts_html = ob_get_clean();

	// Generate pagination
	$pagination_html = golden_template_generate_pagination_html( $projects_query, $page, $selected_states, $selected_types );

	wp_send_json_success(
		array(
			'posts_html'            => $posts_html,
			'pagination_html'       => $pagination_html,
			'total_projects'        => $projects_query->found_posts,
			'selected_filters_html' => $selected_filters_html,
		)
	);
}
add_action( 'wp_ajax_filter_projects', 'golden_template_filter_projects' );
add_action( 'wp_ajax_nopriv_filter_projects', 'golden_template_filter_projects' );

/**
 * Get filter display text from slugs
 * 
 * @param array $selected_states Array of state slugs.
 * @param array $selected_types  Array of type slugs.
 * @return string Comma-separated filter names.
 */
function golden_template_get_filter_display_text( $selected_states, $selected_types ) {
	$selected_filters_display = array();
	
	// Pre-fetch all terms at once instead of individual lookups
	$all_slugs = array_merge( $selected_states, $selected_types );
	if ( empty( $all_slugs ) ) {
		return '';
	}
	
	// Batch fetch state terms
	if ( ! empty( $selected_states ) ) {
		$state_terms = get_terms(
			array(
				'taxonomy'   => 'project_state',
				'slug'       => $selected_states,
				'hide_empty' => false,
			)
		);
		if ( ! is_wp_error( $state_terms ) ) {
			foreach ( $state_terms as $term ) {
				$selected_filters_display[] = $term->name;
			}
		}
	}
	
	// Batch fetch type terms
	if ( ! empty( $selected_types ) ) {
		$type_terms = get_terms(
			array(
				'taxonomy'   => 'project_type',
				'slug'       => $selected_types,
				'hide_empty' => false,
			)
		);
		if ( ! is_wp_error( $type_terms ) ) {
			foreach ( $type_terms as $term ) {
				$selected_filters_display[] = $term->name;
			}
		}
	}
	
	return ! empty( $selected_filters_display ) ? implode( ', ', $selected_filters_display ) : '';
}

/**
 * Get filter tags HTML from slugs
 * 
 * @param array $selected_states Array of state slugs.
 * @param array $selected_types  Array of type slugs.
 * @return string HTML for filter tags.
 */
function golden_template_get_filter_tags_html( $selected_states, $selected_types ) {
	$html = '';
	
	// If no filters selected, return empty (caller will show "All Projects")
	if ( empty( $selected_states ) && empty( $selected_types ) ) {
		return '';
	}
	
	// Batch fetch state terms
	if ( ! empty( $selected_states ) ) {
		$state_terms = get_terms(
			array(
				'taxonomy'   => 'project_state',
				'slug'       => $selected_states,
				'hide_empty' => false,
			)
		);
		if ( ! is_wp_error( $state_terms ) ) {
			foreach ( $state_terms as $term ) {
				$html .= sprintf(
					'<div class="projects-listing__filter-tag" data-filter-type="state" data-value="%s">
						<button type="button" class="projects-listing__filter-tag-remove js-remove-filter-tag" aria-label="%s">
							<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M1 1L11 11M11 1L1 11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
							</svg>
						</button>
						<span class="projects-listing__filter-tag-label">%s</span>
					</div>',
					esc_attr( $term->slug ),
					esc_attr( 'Remove ' . $term->name ),
					esc_html( $term->name )
				);
			}
		}
	}
	
	// Batch fetch type terms
	if ( ! empty( $selected_types ) ) {
		$type_terms = get_terms(
			array(
				'taxonomy'   => 'project_type',
				'slug'       => $selected_types,
				'hide_empty' => false,
			)
		);
		if ( ! is_wp_error( $type_terms ) ) {
			foreach ( $type_terms as $term ) {
				$html .= sprintf(
					'<div class="projects-listing__filter-tag" data-filter-type="type" data-value="%s">
						<button type="button" class="projects-listing__filter-tag-remove js-remove-filter-tag" aria-label="%s">
							<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M1 1L11 11M11 1L1 11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
							</svg>
						</button>
						<span class="projects-listing__filter-tag-label">%s</span>
					</div>',
					esc_attr( $term->slug ),
					esc_attr( 'Remove ' . $term->name ),
					esc_html( $term->name )
				);
			}
		}
	}
	
	return $html;
}

/**
 * Generate pagination HTML
 * 
 * @param WP_Query $query           The projects query object.
 * @param int      $page            Current page number.
 * @param array    $selected_states Selected state slugs.
 * @param array    $selected_types  Selected type slugs.
 * @return string Pagination HTML.
 */
function golden_template_generate_pagination_html( $query, $page, $selected_states, $selected_types ) {
	if ( $query->max_num_pages <= 1 ) {
		return '';
	}
	
	ob_start();
	
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
	
	$has_query_args = ! empty( $query_args );
	
	if ( $has_query_args ) {
		$base_url = add_query_arg( $query_args, $base_url );
	}
	
	// Build pagination arguments
	$pagination_args = array(
		'total'     => $query->max_num_pages,
		'current'   => $page,
		'prev_text' => '← Previous',
		'next_text' => 'Next →',
		'type'      => 'list',
		'base'      => $base_url . '%_%',
		'format'    => $has_query_args ? '&paged=%#%' : '/page/%#%/',
		'add_args'  => false,
	);
	
	$pagination_output = paginate_links( $pagination_args );
	if ( $pagination_output ) {
		echo wp_kses_post( $pagination_output );
	}
	
	return ob_get_clean();
}

/**
 * AJAX handler to get available filter combinations
 * Returns which filter options are available based on current selections
 * 
 * Optimized to use a single query with aggregation instead of individual queries per term
 */
function golden_template_get_available_filters() {
	check_ajax_referer( 'golden_template_nonce', 'nonce' );

	// Get current filter selections
	$selected_states = array();
	$selected_types  = array();
	
	// phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Nonce verified above, sanitized below
	if ( isset( $_POST['states'] ) && ! empty( $_POST['states'] ) ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Nonce verified above, sanitized below
		$selected_states_raw = wp_unslash( $_POST['states'] );
		$selected_states = array_map( 'sanitize_text_field', explode( ',', $selected_states_raw ) );
		$selected_states = array_filter( $selected_states );
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Nonce verified above, sanitized below
	if ( isset( $_POST['types'] ) && ! empty( $_POST['types'] ) ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Nonce verified above, sanitized below
		$selected_types_raw = wp_unslash( $_POST['types'] );
		$selected_types = array_map( 'sanitize_text_field', explode( ',', $selected_types_raw ) );
		$selected_types = array_filter( $selected_types );
	}

	// Try to get cached results first
	$cache_key = 'golden_template_available_filters_' . md5( wp_json_encode( array( $selected_states, $selected_types ) ) );
	$cached    = get_transient( $cache_key );
	
	if ( false !== $cached ) {
		wp_send_json_success( $cached );
		return;
	}

	// Get all terms
	$all_states = get_terms(
		array(
			'taxonomy'   => 'project_state',
			'hide_empty' => true,
		)
	);
	$all_types = get_terms(
		array(
			'taxonomy'   => 'project_type',
			'hide_empty' => true,
		)
	);

	$available_states = array();
	$available_types  = array();

	// If no filters are selected, all options are available
	if ( empty( $selected_states ) && empty( $selected_types ) ) {
		if ( ! is_wp_error( $all_states ) && ! empty( $all_states ) ) {
			foreach ( $all_states as $state ) {
				$available_states[] = $state->slug;
			}
		}
		if ( ! is_wp_error( $all_types ) && ! empty( $all_types ) ) {
			foreach ( $all_types as $type ) {
				$available_types[] = $type->slug;
			}
		}
	} else {
		// OPTIMIZED: Use a single query to get all available term combinations
		$available_states = golden_template_get_available_terms_optimized( 'project_state', $all_states, $selected_types, 'project_type' );
		$available_types  = golden_template_get_available_terms_optimized( 'project_type', $all_types, $selected_states, 'project_state' );
	}

	$result = array(
		'available_states' => $available_states,
		'available_types'  => $available_types,
	);

	// Cache for 5 minutes
	set_transient( $cache_key, $result, 5 * MINUTE_IN_SECONDS );

	wp_send_json_success( $result );
}
add_action( 'wp_ajax_get_available_filters', 'golden_template_get_available_filters' );
add_action( 'wp_ajax_nopriv_get_available_filters', 'golden_template_get_available_filters' );

/**
 * Get available terms for a taxonomy based on selected filters
 * Uses optimized query to check all terms at once instead of individual queries
 * 
 * @param string $check_taxonomy     Taxonomy to check availability for.
 * @param array  $all_terms          All terms in the taxonomy.
 * @param array  $selected_other     Selected terms from the other taxonomy.
 * @param string $other_taxonomy     The other taxonomy name.
 * @return array Array of available term slugs.
 */
function golden_template_get_available_terms_optimized( $check_taxonomy, $all_terms, $selected_other, $other_taxonomy ) {
	global $wpdb;
	
	if ( is_wp_error( $all_terms ) || empty( $all_terms ) ) {
		return array();
	}
	
	// Build base query
	$query = "
		SELECT DISTINCT t.slug
		FROM {$wpdb->terms} t
		INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id
		INNER JOIN {$wpdb->term_relationships} tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
		INNER JOIN {$wpdb->posts} p ON tr.object_id = p.ID
		WHERE tt.taxonomy = %s
		AND p.post_type = 'projects'
		AND p.post_status = 'publish'
	";
	
	$query_args = array( $check_taxonomy );
	
	// If other taxonomy has selections, add filter
	if ( ! empty( $selected_other ) ) {
		$placeholders = implode( ',', array_fill( 0, count( $selected_other ), '%s' ) );
		
		$query .= "
			AND p.ID IN (
				SELECT tr2.object_id
				FROM {$wpdb->term_relationships} tr2
				INNER JOIN {$wpdb->term_taxonomy} tt2 ON tr2.term_taxonomy_id = tt2.term_taxonomy_id
				INNER JOIN {$wpdb->terms} t2 ON tt2.term_id = t2.term_id
				WHERE tt2.taxonomy = %s
				AND t2.slug IN ($placeholders)
			)
		";
		
		$query_args[] = $other_taxonomy;
		$query_args   = array_merge( $query_args, $selected_other );
	}
	
	// Check cache first
	$cache_key_query = 'golden_template_available_terms_' . md5( wp_json_encode( array( $check_taxonomy, $selected_other ) ) );
	$cached_slugs    = wp_cache_get( $cache_key_query, 'golden-template' );
	
	if ( false !== $cached_slugs ) {
		return $cached_slugs;
	}
	
	// Prepare and execute query
	// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery -- Query is prepared, caching added
	$prepared_query  = $wpdb->prepare( $query, $query_args );
	// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery -- Query is prepared, caching added
	$available_slugs = $wpdb->get_col( $prepared_query );
	
	// Cache results for 5 minutes
	if ( false !== $available_slugs ) {
		wp_cache_set( $cache_key_query, $available_slugs, 'golden-template', 5 * MINUTE_IN_SECONDS );
	}
	
	return $available_slugs ? $available_slugs : array();
}

/**
 * Clear filter availability cache when projects are updated
 */
function golden_template_clear_filter_cache( $post_id ) {
	// Only clear for projects post type
	if ( 'projects' !== get_post_type( $post_id ) ) {
		return;
	}
	
	// Clear filter cache transients using WordPress functions
	global $wpdb;
	
	// Use wp_cache_flush_group if available, otherwise use direct query
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery -- Necessary for cache clearing, no alternative available
	$transient_keys = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
			$wpdb->esc_like( '_transient_golden_template_available_filters_' ) . '%'
		)
	);
	
	foreach ( $transient_keys as $transient_key ) {
		$transient_name = str_replace( '_transient_', '', $transient_key );
		delete_transient( $transient_name );
	}
	
	// Also clear timeout transients
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery -- Necessary for cache clearing, no alternative available
	$timeout_keys = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
			$wpdb->esc_like( '_transient_timeout_golden_template_available_filters_' ) . '%'
		)
	);
	
	foreach ( $timeout_keys as $timeout_key ) {
		$transient_name = str_replace( '_transient_timeout_', '', $timeout_key );
		delete_transient( $transient_name );
	}
	
	// Clear object cache
	wp_cache_flush_group( 'golden-template' );
}
add_action( 'save_post', 'golden_template_clear_filter_cache' );
add_action( 'delete_post', 'golden_template_clear_filter_cache' );
add_action( 'wp_trash_post', 'golden_template_clear_filter_cache' );
add_action( 'untrash_post', 'golden_template_clear_filter_cache' );
