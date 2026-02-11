<?php
/**
 * Template Name: Generic
 * 
 * A simple, generic page template for content pages like Privacy Policy, Terms of Service, etc.
 * This template provides a clean layout without ACF blocks, suitable for standard content pages.
 *
 * @package GoldenTemplate
 */

get_header();
?>

<main id="main" class="site-main site-main--default">

<?php
while ( have_posts() ) :
	the_post();
	?>

	<div class="generic-page">
		<div class="container container--lg">
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'generic-page__content' ); ?>>
				<header class="page-header">
					<h1><?php the_title(); ?></h1>
				</header>
				<div class="generic-page__body">
					<div class="container">
						<div class="jlb generic generic--default-page">
							<?php
								the_content();
								// Display page links for paginated content.
								wp_link_pages(
									array(
										'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'golden-template' ),
										'after'  => '</div>',
									)
								);
							?>
						</div>
					</div>
				</div>
			</article>
		</div>
	</div>

	<?php
endwhile;

get_footer();
