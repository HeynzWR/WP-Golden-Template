<?php
/**
 * Template part for displaying post content
 *
 * Used in loops to display individual posts/pages.
 *
 * @package JLBPartners
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( ! is_singular() ) : ?>
		<a href="<?php the_permalink(); ?>" class="fill-link">
			<?php esc_html_e( 'Read More', 'jlbpartners' ); ?>
		</a>
	<?php endif; ?>
	<div class="page-content__wrapper">
		<header class="entry-header">
			<?php
			if ( is_singular() ) :
				the_title( '<h1 class="entry-title">', '</h1>' );
			else :
				the_title( '<h2 class="entry-title">', '</h2>' );
			endif;
			?>

			<?php if ( 'post' === get_post_type() ) : ?>
				<div class="entry-meta">
					<?php
					// Display post date, author, etc. if needed
					?>
				</div>
			<?php endif; ?>
		</header>

		<?php
		if ( is_singular() ) {
			?>
			<div class="entry-content">
				<?php
				the_content();

				// Display page links for paginated content.
				wp_link_pages(
					array(
						'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'jlbpartners' ),
						'after'  => '</div>',
					)
				);
				?>
			</div>
			<?php
		} elseif ( get_the_excerpt() ) {
			?>
			<div class="entry-content">
				<?php the_excerpt(); ?>
			</div>
			<?php
		}
		?>
	</div>
	
</article>
