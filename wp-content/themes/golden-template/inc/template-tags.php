<?php
/**
 * Custom template tags for this theme
 *
 * @package GoldenTemplate
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Formats date with ordinal suffix (e.g., July 23rd, 2025).
 *
 * @param int|string $post_id Optional. Post ID. Defaults to current post.
 * @param bool       $modified Optional. Whether to use modified date. Default false.
 * @return string Formatted date with ordinal suffix.
 */
function golden_template_format_date_with_ordinal( $post_id = null, $modified = false ) {
	if ( $modified ) {
		$day   = (int) get_the_modified_date( 'j', $post_id );
		$month = get_the_modified_date( 'F', $post_id );
		$year  = get_the_modified_date( 'Y', $post_id );
	} else {
		$day   = (int) get_the_date( 'j', $post_id );
		$month = get_the_date( 'F', $post_id );
		$year  = get_the_date( 'Y', $post_id );
	}
	$ordinal = golden_template_get_ordinal_suffix( $day );
	return $month . ' ' . $day . $ordinal . ', ' . $year;
}

/**
 * Gets ordinal suffix for a number (st, nd, rd, th).
 *
 * @param int $number The number to get ordinal suffix for.
 * @return string The ordinal suffix.
 */
function golden_template_get_ordinal_suffix( $number ) {
	$number = (int) $number;
	$ends = array( 'th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th' );
	if ( ( ( $number % 100 ) >= 11 ) && ( ( $number % 100 ) <= 13 ) ) {
		return 'th';
	}
	return $ends[ $number % 10 ];
}

/**
 * Prints HTML with meta information for the current post-date/time.
 */
function golden_template_posted_on() {
	$formatted_date = golden_template_format_date_with_ordinal();
	$time_string    = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	
	$time_string = sprintf(
		$time_string,
		esc_attr( get_the_date( DATE_W3C ) ),
		esc_html( $formatted_date )
	);

	$posted_on = sprintf(
		/* translators: %s: post date */
		esc_html_x( '%s', 'post date', 'golden-template' ),
		$time_string
	);

	echo '<span class="posted-on">' . $posted_on . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Prints HTML with meta information for the current author.
 */
function golden_template_posted_by() {
	$byline = sprintf(
		/* translators: %s: post author */
		esc_html_x( 'Posted by: %s', 'post author', 'golden-template' ),
		'<span class="author vcard">' . esc_html( get_the_author() ) . '</span>'
	);

	echo '<span class="byline"> ' . $byline . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
