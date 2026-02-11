<?php
/**
 * Title Block Template
 *
 * @package GoldenTemplate
 */

// Get field values.
$heading = get_field('title_block_heading');
$text = get_field('title_block_text');
$btn = get_field('title_block_btn');
$button_style = get_field('title_block_button_style') ?: 'primary';

// Build class string for title-block
$title_block_class = 'title-block js-scroll-block';
if ( $button_style === 'primary' ) {
	$title_block_class .= ' title-block--primary';
}
else {
	$title_block_class .= ' title-block--secondary';
}

// Preview mode handling.
if ( $is_preview && empty( $heading ) && empty( $text ) ) {
	golden_template_show_block_preview( 'title-block', __( 'Title Block', 'golden-template' ) );
	return;
}
?>

<section class="section section--title-block" data-aos="fade-up" data-aos-delay="800" data-duration="800" data-aos-offset="-50">
    <div class="<?php echo esc_attr( $title_block_class ); ?>">
        <div class="container">
            <div class="title-block__content">
                <!-- Heading -->
                <?php if ( $heading ) : ?>
                    <h2 class="title-block__heading" data-aos="fade-up" data-aos-delay="100"><?php echo wp_kses( nl2br( $heading ), array( 'br' => array() ) ); ?></h2>
                <?php endif; ?>

                <!-- Text -->
                <?php if ( $text ) : ?>
                    <div class="title-block__text" data-aos="fade-up" data-aos-delay="100"><?php echo wp_kses_post( $text ); ?></div>
                <?php endif; ?>

                <!-- Optional Button -->
                <?php if ( $btn ) : ?>
                    <a href="<?php echo esc_url( $btn['url'] ); ?>" 
                    class="l-btn l-btn--sm l-btn--secondary"
                    <?php if ( !empty( $btn['target'] ) ) : ?>
                        target="<?php echo esc_attr( $btn['target'] ); ?>"
                    <?php endif; ?>
                    data-aos="fade-up">
                        <?php echo esc_html( $btn['title'] ); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>