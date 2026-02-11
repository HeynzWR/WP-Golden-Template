<?php
/**
 * Custom Navigation Walker
 *
 * Extends Walker_Nav_Menu to create accessible dropdown menus.
 *
 * @package JLBPartners
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Custom Walker for Navigation Menus
 */
class JLBPartners_Nav_Walker extends Walker_Nav_Menu {

	/**
	 * Starts the element output.
	 *
	 * @param string   $output Used to append additional content (passed by reference).
	 * @param WP_Post  $item Menu item data object.
	 * @param int      $depth Depth of menu item. Used for padding.
	 * @param stdClass $args An object of wp_nav_menu() arguments.
	 * @param int      $id Current item ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		// Build class list.
		$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		// Check if menu item has children.
		$has_children = in_array( 'menu-item-has-children', $classes, true );

		if ( $has_children && 0 === $depth ) {
			$classes[] = 'has-dropdown';
		}

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		// Build ID.
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		// Start output.
		$output .= $indent . '<li' . $id . $class_names . '>';

		// Add dropdown wrapper for parent items.
		if ( $has_children && 0 === $depth ) {
			$output .= '<div class="dropdown-header">';
		}

		// Build link attributes.
		$atts           = array();
		$atts['href']   = ! empty( $item->url ) ? $item->url : '';
		$atts['class']  = $has_children && 0 === $depth ? 'dropdown-link' : '';
		$atts['aria-label'] = ! empty( $item->attr_title ) ? $item->attr_title : wp_strip_all_tags( $item->title );

		// Apply filters.
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		// Build attributes string.
		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		// Build link.
		$item_output = isset( $args->before ) ? $args->before : '';
		$item_output .= '<a' . $attributes . '>';
		$item_output .= isset( $args->link_before ) ? $args->link_before : '';
		$item_output .= apply_filters( 'the_title', $item->title, $item->ID );
		$item_output .= isset( $args->link_after ) ? $args->link_after : '';
		$item_output .= '</a>';
		$item_output .= isset( $args->after ) ? $args->after : '';

		// Add dropdown toggle button for parent items.
		if ( $has_children && 0 === $depth ) {
			$submenu_id = 'submenu-' . sanitize_title( $item->title );
			$item_output .= '<button class="dropdown-toggle" type="button" ';
			$item_output .= 'aria-label="' . esc_attr( sprintf( __( 'Toggle %s submenu', 'jlbpartners' ), $item->title ) ) . '" ';
			$item_output .= 'aria-expanded="false" ';
			$item_output .= 'aria-controls="' . esc_attr( $submenu_id ) . '">';
			$item_output .= '<span class="dropdown-arrow" aria-hidden="true"></span>';
			$item_output .= '</button>';
			$item_output .= '</div>'; // Close dropdown-header.
		}

		// Output the item.
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	/**
	 * Starts the list before the elements are added.
	 *
	 * @param string   $output Used to append additional content (passed by reference).
	 * @param int      $depth Depth of menu item. Used for padding.
	 * @param stdClass $_args An object of wp_nav_menu() arguments (unused but required by Walker class signature).
	 */
	public function start_lvl( &$output, $depth = 0, $_args = null ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Parameter required by Walker class signature.
		$indent = str_repeat( "\t", $depth );

		// Get parent item title for submenu ID.
		$parent_item = $this->get_parent_item_title();
		$submenu_id  = 'submenu-' . sanitize_title( $parent_item );

		// Start submenu.
		$output .= "\n$indent<ul class=\"sub-menu\" id=\"" . esc_attr( $submenu_id ) . "\" aria-hidden=\"true\">\n";
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @param string   $output Used to append additional content (passed by reference).
	 * @param int      $depth Depth of menu item. Used for padding.
	 * @param stdClass $_args An object of wp_nav_menu() arguments (unused but required by Walker class signature).
	 */
	public function end_lvl( &$output, $depth = 0, $_args = null ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Parameter required by Walker class signature.
		$indent  = str_repeat( "\t", $depth );
		$output .= "$indent</ul>\n";
	}

	/**
	 * Helper function to get parent item title
	 *
	 * @return string
	 */
	private function get_parent_item_title() {
		return 'dropdown';
	}
}
