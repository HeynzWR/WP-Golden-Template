<?php
/**
 * JLB Partners Settings Page Template
 *
 * @package JLBPartners_Core
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap jlbpartners-settings">
	<h1><?php esc_html_e( 'JLB Partners Theme Settings', 'jlbpartners-core' ); ?></h1>
	<p class="subtitle">
		<?php esc_html_e( 'Configure your branding, footer settings, and theme options', 'jlbpartners-core' ); ?>
	</p>

	<div class="jlbpartners-tabs">
		<button class="jlbpartners-tab active" data-tab="branding">
			<span class="dashicons dashicons-art"></span>
			<?php esc_html_e( 'Branding', 'jlbpartners-core' ); ?>
		</button>
		<button class="jlbpartners-tab" data-tab="footer">
			<span class="dashicons dashicons-welcome-widgets-menus"></span>
			<?php esc_html_e( 'Footer', 'jlbpartners-core' ); ?>
		</button>
		<button class="jlbpartners-tab" data-tab="system">
			<span class="dashicons dashicons-admin-tools"></span>
			<?php esc_html_e( 'System Info', 'jlbpartners-core' ); ?>
		</button>
	</div>

	<!-- Branding Tab -->
	<div id="tab-branding" class="jlbpartners-tab-content active">
		<div class="jlbpartners-settings-grid">
			<div class="jlbpartners-settings-main">
				
				<!-- Logo & Images Card -->
				<div class="jlbpartners-card">
					<h2><?php esc_html_e( 'Logo & Images', 'jlbpartners-core' ); ?></h2>
					<form method="post" action="options.php">
						<?php settings_fields( 'jlbpartners_branding' ); ?>
						
					<table class="form-table">
						<tr>
							<th scope="row"><?php esc_html_e( 'Desktop Logo', 'jlbpartners-core' ); ?></th>
							<td>
								<div class="jlbpartners-image-upload">
									<div class="jlbpartners-image-preview">
										<?php
										$logo_desktop_id = get_option( 'jlbpartners_logo_desktop' );
										// Backwards compatibility.
										if ( ! $logo_desktop_id ) {
											$logo_desktop_id = get_option( 'jlbpartners_logo' );
										}
										if ( $logo_desktop_id ) {
											$logo_url = wp_get_attachment_url( $logo_desktop_id );
											$logo_alt = get_post_meta( $logo_desktop_id, '_wp_attachment_image_alt', true );
											if ( ! $logo_alt ) {
												$logo_alt = get_bloginfo( 'name' );
											}
											if ( $logo_url ) {
												echo '<img src="' . esc_url( $logo_url ) . '" alt="' . esc_attr( $logo_alt ) . '" style="max-width: 300px; height: auto;">';
											}
										} else {
											echo '<span class="description">' . esc_html__( 'No logo uploaded', 'jlbpartners-core' ) . '</span>';
										}
										?>
									</div>
									<input type="hidden" id="jlbpartners_logo_desktop" name="jlbpartners_logo_desktop" value="<?php echo esc_attr( $logo_desktop_id ); ?>" />
									<button type="button" class="button jlbpartners-upload-btn" data-target="logo_desktop">
										<?php esc_html_e( 'Upload Desktop Logo', 'jlbpartners-core' ); ?>
									</button>
									<?php if ( $logo_desktop_id ) : ?>
										<button type="button" class="button button-link-delete jlbpartners-remove-btn" data-target="logo_desktop">
											<?php esc_html_e( 'Remove', 'jlbpartners-core' ); ?>
										</button>
									<?php endif; ?>
									<p class="description"><?php esc_html_e( 'Logo displayed on desktop devices (Recommended size: 400x100px PNG with transparency)', 'jlbpartners-core' ); ?></p>
								</div>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Mobile Logo', 'jlbpartners-core' ); ?></th>
							<td>
								<div class="jlbpartners-image-upload">
									<div class="jlbpartners-image-preview">
										<?php
										$logo_mobile_id = get_option( 'jlbpartners_logo_mobile' );
										if ( $logo_mobile_id ) {
											$logo_url = wp_get_attachment_url( $logo_mobile_id );
											$logo_alt = get_post_meta( $logo_mobile_id, '_wp_attachment_image_alt', true );
											if ( ! $logo_alt ) {
												$logo_alt = get_bloginfo( 'name' );
											}
											if ( $logo_url ) {
												echo '<img src="' . esc_url( $logo_url ) . '" alt="' . esc_attr( $logo_alt ) . '" style="max-width: 300px; height: auto;">';
											}
										} else {
											echo '<span class="description">' . esc_html__( 'No mobile logo uploaded', 'jlbpartners-core' ) . '</span>';
										}
										?>
									</div>
									<input type="hidden" id="jlbpartners_logo_mobile" name="jlbpartners_logo_mobile" value="<?php echo esc_attr( $logo_mobile_id ); ?>" />
									<button type="button" class="button jlbpartners-upload-btn" data-target="logo_mobile">
										<?php esc_html_e( 'Upload Mobile Logo', 'jlbpartners-core' ); ?>
									</button>
									<?php if ( $logo_mobile_id ) : ?>
										<button type="button" class="button button-link-delete jlbpartners-remove-btn" data-target="logo_mobile">
											<?php esc_html_e( 'Remove', 'jlbpartners-core' ); ?>
										</button>
									<?php endif; ?>
									<p class="description"><?php esc_html_e( 'Logo displayed on mobile devices ≤992px (Recommended size: 200x50px PNG with transparency). If not set, desktop logo will be used.', 'jlbpartners-core' ); ?></p>
								</div>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Desktop Dark Logo', 'jlbpartners-core' ); ?></th>
							<td>
								<div class="jlbpartners-image-upload">
									<div class="jlbpartners-image-preview">
										<?php
										$logo_desktop_dark_id = get_option( 'jlbpartners_logo_desktop_dark' );
										if ( $logo_desktop_dark_id ) {
											$logo_url = wp_get_attachment_url( $logo_desktop_dark_id );
											$logo_alt = get_post_meta( $logo_desktop_dark_id, '_wp_attachment_image_alt', true );
											if ( ! $logo_alt ) {
												$logo_alt = get_bloginfo( 'name' );
											}
											if ( $logo_url ) {
												echo '<img src="' . esc_url( $logo_url ) . '" alt="' . esc_attr( $logo_alt ) . '" style="max-width: 300px; height: auto;">';
											}
										} else {
											echo '<span class="description">' . esc_html__( 'No dark logo uploaded', 'jlbpartners-core' ) . '</span>';
										}
										?>
									</div>
									<input type="hidden" id="jlbpartners_logo_desktop_dark" name="jlbpartners_logo_desktop_dark" value="<?php echo esc_attr( $logo_desktop_dark_id ); ?>" />
									<button type="button" class="button jlbpartners-upload-btn" data-target="logo_desktop_dark">
										<?php esc_html_e( 'Upload Desktop Dark Logo', 'jlbpartners-core' ); ?>
									</button>
									<?php if ( $logo_desktop_dark_id ) : ?>
										<button type="button" class="button button-link-delete jlbpartners-remove-btn" data-target="logo_desktop_dark">
											<?php esc_html_e( 'Remove', 'jlbpartners-core' ); ?>
										</button>
									<?php endif; ?>
									<p class="description"><?php esc_html_e( 'Dark logo displayed on desktop devices when background is light (Recommended size: 400x100px PNG with transparency)', 'jlbpartners-core' ); ?></p>
								</div>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Mobile Dark Logo', 'jlbpartners-core' ); ?></th>
							<td>
								<div class="jlbpartners-image-upload">
									<div class="jlbpartners-image-preview">
										<?php
										$logo_mobile_dark_id = get_option( 'jlbpartners_logo_mobile_dark' );
										if ( $logo_mobile_dark_id ) {
											$logo_url = wp_get_attachment_url( $logo_mobile_dark_id );
											$logo_alt = get_post_meta( $logo_mobile_dark_id, '_wp_attachment_image_alt', true );
											if ( ! $logo_alt ) {
												$logo_alt = get_bloginfo( 'name' );
											}
											if ( $logo_url ) {
												echo '<img src="' . esc_url( $logo_url ) . '" alt="' . esc_attr( $logo_alt ) . '" style="max-width: 300px; height: auto;">';
											}
										} else {
											echo '<span class="description">' . esc_html__( 'No mobile dark logo uploaded', 'jlbpartners-core' ) . '</span>';
										}
										?>
									</div>
									<input type="hidden" id="jlbpartners_logo_mobile_dark" name="jlbpartners_logo_mobile_dark" value="<?php echo esc_attr( $logo_mobile_dark_id ); ?>" />
									<button type="button" class="button jlbpartners-upload-btn" data-target="logo_mobile_dark">
										<?php esc_html_e( 'Upload Mobile Dark Logo', 'jlbpartners-core' ); ?>
									</button>
									<?php if ( $logo_mobile_dark_id ) : ?>
										<button type="button" class="button button-link-delete jlbpartners-remove-btn" data-target="logo_mobile_dark">
											<?php esc_html_e( 'Remove', 'jlbpartners-core' ); ?>
										</button>
									<?php endif; ?>
									<p class="description"><?php esc_html_e( 'Dark logo displayed on mobile devices ≤992px when background is light (Recommended size: 200x50px PNG with transparency). If not set, desktop dark logo will be used.', 'jlbpartners-core' ); ?></p>
								</div>
							</td>
						</tr>
							<tr>
								<th scope="row"><?php esc_html_e( 'Placeholder Image', 'jlbpartners-core' ); ?></th>
								<td>
									<div class="jlbpartners-image-upload">
										<div class="jlbpartners-image-preview">
											<?php
											$placeholder_id = get_option( 'jlbpartners_placeholder_image' );
											if ( $placeholder_id ) {
												$placeholder_url = wp_get_attachment_url( $placeholder_id );
												$placeholder_alt = get_post_meta( $placeholder_id, '_wp_attachment_image_alt', true );
												if ( ! $placeholder_alt ) {
													$placeholder_alt = esc_html__( 'Placeholder image', 'jlbpartners-core' );
												}
												if ( $placeholder_url ) {
													echo '<img src="' . esc_url( $placeholder_url ) . '" alt="' . esc_attr( $placeholder_alt ) . '" style="max-width: 300px; height: auto;">';
												}
											} else {
												echo '<span class="description">' . esc_html__( 'No placeholder image uploaded', 'jlbpartners-core' ) . '</span>';
											}
											?>
										</div>
										<input type="hidden" id="jlbpartners_placeholder_image" name="jlbpartners_placeholder_image" value="<?php echo esc_attr( $placeholder_id ); ?>" />
										<button type="button" class="button jlbpartners-upload-btn" data-target="placeholder_image">
											<?php esc_html_e( 'Upload Placeholder', 'jlbpartners-core' ); ?>
										</button>
										<?php if ( $placeholder_id ) : ?>
											<button type="button" class="button button-link-delete jlbpartners-remove-btn" data-target="placeholder_image">
												<?php esc_html_e( 'Remove', 'jlbpartners-core' ); ?>
											</button>
										<?php endif; ?>
										<p class="description"><?php esc_html_e( 'Default placeholder for components (1200x600px recommended)', 'jlbpartners-core' ); ?></p>
									</div>
								</td>
							</tr>
						</table>
						
					<?php submit_button( __( 'Save Logo Settings', 'jlbpartners-core' ) ); ?>
				</form>
			</div>
			
		</div>
		</div>
	</div>

	<!-- Footer Tab -->
	<div id="tab-footer" class="jlbpartners-tab-content">
		
		<!-- Footer Settings Card -->
		<div class="jlbpartners-card">
			<h2>
				<?php esc_html_e( 'Footer Settings', 'jlbpartners-core' ); ?>
			</h2>
			<p class="description"><?php esc_html_e( 'Manage your footer content including contact information and copyright text.', 'jlbpartners-core' ); ?></p>
			
			<form method="post" action="options.php">
				<?php settings_fields( 'jlbpartners_footer' ); ?>
				
				<!-- Contact Information -->
				<h3>
					<?php esc_html_e( 'Contact Information', 'jlbpartners-core' ); ?>
					<span class="jlbpartners-preview-icon dashicons dashicons-visibility" data-preview="footer-settings" title="<?php esc_attr_e( 'Preview location', 'jlbpartners-core' ); ?>"></span>
				</h3>
				<table class="form-table">
					<tr>
						<th scope="row"><label for="jlbpartners_footer_address"><?php esc_html_e( 'Address', 'jlbpartners-core' ); ?></label></th>
						<td>
							<?php
							$address_content = get_option( 'jlbpartners_footer_address', '' );
							wp_editor(
								$address_content,
								'jlbpartners_footer_address',
								array(
									'textarea_name' => 'jlbpartners_footer_address',
									'textarea_rows' => 5,
									'media_buttons' => false,
									'teeny'         => false,
									'quicktags'     => true,
									'tinymce'       => array(
										'toolbar1'      => 'bold,italic,underline,link',
										'toolbar2'      => '',
										'forced_root_block' => false,
										'force_br_newlines' => true,
										'force_p_newlines'  => false,
									),
								)
							);
							?>
							<p class="description"><?php esc_html_e( 'Physical address. Press Shift+Enter for line breaks, or use the Code tab to add <br> tags manually.', 'jlbpartners-core' ); ?></p>
						</td>
					</tr>
				</table>

				<!-- Section Divider -->
				<hr style="margin: 40px 0; border: none; border-top: 2px solid #f0f0f1;">

				<!-- Footer Bottom Section -->

				<?php submit_button( __( 'Save All Footer Settings', 'jlbpartners-core' ) ); ?>
			</form>
		</div>
		
	</div>

	<!-- System Info Tab -->
	<div id="tab-system" class="jlbpartners-tab-content">
		<div class="jlbpartners-card">
			<h2><?php esc_html_e( 'System Information', 'jlbpartners-core' ); ?></h2>
			<table class="form-table">
				<tr>
					<th scope="row"><?php esc_html_e( 'Theme Version', 'jlbpartners-core' ); ?></th>
					<td><?php echo esc_html( wp_get_theme()->get( 'Version' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'WordPress Version', 'jlbpartners-core' ); ?></th>
					<td><?php echo esc_html( get_bloginfo( 'version' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'PHP Version', 'jlbpartners-core' ); ?></th>
					<td><?php echo esc_html( phpversion() ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Active Plugins', 'jlbpartners-core' ); ?></th>
					<td><?php echo esc_html( count( get_option( 'active_plugins', array() ) ) ); ?></td>
				</tr>
			</table>
		</div>

		<div class="jlbpartners-card">
			<h2><?php esc_html_e( 'Required Plugins', 'jlbpartners-core' ); ?></h2>
			<?php
			$required_plugins = array(
				'advanced-custom-fields-pro/acf.php' => 'Advanced Custom Fields Pro',
			);

			$active_plugins = get_option( 'active_plugins', array() );
			$all_active     = true;

			echo '<ul>';
			foreach ( $required_plugins as $plugin_path => $plugin_name ) {
				$is_active = in_array( $plugin_path, $active_plugins, true );
				$all_active = $all_active && $is_active;
				$status_icon = $is_active ? '✓' : '✗';
				$status_color = $is_active ? 'green' : 'red';
				
				echo '<li style="color: ' . esc_attr( $status_color ) . '; padding: 5px 0;">';
				echo '<strong>' . esc_html( $status_icon ) . '</strong> ';
				echo esc_html( $plugin_name );
				if ( ! $is_active ) {
					echo ' <span style="color: #999;">(' . esc_html__( 'Not Active', 'jlbpartners-core' ) . ')</span>';
				}
				echo '</li>';
			}
			echo '</ul>';

			if ( $all_active ) {
				echo '<p style="color: green; font-weight: 600;">✓ ' . esc_html__( 'All required plugins are active', 'jlbpartners-core' ) . '</p>';
			} else {
				echo '<p style="color: red; font-weight: 600;">✗ ' . esc_html__( 'Some required plugins are not active', 'jlbpartners-core' ) . '</p>';
			}
			?>
		</div>
	</div>

</div>
