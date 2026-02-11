<?php
/**
 * Template part for displaying a message when no posts are found
 *
 * Used when no content is available to display.
 *
 * @package JLBPartners
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<section class="no-results not-found">
		<?php if ( is_search() ) : ?>
			<h2>
				<?php
					if ( is_search() ) {
						esc_html_e( 'Nothing Found', 'jlbpartners' );
					} else {
						esc_html_e( 'No Content Found', 'jlbpartners' );
					}
				?>
			</h2>
			<p>
				<?php
				esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'jlbpartners' );
				?>
			</p>
			<?php get_search_form(); ?>
		<?php else : ?>
			<p>
				<?php
				esc_html_e( 'It seems we can\'t find what you\'re looking for. Perhaps searching can help.', 'jlbpartners' );
				?>
			</p>
			<?php get_search_form(); ?>
		<?php endif; ?>
</section>
