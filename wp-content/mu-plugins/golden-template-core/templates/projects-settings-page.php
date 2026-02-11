<?php
/**
 * Project Listing Settings Page Template
 *
 * @package GoldenTemplate_Core
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap golden-template-settings">
	<h1><?php esc_html_e( 'Project Listing Settings', 'golden-template-core' ); ?></h1>
	<p class="subtitle">
		<?php esc_html_e( 'Manage your project listing page settings including display options, pagination, and default filters', 'golden-template-core' ); ?>
	</p>

	<!-- Project Listing Settings Card -->
	<div class="golden-template-card">
		<h2>
			<?php esc_html_e( 'Project Listing Settings', 'golden-template-core' ); ?>
		</h2>
		<p class="description"><?php esc_html_e( 'Configure how projects are displayed on the listing page.', 'golden-template-core' ); ?></p>
		
		<form method="post" action="options.php">
			<?php settings_fields( 'golden_template_projects' ); ?>
			
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="golden_template_projects_page_title"><?php esc_html_e( 'Archive Page Title', 'golden-template-core' ); ?></label>
					</th>
					<td>
						<input 
							type="text" 
							id="golden_template_projects_page_title" 
							name="golden_template_projects_page_title" 
							value="<?php echo esc_attr( get_option( 'golden_template_projects_page_title', 'All Projects' ) ); ?>" 
							class="regular-text"
							maxlength="100"
						/>
						<p class="description"><?php esc_html_e( 'The title displayed at the top of the projects archive page (maximum 100 characters).', 'golden-template-core' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="golden_template_projects_per_page"><?php esc_html_e( 'Projects Per Page', 'golden-template-core' ); ?></label>
					</th>
					<td>
					<input 
						type="number" 
						id="golden_template_projects_per_page" 
						name="golden_template_projects_per_page" 
						value="<?php echo esc_attr( get_option( 'golden_template_projects_per_page', 2 ) ); ?>" 
						min="1" 
						max="100" 
						class="small-text"
					/>
					<p class="description"><?php esc_html_e( 'Number of projects to display per page (1-100).', 'golden-template-core' ); ?></p>
					</td>
				</tr>
			</table>

			<?php submit_button( __( 'Save Project Listing Settings', 'golden-template-core' ) ); ?>
		</form>
	</div>
</div>

