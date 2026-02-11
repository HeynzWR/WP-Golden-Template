<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package GoldenTemplate
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="main" class="site-main site-main--default">
	<section class="section section--not-found">
		<div class="container container--lg">
			<div class="jlb not-found" data-aos="fade-up">
				<h2><?php esc_html_e( '404', 'golden-template' ); ?></h2>
				<a class="not-found__link" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php esc_attr_e( 'Return to homepage', 'golden-template' ); ?>" data-aos="fade-up">
					<span><?php esc_html_e( 'Go home', 'golden-template' ); ?></span>
					<svg xmlns="http://www.w3.org/2000/svg" width="61" height="61" viewBox="0 0 61 61" fill="none">
						<path d="M5.08301 55.9167L55.9163 5.08334M55.9163 5.08334V50.8333M55.9163 5.08334H10.1663" stroke="#013D62" style="stroke:#013D62;stroke:color(display-p3 0.0039 0.2392 0.3843);stroke-opacity:1;" stroke-width="5"/>
					</svg>
				</a>
			</div>
		</div>
	</section>

<?php
get_footer();
