<?php
/**
 * Header Template
 *
 * @package GoldenTemplate
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Skip Link for Accessibility -->
<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to main content', 'golden-template' ); ?></a>

<div id="page" class="site">
	<?php
		// Check if the current page/post has a hero block.
		$has_hero_block = false;
		if ( is_singular() ) {
			// Check for hero blocks in post content.
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
		
	// Determine which logos to use based on whether there's a hero block.
	$logo_class = '';
	$header_class = 'golden-template header js-header';
	$main_class = 'site-main';
	
	if ( $has_hero_block ) {
		// Use regular logos when hero block is present.
		$logo_desktop_id = get_option( 'golden_template_logo_desktop' );
		$logo_mobile_id  = get_option( 'golden_template_logo_mobile' );
		$logo_class = 'logo--light';
	} else {
		// Use dark logos when no hero block is present.
		$logo_desktop_id = get_option( 'golden_template_logo_desktop_dark' );
		$logo_mobile_id  = get_option( 'golden_template_logo_mobile_dark' );
		$logo_class = 'logo--dark';
		$header_class .= ' header--dark';
		$main_class .= ' site-main--default';
		
		// Fallback to regular logos if dark logos are not set.
		if ( ! $logo_desktop_id ) {
			$logo_desktop_id = get_option( 'golden_template_logo_desktop' );
			$logo_class = 'logo--light';
			$header_class = 'golden-template header js-header'; // Remove header--dark
			$main_class = 'site-main'; // Remove site-main--default
		}
		if ( ! $logo_mobile_id ) {
			$logo_mobile_id  = get_option( 'golden_template_logo_mobile' );
		}
	}
	?>
	
	<header class="<?php echo esc_attr( $header_class ); ?>">
		<div class="container container--lg">
			<div class="header__inner">
				<div class="header__logo">
					<!-- Header Logo -->
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo-link" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) . ' ' . __( 'Home', 'golden-template' ) ); ?>">
						<?php
							// Fall back to single logo option for backwards compatibility.
							if ( ! $logo_desktop_id ) {
								$logo_desktop_id = get_option( 'golden_template_logo' );
							}
							
							// Handle case where only mobile logo is set: use it for desktop too.
							if ( ! $logo_desktop_id && $logo_mobile_id ) {
								$logo_desktop_id = $logo_mobile_id;
							}
							
							// If mobile logo not set, use desktop logo for mobile.
							if ( ! $logo_mobile_id && $logo_desktop_id ) {
								$logo_mobile_id = $logo_desktop_id;
							}
							
							// Proceed if we have at least one logo (desktop or mobile).
							if ( $logo_desktop_id || $logo_mobile_id ) {
								// Ensure we have a desktop logo ID (use mobile if desktop is missing).
								if ( ! $logo_desktop_id ) {
									$logo_desktop_id = $logo_mobile_id;
								}
								
								$logo_desktop_url  = wp_get_attachment_url( $logo_desktop_id );
								$logo_desktop_meta = wp_get_attachment_metadata( $logo_desktop_id );
								$logo_desktop_alt  = get_post_meta( $logo_desktop_id, '_wp_attachment_image_alt', true );
								
								if ( ! $logo_desktop_alt ) {
									$logo_desktop_alt = get_bloginfo( 'name' );
								}
								
								if ( $logo_desktop_url ) {
									?>
									<picture class="<?php echo esc_attr( $logo_class ); ?>">
										<?php
										// Mobile logo source (if different from desktop).
										if ( $logo_mobile_id && $logo_mobile_id !== $logo_desktop_id ) {
											$logo_mobile_url  = wp_get_attachment_url( $logo_mobile_id );
											$logo_mobile_meta = wp_get_attachment_metadata( $logo_mobile_id );
											
											if ( $logo_mobile_url ) {
												$mobile_type = pathinfo( $logo_mobile_url, PATHINFO_EXTENSION );
												// SVG should use svg+xml, otherwise use extension as-is.
												$mobile_type = ( 'svg' === $mobile_type ) ? 'svg+xml' : $mobile_type;
												?>
												<source 
													srcset="<?php echo esc_url( $logo_mobile_url ); ?>" 
													media="(max-width: 992px)" 
													type="image/<?php echo esc_attr( $mobile_type ); ?>"
													<?php if ( isset( $logo_mobile_meta['width'] ) && isset( $logo_mobile_meta['height'] ) ) : ?>
														width="<?php echo esc_attr( $logo_mobile_meta['width'] ); ?>" 
														height="<?php echo esc_attr( $logo_mobile_meta['height'] ); ?>"
													<?php endif; ?>
												>
												<?php
											}
										}
										?>
										<!-- Desktop logo -->
										<img 
											src="<?php echo esc_url( $logo_desktop_url ); ?>" 
											alt="<?php echo esc_attr( $logo_desktop_alt ); ?>" 
											class="<?php echo esc_attr( $logo_class ); ?>"
											fetchpriority="high"
											<?php if ( isset( $logo_desktop_meta['width'] ) && isset( $logo_desktop_meta['height'] ) ) : ?>
												width="<?php echo esc_attr( $logo_desktop_meta['width'] ); ?>" 
												height="<?php echo esc_attr( $logo_desktop_meta['height'] ); ?>"
											<?php endif; ?>
										>
									</picture>
									<?php
								}
							} else {
								// Fallback to site name if no logo.
								?>
								<span class="header__brand-text"><?php bloginfo( 'name' ); ?></span>
								<?php
							}
						?>
					</a>
				</div>
				<!-- Header Navigation -->
				<nav class="header__nav js-header-nav" aria-label="<?php esc_attr_e( 'Primary navigation', 'golden-template' ); ?>" id="site-navigation">
					<button class="mobile-menu-toggle js-header-toggle" aria-label="<?php esc_attr_e( 'Toggle mobile menu', 'golden-template' ); ?>" aria-expanded="false">
						<span class="menu-text"><?php esc_html_e( 'Menu', 'golden-template' ); ?></span>
					</button>
					<div class="header__nav-inner">
						<?php
							// Display primary navigation menu.
							if ( has_nav_menu( 'primary' ) ) {
								wp_nav_menu(
									array(
										'theme_location'  => 'primary',
										'container'       => false,
										'menu_class'      => '',
										'items_wrap'      => '<ul class="header__nav-links">%3$s</ul>',
										'walker'          => new GoldenTemplate_Nav_Walker(),
										'fallback_cb'     => false,
									)
								);
							} else {
								// Fallback message for admin.
								if ( current_user_can( 'manage_options' ) ) {
									?>
									<ul>
										<li>
											<a href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>">
												<?php esc_html_e( 'Set up your primary menu', 'golden-template' ); ?>
											</a>
										</li>
									</ul>
									<?php
								}
							}
						?>
					</div>
				</nav>
			</div>
		</div>
	</header>