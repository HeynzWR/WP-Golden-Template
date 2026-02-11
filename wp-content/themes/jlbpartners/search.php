<?php
/**
 * The template for displaying search results
 *
 * @package JLBPartners
 */

get_header();
?>

<main id="main" class="site-main">
	<header class="page-header">
		<h1 class="page-title">
			<?php
			printf(
				/* translators: %s: search query. */
				esc_html__( 'Search Results for: %s', 'jlbpartners' ),
				'<span>' . get_search_query() . '</span>'
			);
			?>
		</h1>
	</header>
	<div class="page-content page-content--list">
		<div class="container">
			<?php if ( have_posts() ) : ?>
				
				

				<?php
				// Start the Loop.
				while ( have_posts() ) :
					the_post();

					/**
					 * Include the Post-Type-specific template for the content.
					 * If you want to override this in a child theme, then include a file
					 * called content-{post-type}.php and that will be used instead.
					 */
					get_template_part( 'content', get_post_type() );

				endwhile;

				// Previous/next page navigation.
				the_posts_navigation();

			else :

				// No results found.
				get_template_part( 'content', 'none' );

			endif;
			?>
		</div>
	</div>

<?php
get_footer();
