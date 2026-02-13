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
