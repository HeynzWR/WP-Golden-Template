<?php
/**
 * Template part for displaying a message when no posts are found
 *
 * Used when no content is available to display.
 *
 * @package GoldenTemplate
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
						esc_html_e( 'Nothing Found', 'golden-template' );
					} else {
						esc_html_e( 'No Content Found', 'golden-template' );
					}
				?>
			</h2>
			<p>
				<?php
				esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'golden-template' );
				?>
			</p>
			<?php get_search_form(); ?>
		<?php else : ?>
			<p>
				<?php
				esc_html_e( 'It seems we can\'t find what you\'re looking for. Perhaps searching can help.', 'golden-template' );
				?>
			</p>
			<?php get_search_form(); ?>
		<?php endif; ?>
</section>
