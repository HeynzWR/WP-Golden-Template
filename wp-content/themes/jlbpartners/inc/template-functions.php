<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package JLBPartners
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function jlbpartners_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'jlbpartners_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function jlbpartners_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'jlbpartners_pingback_header' );

/**
 * Display footer menu with parent as heading and children as list items
 *
 * @param string $location Menu location slug.
 * @return void
 */
function jlbpartners_footer_menu( $location ) {
	// Get menu by location.
	$locations = get_nav_menu_locations();

	if ( ! isset( $locations[ $location ] ) ) {
		return;
	}

	$menu_id = $locations[ $location ];

	// Get all menu items.
	$menu_items = wp_get_nav_menu_items( $menu_id );

	if ( ! $menu_items ) {
		return;
	}

	// Find parent (top-level) items.
	$parent_items = array();
	$child_items  = array();

	foreach ( $menu_items as $item ) {
		if ( 0 === (int) $item->menu_item_parent ) {
			$parent_items[] = $item;
		} else {
			$parent_id = (int) $item->menu_item_parent;
			if ( ! isset( $child_items[ $parent_id ] ) ) {
				$child_items[ $parent_id ] = array();
			}
			$child_items[ $parent_id ][] = $item;
		}
	}

	// Display menu - parent as h3 in footer__col-menu div, children as ul > li.
	if ( ! empty( $parent_items ) ) {
		foreach ( $parent_items as $parent ) {
			echo '<div class="footer__col-menu">';
			
			echo '<h3 class="footer__menu-title">';
			if ( ! empty( $parent->url ) && '#' !== $parent->url ) {
				echo '<a href="' . esc_url( $parent->url ) . '" title="' . esc_attr( $parent->title ) . '">';
				echo esc_html( $parent->title );
				echo '</a>';
			} else {
				echo esc_html( $parent->title );
			}
			echo '</h3>';

			// Display children if they exist.
			if ( isset( $child_items[ $parent->ID ] ) && ! empty( $child_items[ $parent->ID ] ) ) {
				echo '<ul>';
				foreach ( $child_items[ $parent->ID ] as $child ) {
					echo '<li>';
					echo '<a href="' . esc_url( $child->url ) . '" title="' . esc_attr( $child->title ) . '">';
					echo esc_html( $child->title );
					echo '</a>';
					echo '</li>';
				}
				echo '</ul>';
			}

			echo '</div>';
		}
	}
}
