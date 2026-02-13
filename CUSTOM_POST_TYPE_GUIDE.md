# Custom Post Type Development Guide

> **📌 IMPORTANT - Golden Template Usage**
> 
> This repository is a **golden template** for WordPress projects. Before creating custom post types:
> 1. **Run the rename script first**: `./rename-project.sh your-project-name`
> 2. All references to "golden-template" will be updated to your project name
> 3. Follow this guide using YOUR project name, not "golden-template"
> 
> See [RENAME-GUIDE.md](RENAME-GUIDE.md) for detailed rename instructions.

---

This guide provides a comprehensive template and examples for adding custom post types (CPTs) to the Golden Template theme. The projects custom post type has been removed, but this guide will help future developers easily add new custom post types following WordPress best practices.

## Table of Contents

1. [File Structure](#file-structure)
2. [Step-by-Step Guide](#step-by-step-guide)
3. [Registration Template](#registration-template)
4. [Taxonomy Template](#taxonomy-template)
5. [ACF Fields Template](#acf-fields-template)
6. [Template Files](#template-files)
7. [Integration with functions.php](#integration-with-functionsphp)
8. [Best Practices](#best-practices)

---

## File Structure

When adding a custom post type, create the following file structure:

```
wp-content/themes/golden-template/
├── inc/
│   ├── post-types/
│   │   └── your-cpt-name.php          # CPT registration
│   ├── taxonomies/
│   │   └── your-cpt-taxonomies.php     # Custom taxonomies
│   └── fields/
│       └── your-cpt-fields.php         # ACF fields for CPT
├── single-your-cpt-name.php            # Single post template
└── archive-your-cpt-name.php           # Archive template (optional)
```

---

## Step-by-Step Guide

### Step 1: Create the Custom Post Type Registration File

Create `inc/post-types/your-cpt-name.php`:

```php
<?php
/**
 * Register [Your CPT Name] Custom Post Type
 *
 * @package GoldenTemplate
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register [Your CPT Name] CPT
 */
function golden_template_register_your_cpt_name_cpt() {

	$labels = array(
		'name'                  => _x( '[Plural Name]', 'Post Type General Name', 'golden-template' ),
		'singular_name'         => _x( '[Singular Name]', 'Post Type Singular Name', 'golden-template' ),
		'menu_name'             => __( '[Menu Name]', 'golden-template' ),
		'name_admin_bar'        => __( '[Admin Bar Name]', 'golden-template' ),
		'archives'              => __( '[Archive Name]', 'golden-template' ),
		'attributes'             => __( '[Attributes Name]', 'golden-template' ),
		'parent_item_colon'     => __( 'Parent [Item]:', 'golden-template' ),
		'all_items'             => __( 'All [Items]', 'golden-template' ),
		'add_new_item'          => __( 'Add New [Item]', 'golden-template' ),
		'add_new'               => __( 'Add New', 'golden-template' ),
		'new_item'              => __( 'New [Item]', 'golden-template' ),
		'edit_item'             => __( 'Edit [Item]', 'golden-template' ),
		'update_item'           => __( 'Update [Item]', 'golden-template' ),
		'view_item'             => __( 'View [Item]', 'golden-template' ),
		'view_items'            => __( 'View [Items]', 'golden-template' ),
		'search_items'          => __( 'Search [Item]', 'golden-template' ),
		'not_found'             => __( 'Not found', 'golden-template' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'golden-template' ),
		'featured_image'        => __( 'Featured Image', 'golden-template' ),
		'set_featured_image'    => __( 'Set featured image', 'golden-template' ),
		'remove_featured_image' => __( 'Remove featured image', 'golden-template' ),
		'use_featured_image'    => __( 'Use as featured image', 'golden-template' ),
		'insert_into_item'      => __( 'Insert into [item]', 'golden-template' ),
		'uploaded_to_this_item' => __( 'Uploaded to this [item]', 'golden-template' ),
		'items_list'            => __( '[Items] list', 'golden-template' ),
		'items_list_navigation' => __( '[Items] list navigation', 'golden-template' ),
		'filter_items_list'     => __( 'Filter [items] list', 'golden-template' ),
	);

	$args = array(
		'label'                 => __( '[Label]', 'golden-template' ),
		'description'           => __( '[Description]', 'golden-template' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
		'taxonomies'            => array( 'category', 'post_tag' ), // Add custom taxonomies here
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-admin-post', // Choose appropriate icon
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'post',
		'show_in_rest'          => true, // Enable Gutenberg editor
		'rewrite'               => array(
			'slug'       => 'your-cpt-slug',
			'with_front' => false,
		),
	);

	register_post_type( 'your_cpt_name', $args );
}
add_action( 'init', 'golden_template_register_your_cpt_name_cpt', 0 );
```

**Key Points:**
- Replace `your_cpt_name` with your actual CPT slug (use underscores, lowercase)
- Replace `[Placeholders]` with appropriate text
- Choose an appropriate `menu_icon` from [Dashicons](https://developer.wordpress.org/resource/dashicons/)
- Set `show_in_rest` to `true` to enable Gutenberg editor
- Adjust `supports` array based on what features you need

### Step 2: Create Custom Taxonomies (Optional)

Create `inc/taxonomies/your-cpt-taxonomies.php`:

```php
<?php
/**
 * Registers custom taxonomies for the [Your CPT Name] CPT
 *
 * @package GoldenTemplate
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register [Taxonomy Name] Taxonomy
 */
function golden_template_register_your_taxonomy_taxonomy() {

	$labels = array(
		'name'                       => _x( '[Taxonomy Plural]', 'Taxonomy General Name', 'golden-template' ),
		'singular_name'              => _x( '[Taxonomy Singular]', 'Taxonomy Singular Name', 'golden-template' ),
		'menu_name'                  => __( '[Taxonomy Name]', 'golden-template' ),
		'all_items'                  => __( 'All [Items]', 'golden-template' ),
		'parent_item'                => __( 'Parent [Item]', 'golden-template' ),
		'parent_item_colon'          => __( 'Parent [Item]:', 'golden-template' ),
		'new_item_name'              => __( 'New [Item] Name', 'golden-template' ),
		'add_new_item'               => __( 'Add New [Item]', 'golden-template' ),
		'edit_item'                  => __( 'Edit [Item]', 'golden-template' ),
		'update_item'                => __( 'Update [Item]', 'golden-template' ),
		'view_item'                  => __( 'View [Item]', 'golden-template' ),
		'separate_items_with_commas' => __( 'Separate [items] with commas', 'golden-template' ),
		'add_or_remove_items'        => __( 'Add or remove [items]', 'golden-template' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'golden-template' ),
		'popular_items'              => __( 'Popular [Items]', 'golden-template' ),
		'search_items'              => __( 'Search [Items]', 'golden-template' ),
		'not_found'                 => __( 'Not Found', 'golden-template' ),
		'no_terms'                  => __( 'No [items]', 'golden-template' ),
		'items_list'                 => __( '[Items] list', 'golden-template' ),
		'items_list_navigation'      => __( '[Items] list navigation', 'golden-template' ),
	);

	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true, // true for categories, false for tags
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => false,
		'show_in_rest'               => true,
		'rewrite'                    => array(
			'slug' => 'your-taxonomy-slug',
		),
	);

	register_taxonomy( 'your_taxonomy_name', array( 'your_cpt_name' ), $args );
}

// Register taxonomies on init, after CPT registration
add_action( 'init', 'golden_template_register_your_taxonomy_taxonomy', 1 );
```

### Step 3: Create ACF Fields (Optional)

Create `inc/fields/your-cpt-fields.php`:

```php
<?php
/**
 * [Your CPT Name] CPT Fields
 *
 * @package GoldenTemplate
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register ACF fields for [Your CPT Name] CPT
 */
function golden_template_your_cpt_name_cpt_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_your_cpt_name',
			'title'                 => '[Your CPT Name] Fields',
			'fields'                => array(
				// Add your fields here
				array(
					'key'               => 'field_your_field_name',
					'label'             => 'Field Label',
					'name'              => 'field_name',
					'type'              => 'text',
					'instructions'      => '',
					'required'         => 0,
					'conditional_logic' => 0,
					'wrapper'          => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'    => '',
					'placeholder'       => '',
					'prepend'          => '',
					'append'           => '',
					'maxlength'        => '',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'your_cpt_name',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
		)
	);
}
add_action( 'acf/init', 'golden_template_your_cpt_name_cpt_fields' );
```

### Step 4: Create Template Files

#### Single Post Template

Create `single-your-cpt-name.php`:

```php
<?php
/**
 * The template for displaying single [Your CPT Name] posts
 *
 * @package GoldenTemplate
 */

get_header();
?>

<main id="main" class="site-main">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<header class="entry-header">
				<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
			</header>

			<div class="entry-content">
				<?php
				the_content();

				// Display ACF fields if needed
				// $field_value = get_field( 'field_name' );
				// if ( $field_value ) {
				//     echo esc_html( $field_value );
				// }
				?>
			</div>
		</article>
		<?php
	endwhile;
	?>
</main>

<?php
get_footer();
```

#### Archive Template (Optional)

Create `archive-your-cpt-name.php`:

```php
<?php
/**
 * The template for displaying [Your CPT Name] archives
 *
 * @package GoldenTemplate
 */

get_header();
?>

<main id="main" class="site-main">
	<header class="page-header">
		<?php
		the_archive_title( '<h1 class="page-title">', '</h1>' );
		the_archive_description( '<div class="archive-description">', '</div>' );
		?>
	</header>

	<?php
	if ( have_posts() ) :
		?>
		<div class="posts-container">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<header class="entry-header">
						<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '">', '</a></h2>' ); ?>
					</header>

					<div class="entry-summary">
						<?php the_excerpt(); ?>
					</div>
				</article>
				<?php
			endwhile;
			?>
		</div>

		<?php
		// Pagination
		the_posts_pagination(
			array(
				'mid_size'  => 2,
				'prev_text' => __( '← Previous', 'golden-template' ),
				'next_text' => __( 'Next →', 'golden-template' ),
			)
		);
		?>
		<?php
	else :
		?>
		<p><?php esc_html_e( 'No posts found.', 'golden-template' ); ?></p>
		<?php
	endif;
	?>
</main>

<?php
get_footer();
```

### Step 5: Integrate with functions.php

Add the following to `functions.php` in the appropriate section:

```php
/**
 * Custom Post Types
 */
require_once GOLDEN_TEMPLATE_THEME_DIR . '/inc/post-types/your-cpt-name.php';

/**
 * Custom Taxonomies
 */
require_once GOLDEN_TEMPLATE_THEME_DIR . '/inc/taxonomies/your-cpt-taxonomies.php';

/**
 * CPT Fields
 */
require_once GOLDEN_TEMPLATE_THEME_DIR . '/inc/fields/your-cpt-fields.php';
```

### Step 6: Enqueue Styles and Scripts (If Needed)

If your CPT needs custom CSS or JavaScript, add to `functions.php` in the `golden_template_scripts()` function:

```php
// [Your CPT Name] styles (only on archive pages)
if ( is_post_type_archive( 'your_cpt_name' ) || is_page_template( 'archive-your-cpt-name.php' ) ) {
	wp_enqueue_style(
		'golden-template-your-cpt-name-listing',
		GOLDEN_TEMPLATE_THEME_URI . '/assets/css/frontend/your-cpt-name-listing.css',
		array( 'golden-template-main' ),
		golden_template_get_asset_version( 'assets/css/frontend/your-cpt-name-listing.css' )
	);
}

// Single [Your CPT Name] styles (only on single post pages)
if ( is_singular( 'your_cpt_name' ) ) {
	wp_enqueue_style(
		'golden-template-single-your-cpt-name',
		GOLDEN_TEMPLATE_THEME_URI . '/assets/css/frontend/single-your-cpt-name.css',
		array( 'golden-template-main' ),
		golden_template_get_asset_version( 'assets/css/frontend/single-your-cpt-name.css' )
	);
}
```

---

## Best Practices

### 1. Naming Conventions

- **CPT Slug**: Use lowercase with underscores (e.g., `your_cpt_name`)
- **Function Names**: Use prefix `golden_template_` followed by descriptive name
- **File Names**: Use kebab-case (e.g., `your-cpt-name.php`)
- **Template Files**: Follow WordPress naming: `single-{cpt-slug}.php`, `archive-{cpt-slug}.php`

### 2. Security

- Always sanitize input: Use `sanitize_text_field()`, `sanitize_email()`, etc.
- Always escape output: Use `esc_html()`, `esc_attr()`, `esc_url()`, etc.
- Use `$wpdb->prepare()` for database queries
- Verify nonces for AJAX requests

### 3. Performance

- Use `show_in_rest => true` to enable Gutenberg (better performance)
- Consider caching for archive queries
- Use `pre_get_posts` to modify queries efficiently

### 4. Query Modifications

If you need to modify the main query for your CPT archive, add to `functions.php`:

```php
/**
 * Modify the main query for [Your CPT Name] archive
 */
function golden_template_modify_your_cpt_name_query( $query ) {
	if ( ! is_admin() && $query->is_main_query() && is_post_type_archive( 'your_cpt_name' ) ) {
		// Set posts per page
		$query->set( 'posts_per_page', 12 );
		
		// Set ordering
		$query->set( 'orderby', 'date' );
		$query->set( 'order', 'DESC' );
	}
}
add_action( 'pre_get_posts', 'golden_template_modify_your_cpt_name_query' );
```

### 5. Flush Rewrite Rules

After creating a new CPT, you may need to flush rewrite rules:

1. Go to WordPress Admin → Settings → Permalinks
2. Click "Save Changes" (no need to change anything)

Or add this to your theme's activation hook:

```php
function golden_template_activate() {
	golden_template_register_your_cpt_name_cpt();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'golden_template_activate' );
```

---

## Example: Complete Implementation

Here's a complete example for a "Portfolio" custom post type:

### File: `inc/post-types/portfolio.php`

```php
<?php
/**
 * Register Portfolio Custom Post Type
 *
 * @package GoldenTemplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function golden_template_register_portfolio_cpt() {
	$labels = array(
		'name'                  => _x( 'Portfolio Items', 'Post Type General Name', 'golden-template' ),
		'singular_name'         => _x( 'Portfolio Item', 'Post Type Singular Name', 'golden-template' ),
		'menu_name'             => __( 'Portfolio', 'golden-template' ),
		// ... other labels
	);

	$args = array(
		'label'                 => __( 'Portfolio', 'golden-template' ),
		'description'           => __( 'Portfolio items showcase', 'golden-template' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		'taxonomies'            => array( 'portfolio_category' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-portfolio',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'post',
		'show_in_rest'          => true,
		'rewrite'               => array( 'slug' => 'portfolio' ),
	);

	register_post_type( 'portfolio', $args );
}
add_action( 'init', 'golden_template_register_portfolio_cpt', 0 );
```

---

## Additional Resources

- [WordPress Custom Post Types](https://developer.wordpress.org/reference/functions/register_post_type/)
- [WordPress Taxonomies](https://developer.wordpress.org/reference/functions/register_taxonomy/)
- [ACF Field Types](https://www.advancedcustomfields.com/resources/)
- [WordPress Template Hierarchy](https://developer.wordpress.org/themes/basics/template-hierarchy/)

---

## Notes

- The projects custom post type has been removed from this theme
- This guide provides a template for future developers to easily add new custom post types
- Always follow WordPress coding standards and best practices
- Test thoroughly before deploying to production
