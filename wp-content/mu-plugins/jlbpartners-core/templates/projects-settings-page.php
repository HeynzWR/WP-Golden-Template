<?php
/**
 * Project Listing Settings Page Template
 *
 * @package JLBPartners_Core
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap jlbpartners-settings">
	<h1><?php esc_html_e( 'Project Listing Settings', 'jlbpartners-core' ); ?></h1>
	<p class="subtitle">
		<?php esc_html_e( 'Manage your project listing page settings including display options, pagination, and default filters', 'jlbpartners-core' ); ?>
	</p>

	<!-- Project Listing Settings Card -->
	<div class="jlbpartners-card">
		<h2>
			<?php esc_html_e( 'Project Listing Settings', 'jlbpartners-core' ); ?>
		</h2>
		<p class="description"><?php esc_html_e( 'Configure how projects are displayed on the listing page.', 'jlbpartners-core' ); ?></p>
		
		<form method="post" action="options.php">
			<?php settings_fields( 'jlbpartners_projects' ); ?>
			
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="jlbpartners_projects_page_title"><?php esc_html_e( 'Archive Page Title', 'jlbpartners-core' ); ?></label>
					</th>
					<td>
						<input 
							type="text" 
							id="jlbpartners_projects_page_title" 
							name="jlbpartners_projects_page_title" 
							value="<?php echo esc_attr( get_option( 'jlbpartners_projects_page_title', 'All Projects' ) ); ?>" 
							class="regular-text"
							maxlength="100"
						/>
						<p class="description"><?php esc_html_e( 'The title displayed at the top of the projects archive page (maximum 100 characters).', 'jlbpartners-core' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="jlbpartners_projects_per_page"><?php esc_html_e( 'Projects Per Page', 'jlbpartners-core' ); ?></label>
					</th>
					<td>
					<input 
						type="number" 
						id="jlbpartners_projects_per_page" 
						name="jlbpartners_projects_per_page" 
						value="<?php echo esc_attr( get_option( 'jlbpartners_projects_per_page', 2 ) ); ?>" 
						min="1" 
						max="100" 
						class="small-text"
					/>
					<p class="description"><?php esc_html_e( 'Number of projects to display per page (1-100).', 'jlbpartners-core' ); ?></p>
					</td>
				</tr>
			</table>

			<?php submit_button( __( 'Save Project Listing Settings', 'jlbpartners-core' ) ); ?>
		</form>
	</div>
</div>

