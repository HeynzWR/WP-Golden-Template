<?php
/**
 * Template part for displaying the projects filter dropdown
 *
 * @package JLBPartners
 * 
 * Expected variables:
 * @var array|WP_Error $states Array of project_state terms
 * @var array|WP_Error $types Array of project_type terms
 * @var array $selected_states Array of selected state slugs
 * @var array $selected_types Array of selected type slugs
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Extract variables from args
$states          = isset( $args['states'] ) ? $args['states'] : array();
$types           = isset( $args['types'] ) ? $args['types'] : array();
$selected_states = isset( $args['selected_states'] ) ? $args['selected_states'] : array();
$selected_types  = isset( $args['selected_types'] ) ? $args['selected_types'] : array();
?>

<div class="projects-listing__filter-button-wrapper js-filter-button-wrapper" data-aos="fade-up" data-aos-anchor-placement="top-bottom"    >
	<button class="projects-listing__filter-button js-filter-toggle" aria-label="Filter projects" aria-expanded="false">
		<span class="projects-listing__filter-icon">
			<span></span>
			<span></span>
			<span></span>
		</span>
		<span class="projects-listing__filter-button-text"><?php esc_html_e( 'Filter', 'jlbpartners' ); ?></span>
	</button>

	<!-- Filter Dropdown -->
	<div class="projects-listing__filter-dropdown js-filter-dropdown" aria-hidden="true">
		<div class="projects-listing__filter-dropdown-inner">
			<!-- Applied Filters Section -->
			<div class="projects-listing__applied-filters">
				<h3 class="projects-listing__applied-filters-title">Applied filters</h3>
				<div class="projects-listing__applied-filters-list js-applied-filters-list">
					<!-- Applied filters will be dynamically added here -->
				</div>
			</div>

			<!-- Filter Columns -->
			<div class="projects-listing__filter-columns">
				<!-- By State Column -->
				<div class="projects-listing__filter-column">
					<h3 class="projects-listing__filter-column-title">By state</h3>
					<div class="projects-listing__filter-options">
						<button 
							type="button"
							class="projects-listing__filter-option js-filter-state <?php echo empty( $selected_states ) ? 'is-active' : ''; ?>"
							data-value="any"
							data-filter-type="state"
						>
							<span class="projects-listing__filter-dot js-filter-dot"></span>
							<span class="projects-listing__filter-option-label">Any</span>
						</button>
						<?php if ( ! is_wp_error( $states ) && ! empty( $states ) ) : ?>
							<?php foreach ( $states as $state ) : ?>
								<button 
									type="button"
									class="projects-listing__filter-option js-filter-state <?php echo in_array( $state->slug, $selected_states, true ) ? 'is-active' : ''; ?>"
									data-value="<?php echo esc_attr( $state->slug ); ?>"
									data-filter-type="state"
									data-label="<?php echo esc_attr( $state->name ); ?>"
								>
									<span class="projects-listing__filter-dot js-filter-dot"></span>
									<span class="projects-listing__filter-option-label"><?php echo esc_html( $state->name ); ?></span>
								</button>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				</div>

				<!-- By Type Column -->
				<div class="projects-listing__filter-column">
					<h3 class="projects-listing__filter-column-title">By type</h3>
					<div class="projects-listing__filter-options">
						<button 
							type="button"
							class="projects-listing__filter-option js-filter-type <?php echo empty( $selected_types ) ? 'is-active' : ''; ?>"
							data-value="any"
							data-filter-type="type"
						>
							<span class="projects-listing__filter-dot js-filter-dot"></span>
							<span class="projects-listing__filter-option-label">Any</span>
						</button>
						<?php if ( ! is_wp_error( $types ) && ! empty( $types ) ) : ?>
							<?php foreach ( $types as $type_term ) : ?>
								<button 
									type="button"
									class="projects-listing__filter-option js-filter-type <?php echo in_array( $type_term->slug, $selected_types, true ) ? 'is-active' : ''; ?>"
									data-value="<?php echo esc_attr( $type_term->slug ); ?>"
									data-filter-type="type"
									data-label="<?php echo esc_attr( $type_term->name ); ?>"
								>
									<span class="projects-listing__filter-dot js-filter-dot"></span>
									<span class="projects-listing__filter-option-label"><?php echo esc_html( $type_term->name ); ?></span>
								</button>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<!-- Action Buttons -->
			<div class="projects-listing__filter-actions">
				<button type="button" class="projects-listing__filter-apply js-filter-apply">
					<?php esc_html_e( 'Apply', 'jlbpartners' ); ?>
				</button>
				<button type="button" class="projects-listing__filter-close js-filter-close">
					<?php esc_html_e( 'Close', 'jlbpartners' ); ?>
				</button>
			</div>
		</div>
	</div>
</div>
