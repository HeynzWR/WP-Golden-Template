<?php
/**
 * The template for displaying all pages
 *
 * This is the default page template. It renders ACF block components.
 *
 * @package GoldenTemplate
 */

get_header();

// Determine main class based on hero block presence
$has_hero_block = false;
if ( is_singular() ) {
	$post_content = get_post_field( 'post_content', get_the_ID() );
	if ( $post_content ) {
		$blocks = parse_blocks( $post_content );
		foreach ( $blocks as $block ) {
			if ( ! empty( $block['blockName'] ) && strpos( $block['blockName'], 'hero' ) !== false ) {
				$has_hero_block = true;
				break;
			}
		}
	}
}

$main_class = $has_hero_block ? 'site-main' : 'site-main site-main--default';
?>

<main id="main" class="<?php echo esc_attr( $main_class ); ?>">

<?php
while ( have_posts() ) :
	the_post();
	
	/**
	 * Render ACF block components.
	 * Each component manages its own HTML structure and accessibility.
	 */
	the_content();

endwhile;

get_footer();
