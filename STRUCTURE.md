# Repository Structure

Understanding the organization and architecture of this WordPress golden template.

## Overview

This is a WordPress-based project with a custom theme and MU (must-use) plugin. The structure follows WordPress VIP standards and best practices.

## Root Directory

```
.
├── wp-admin/                    # WordPress core (not modified)
├── wp-content/                  # Custom code lives here
│   ├── themes/
│   │   └── golden-template/        # Main custom theme ⭐
│   ├── mu-plugins/
│   │   ├── golden-template-core/   # Core functionality plugin ⭐
│   │   └── golden-template-core-loader.php
│   ├── plugins/                # Third-party plugins
│   └── uploads/                # Media files
├── wp-includes/                # WordPress core (not modified)
├── composer.json               # PHP dependencies
├── composer.lock
├── rename-project.sh           # Project rename script ⭐
├── rollback-rename.sh          # Rollback script ⭐
├── README.md                   # Main documentation ⭐
├── SETUP.md                    # Setup guide ⭐
├── RENAME-GUIDE.md             # Rename guide ⭐
├── CONTRIBUTING.md             # Dev guidelines ⭐
├── STRUCTURE.md                # This file ⭐
├── BLOCK_DEVELOPMENT_GUIDELINES.md  # Block creation guide ⭐
└── wp-config.php               # WordPress configuration

⭐ = Custom files (part of golden template)
```

## Theme Structure

### `wp-content/themes/golden-template/`

```
golden-template/
├── assets/                     # Theme assets
│   ├── css/
│   │   ├── admin/             # Backend styles
│   │   └── frontend/          # Frontend styles
│   ├── js/
│   │   ├── admin/             # Backend scripts
│   │   └── frontend/          # Frontend scripts
│   └── images/
│       ├── block-previews/    # Block preview images (for editor)
│       └── ...                # Other theme images
│
├── blocks/                     # ACF Blocks (main feature!)
│   ├── hero-section/
│   │   ├── fields.php         # ACF field definitions
│   │   ├── template.php       # Block template/renderer
│   │   └── hero-section.css   # Block-specific styles
│   ├── about/
│   ├── testimonial-card/
│   └── ...                    # Each block in its own folder
│
├── inc/                        # Theme includes/components
│   ├── blocks/
│   │   ├── block-helpers.php      # Helper functions for blocks
│   │   └── block-registration.php # Register all ACF blocks
│   ├── post-types/
│   │   └── projects.php       # Custom post type definitions
│   ├── taxonomies/
│   │   └── projects-taxonomies.php
│   ├── fields/
│   │   └── projects-cpt-fields.php # ACF fields for CPTs
│   ├── acf-config.php         # ACF configuration
│   ├── ajax-handlers.php      # AJAX endpoints
│   ├── asset-loading.php      # Smart asset enqueuing
│   ├── cache-optimization.php # Caching logic
│   ├── class-nav-walker.php   # Custom menu walker
│   ├── editor-customization.php # Gutenberg customization
│   └── template-functions.php # Template helper functions
│
├── template-parts/             # Reusable template parts
│   └── ...
│
├── templates/                  # Page templates
│   └── ...
│
├── styles/                     # Legacy/compiled styles
│   └── ...
│
├── 404.php                     # 404 page template
├── archive-projects.php        # Projects archive
├── footer.php                  # Site footer
├── functions.php               # Main theme file ⭐ IMPORTANT
├── header.php                  # Site header
├── index.php                   # Main template fallback
├── page.php                    # Default page template
├── single-projects.php         # Single project template
├── style.css                   # Theme header/metadata ⭐ IMPORTANT
└── screenshot.png              # Theme screenshot
```

### Key Theme Files

#### `functions.php`
The main theme file that:
- Defines constants (`GOLDEN_TEMPLATE_VERSION`, etc.)
- Loads all includes
- Registers menus and theme support
- Enqueues scripts and styles
- Contains template debugging system

#### `inc/blocks/block-registration.php`
Registers all ACF blocks. When you create a new block, you add it here:

```php
acf_register_block_type(
    array(
        'name'            => 'testimonial-card',
        'title'           => __( 'Testimonial Card', 'golden-template' ),
        'render_template' => get_template_directory() . '/blocks/testimonial-card/template.php',
        // ... more settings
    )
);
```

#### `inc/editor-customization.php`
Controls which blocks appear in the editor. Add your ACF blocks here:

```php
return array(
    'acf/hero-section',
    'acf/about',
    'acf/testimonial-card',
    // ... your blocks
);
```

## MU Plugin Structure

### `wp-content/mu-plugins/golden-template-core/`

```
golden-template-core/
├── assets/
│   ├── css/
│   │   └── admin.css          # Admin area styles
│   └── js/
│       └── admin.js           # Admin area scripts
│
├── includes/                   # Core functionality classes
│   ├── class-branding.php     # Logo/branding management
│   ├── class-logger.php       # Logging system
│   ├── class-plugin-checker.php # Plugin dependency checks
│   ├── class-settings.php     # Settings page
│   └── class-update-manager.php # Update management
│
└── golden-template-core.php       # Main plugin file
```

### MU Plugin Loader

`wp-content/mu-plugins/golden-template-core-loader.php`
- Loads the main MU plugin
- Must-use plugins don't support subdirectories, so we need this loader

## Block Architecture

Each ACF block follows this structure:

### Standard Block Structure

```
blocks/testimonial-card/
├── fields.php              # ACF field definitions
├── template.php            # Rendering logic
└── style.css              # Block-specific styles (optional)
```

### Field Definition (`fields.php`)

```php
<?php
/**
 * Testimonial Card Block - ACF Fields
 */

function golden_template_testimonial_card_fields() {
    acf_add_local_field_group(
        array(
            'key'    => 'group_testimonial_card',
            'title'  => 'Testimonial Card',
            'fields' => array(
                // Field definitions here
            ),
            'location' => array(
                array(
                    array(
                        'param'    => 'block',
                        'operator' => '==',
                        'value'    => 'acf/testimonial-card',
                    ),
                ),
            ),
        )
    );
}
add_action( 'acf/init', 'golden_template_testimonial_card_fields' );
```

### Template (`template.php`)

```php
<?php
/**
 * Testimonial Card Block Template
 */

// Get field values
$quote = get_field( 'quote' );
$author = get_field( 'author' );

// Block attributes
$block_id = 'testimonial-' . $block['id'];
$class_name = 'testimonial-card';

// Preview mode
if ( $is_preview && empty( $quote ) ) {
    golden_template_show_block_preview( 'testimonial-card', __( 'Testimonial Card Block', 'golden-template' ) );
    return;
}

// Skip if empty
if ( empty( $quote ) ) {
    return;
}
?>

<div id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>">
    <blockquote><?php echo esc_html( $quote ); ?></blockquote>
    <cite><?php echo esc_html( $author ); ?></cite>
</div>
```

## Asset Loading Strategy

### Smart Loading

Assets are loaded conditionally based on what's being rendered:

```php
// In functions.php
function golden_template_scripts() {
    // Always loaded
    wp_enqueue_style( 'golden-template-main', ... );
    
    // Conditionally loaded
    if ( is_singular( 'projects' ) ) {
        wp_enqueue_style( 'golden-template-single-project', ... );
    }
    
    // Block-specific (only when block is used)
    // Handled by ACF block registration's 'enqueue_assets'
}
```

### Asset Organization

```
assets/
├── css/
│   ├── admin/              # Admin-only styles
│   │   └── backend.css
│   └── frontend/           # Frontend styles
│       ├── main.css       # Core styles (always loaded)
│       ├── projects-listing.css
│       └── single-project.css
├── js/
│   ├── admin/
│   └── frontend/
│       ├── main.js        # Core scripts
│       ├── header.js      # Header/nav scripts
│       └── project-filters.js
└── images/
    ├── block-previews/    # Block preview images (editor)
    └── ...
```

## Data Flow

### How ACF Blocks Work

1. **User adds block** in editor
2. **Block registered** via `acf_register_block_type()` in `block-registration.php`
3. **Fields defined** in `blocks/{name}/fields.php`
4. **User fills fields** in block editor
5. **Template renders** using `blocks/{name}/template.php`
6. **Styles loaded** from `blocks/{name}/style.css` (if exists)

### Custom Post Types Flow

1. **CPT registered** in `inc/post-types/`
2. **Taxonomies registered** in `inc/taxonomies/`
3. **ACF fields defined** in `inc/fields/`
4. **Templates render** using `single-{post-type}.php` or `archive-{post-type}.php`

## Naming Conventions

### Files
- PHP: `kebab-case.php`
- CSS: `kebab-case.css`
- JS: `kebab-case.js`
- Directories: `kebab-case/`

### PHP Code
- Functions: `golden_template_function_name()`
- Classes: `GoldenTemplate_Class_Name`
- Constants: `GOLDEN_TEMPLATE_CONSTANT_NAME`
- Variables: `$snake_case`

### CSS
- Classes: `.block-name` or `.block-name__element`
- IDs: `#block-name`
- Custom properties: `--property-name`

### JavaScript
- Variables: `camelCase`
- Constants: `UPPER_SNAKE_CASE`
- Objects: `projectName`

## Common Directories to Ignore

These should be in `.gitignore`:

```
/node_modules/         # npm dependencies
/vendor/               # Composer dependencies
/wp-admin/             # WordPress core
/wp-includes/          # WordPress core
/.backup_*/            # Rename script backups
.DS_Store              # macOS files
*.log                  # Log files
```

## Development Workflow

1. **Start development** in `wp-content/themes/golden-template/`
2. **Create blocks** in `blocks/` folder
3. **Register blocks** in `inc/blocks/block-registration.php`
4. **Allow blocks** in `inc/editor-customization.php`
5. **Load block fields** in `functions.php`
6. **Test in editor** and on frontend
7. **Run PHPCS** before committing

## For More Information

- **Block Development**: See [BLOCK_DEVELOPMENT_GUIDELINES.md](BLOCK_DEVELOPMENT_GUIDELINES.md)
- **Setup Process**: See [SETUP.md](SETUP.md)
- **Development Standards**: See [CONTRIBUTING.md](CONTRIBUTING.md)

---

**Need clarification?** Contact your development team lead or refer to the specific documentation files.
