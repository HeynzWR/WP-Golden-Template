# Block Development Guidelines

> **📌 IMPORTANT - Golden Template Usage**
> 
> This repository is a **golden template** for WordPress projects. Before creating blocks:
> 1. **Run the rename script first**: `./rename-project.sh your-project-name`
> 2. All references to "jlbpartners" will be updated to your project name
> 3. Follow this guide using YOUR project name, not "jlbpartners"
> 
> See [RENAME-GUIDE.md](RENAME-GUIDE.md) for detailed rename instructions.

---

**AI-Agent Ready Block Creation System**

This guide enables AI agents to automatically create complete, professional ACF blocks for your WordPress theme. Developers simply specify the block name and desired functionality, and the AI agent handles all implementation details following established patterns.

## 🤖 For AI Agents: How to Use This Guide

When a developer requests a new block, follow this process:

1. **Parse the request** for block name and functionality requirements
2. **Use the [Component Templates](#component-templates)** section to generate all files
3. **Follow the [File Creation Checklist](#file-creation-checklist)** to ensure nothing is missed
4. **Apply [Standard Patterns](#standard-patterns)** for common functionality
5. **Use [Naming Conventions](#naming-conventions)** for auto-population to work
6. **Generate [Registration Code](#registration-code)** using the templates

## 🎯 For Developers: How to Request a Block

Simply tell an AI agent:

```
"Create a [BLOCK_NAME] block with [FUNCTIONALITY_LIST] using the block development guidelines"
```

**Example:**
```
"Create a testimonial-slider block with image, quote, author name, company, star rating, and navigation arrows using the block development guidelines"
```

The AI will generate all necessary files, register the block, and provide setup instructions.

---

## Table of Contents

### For AI Agents
1. [Component Templates](#component-templates) - Copy-paste templates for all files
2. [Standard Patterns](#standard-patterns) - Reusable field and template patterns
3. [File Creation Checklist](#file-creation-checklist) - Ensure nothing is missed
4. [Registration Code](#registration-code) - Auto-generate registration
5. [Naming Conventions](#naming-conventions) - Critical for auto-functionality

### For Developers
6. [Request Format](#request-format) - How to ask AI for blocks
7. [Manual Tasks](#manual-tasks) - What developers must do themselves
8. [Testing & Verification](#testing--verification) - How to verify the block works
9. [Troubleshooting](#troubleshooting) - Common issues and fixes

---

## Component Templates

### Fields File Template (`blocks/{block-name}/fields.php`)

```php
<?php
/**
 * {Block Title} Block - ACF Fields
 *
 * @package JLBPartners
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register ACF fields for {Block Title} Block
 */
function jlbpartners_{block_name}_fields() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    acf_add_local_field_group(
        array(
            'key'    => 'group_{block_name}',
            'title'  => '{Block Title}',
            'fields' => array(
                // CONTENT TAB
                array(
                    'key'   => 'field_{block_name}_content_tab',
                    'label' => 'Content',
                    'type'  => 'tab',
                ),
                
                // ADD CONTENT FIELDS HERE
                
                // MEDIA TAB (if block has images/videos)
                array(
                    'key'   => 'field_{block_name}_media_tab',
                    'label' => 'Media',
                    'type'  => 'tab',
                ),
                
                // ADD MEDIA FIELDS HERE
                
                // SETTINGS TAB (if block has layout/style options)
                array(
                    'key'   => 'field_{block_name}_settings_tab',
                    'label' => 'Settings',
                    'type'  => 'tab',
                ),
                
                // ADD SETTINGS FIELDS HERE
            ),
            'location' => array(
                array(
                    array(
                        'param'    => 'block',
                        'operator' => '==',
                        'value'    => 'acf/{block-name}',
                    ),
                ),
            ),
        )
    );
}
add_action( 'acf/init', 'jlbpartners_{block_name}_fields' );
```

### Template File Template (`blocks/{block-name}/template.php`)

```php
<?php
/**
 * {Block Title} Block Template
 *
 * @package JLBPartners
 *
 * @param array  $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool   $is_preview True during AJAX preview.
 * @param int    $post_id The post ID this block is saved to.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// GET FIELD VALUES
// ADD get_field() calls here for all fields

// CREATE BLOCK ATTRIBUTES
$block_id = '{block-name}-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
    $block_id = $block['anchor'];
}

$class_name = 'section {block-name}';
if ( ! empty( $block['className'] ) ) {
    $class_name .= ' ' . $block['className'];
}

// PREVIEW MODE HANDLING
// Enhanced preview mode - show image preview if no content.
if ( $is_preview && empty( $required_field ) ) {
    jlbpartners_show_block_preview( '{block-name}', __( '{Block Title} Block', 'jlbpartners' ) );
    return;
}

// SKIP RENDERING IF REQUIRED FIELDS EMPTY
if ( empty( $required_field ) ) {
    return;
}

// RENDER OUTPUT
?>
<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>">
    <div class="container">
        <!-- ADD BLOCK CONTENT HERE -->
    </div>
</section>
```

### CSS File Template (`blocks/{block-name}/style.css`) - Optional

```css
/* {Block Title} Block */
.section.{block-name} {
    padding: clamp(2rem, 4vw, 4rem) 0;
}

/* Block Elements */
.{block-name}__element {
    /* Element styles */
}

/* Responsive */
@media screen and (max-width: 767px) {
    .section.{block-name} {
        /* Mobile styles */
    }
}

@media screen and (min-width: 768px) {
    .section.{block-name} {
        /* Tablet styles */
    }
}

@media screen and (min-width: 1199px) {
    .section.{block-name} {
        /* Desktop styles */
    }
}
```

---

## Standard Patterns

### Text Field Pattern
```php
array(
    'key'      => 'field_{block_name}_{field_name}',
    'label'    => '{Field Label}',
    'name'     => '{field_name}',
    'type'     => 'text',
    'required' => {0|1},
),
```

### Textarea Field Pattern
```php
array(
    'key'  => 'field_{block_name}_{field_name}',
    'label' => '{Field Label}',
    'name'  => '{field_name}',
    'type'  => 'textarea',
    'rows'  => 3,
),
```

### WYSIWYG Field Pattern
```php
array(
    'key'          => 'field_{block_name}_{field_name}',
    'label'        => '{Field Label}',
    'name'         => '{field_name}',
    'type'         => 'wysiwyg',
    'tabs'         => 'visual',
    'toolbar'      => 'basic',
    'media_upload' => 0,
),
```

### Image Field with Full Accessibility Pattern
```php
// Main image field
array(
    'key'           => 'field_{block_name}_image',
    'label'         => 'Image',
    'name'          => 'image',
    'type'          => 'image',
    'required'      => {0|1},
    'return_format' => 'id',
    'preview_size'  => 'medium',
),

// Accessibility accordion
array(
    'key'          => 'field_{block_name}_image_accessibility_accordion',
    'label'        => 'Accessibility Settings',
    'type'         => 'accordion',
    'open'         => 0,
    'multi_expand' => 1,
    'endpoint'     => 0,
),
array(
    'key'           => 'field_{block_name}_image_role',
    'label'         => 'Image Role',
    'name'          => 'image_role',
    'type'          => 'radio',
    'instructions'  => 'Is this image decorative or informational?',
    'choices'       => array(
        'decorative'    => 'Decorative (hidden from screen readers)',
        'informational' => 'Informational (needs description)',
    ),
    'default_value' => 'informational',
),
array(
    'key'               => 'field_{block_name}_image_alt',
    'label'             => 'ALT Text',
    'name'              => 'image_alt',
    'type'              => 'text',
    'instructions'      => 'Describe what the image shows',
    'conditional_logic' => array(
        array(
            array(
                'field'    => 'field_{block_name}_image_role',
                'operator' => '==',
                'value'    => 'informational',
            ),
        ),
    ),
    'maxlength' => 200,
),
array(
    'key'          => 'field_{block_name}_image_caption',
    'label'        => 'Caption',
    'name'         => 'image_caption',
    'type'         => 'text',
    'instructions' => 'Optional visible caption',
    'maxlength'    => 150,
),
array(
    'key'               => 'field_{block_name}_image_description',
    'label'             => 'Description',
    'name'              => 'image_description',
    'type'              => 'textarea',
    'instructions'      => 'Detailed context for screen reader users',
    'conditional_logic' => array(
        array(
            array(
                'field'    => 'field_{block_name}_image_role',
                'operator' => '==',
                'value'    => 'informational',
            ),
        ),
    ),
    'rows'      => 3,
    'maxlength' => 300,
),
array(
    'key'      => 'field_{block_name}_image_accessibility_accordion_end',
    'label'    => 'Accessibility End',
    'type'     => 'accordion',
    'endpoint' => 1,
),
```

### CTA/Link Field Pattern
```php
array(
    'key'           => 'field_{block_name}_cta',
    'label'         => 'Call to Action',
    'name'          => 'cta',
    'type'          => 'link',
    'instructions'  => 'Add a button or link',
    'return_format' => 'array',
),
```

### Select/Radio Field Pattern
```php
array(
    'key'           => 'field_{block_name}_{field_name}',
    'label'         => '{Field Label}',
    'name'          => '{field_name}',
    'type'          => 'radio', // or 'select'
    'choices'       => array(
        'option1' => 'Option 1 Label',
        'option2' => 'Option 2 Label',
    ),
    'default_value' => 'option1',
),
```

### Title with Styled Spans Pattern
```php
array(
    'key'        => 'field_{block_name}_title_group',
    'label'      => 'Title',
    'name'       => 'title_group',
    'type'       => 'group',
    'layout'     => 'row',
    'sub_fields' => array(
        array(
            'key'   => 'field_{block_name}_title_main',
            'label' => 'Main Text',
            'name'  => 'main',
            'type'  => 'text',
        ),
        array(
            'key'   => 'field_{block_name}_title_highlight',
            'label' => 'Highlighted Text',
            'name'  => 'highlight',
            'type'  => 'text',
        ),
    ),
),
```

### Image Template Implementation Pattern
```php
// Get image data
$image = get_field( 'image' );
if ( $image ) {
    $image_url = wp_get_attachment_image_url( $image, 'large' );
    $image_meta = wp_get_attachment_metadata( $image );
    $image_width = isset( $image_meta['width'] ) ? $image_meta['width'] : 800;
    $image_height = isset( $image_meta['height'] ) ? $image_meta['height'] : 600;
    
    // Accessibility fields
    $image_role = get_field( 'image_role' ) ?: 'informational';
    $image_alt = get_field( 'image_alt' );
    $image_caption = get_field( 'image_caption' );
    
    // Auto-populate alt text if not provided
    $alt_text = $image_alt ? $image_alt : get_post_meta( $image, '_wp_attachment_image_alt', true );
    if ( ! $alt_text ) {
        $alt_text = get_the_title( $image );
    }
}
?>

<?php if ( $image ) : ?>
    <figure <?php echo 'decorative' === $image_role ? 'aria-hidden="true"' : ''; ?>>
        <img 
            src="<?php echo esc_url( $image_url ); ?>" 
            alt="<?php echo 'decorative' === $image_role ? '' : esc_attr( $alt_text ); ?>" 
            width="<?php echo esc_attr( $image_width ); ?>" 
            height="<?php echo esc_attr( $image_height ); ?>"
            loading="lazy"
            <?php if ( 'decorative' === $image_role ) : ?>
                role="presentation"
            <?php endif; ?>
        >
        <?php if ( $image_caption ) : ?>
            <figcaption><?php echo esc_html( $image_caption ); ?></figcaption>
        <?php endif; ?>
    </figure>
<?php endif; ?>
```

---

## File Creation Checklist

### 1. Create Block Files
- [ ] Create `/blocks/{block-name}/fields.php` using template
- [ ] Create `/blocks/{block-name}/template.php` using template with enhanced preview mode
- [ ] Create `/blocks/{block-name}/style.css` (only if custom styles needed)
- [ ] Create block preview image: `/assets/images/block-previews/{block-name}-preview.png`

### 2. Update Registration Files
- [ ] Add to `/functions.php`: `require_once JLBPARTNERS_THEME_DIR . '/blocks/{block-name}/fields.php';`
- [ ] Add to `/inc/blocks/block-registration.php`: Full block registration code
- [ ] Add to `/inc/editor-customization.php`: `'acf/{block-name}'` in allowed blocks array
- [ ] Add to `/inc/asset-loading.php`: CSS path in `$block_css_map` array (if custom CSS exists)

### 3. Verify Auto-Population
- [ ] Image fields named: `image`, `background_image`, `photo`
- [ ] Accessibility fields named: `{field}_alt`, `{field}_caption`, `{field}_description`, `{field}_role`
- [ ] Video fields named: `video`, `background_video`, `video_file`
- [ ] Video accessibility fields: `video_title`, `video_description`

### 4. Code Quality & Testing
- [ ] Run coding standards test: `./vendor/bin/phpcs --standard=WordPressVIPMinimum wp-content/themes/jlbpartners/`
- [ ] Ensure all PHP files pass WordPress VIP standards (no errors, warnings acceptable)
- [ ] Test auto-population fetch buttons work correctly
- [ ] Verify enhanced preview mode displays correctly

---

## Registration Code

### Block Registration Template
```php
// {Block Title} Block.
acf_register_block_type(
    array(
        'name'            => '{block-name}',
        'title'           => __( '{Block Title}', 'jlbpartners' ),
        'description'     => __( '{Block description}', 'jlbpartners' ),
        'render_template' => get_template_directory() . '/blocks/{block-name}/template.php',
        'category'        => 'jlbpartners-blocks',
        'icon'            => array(
            'src'        => '{dashicon-name}', // e.g., 'layout', 'id-alt', 'format-gallery'
            'foreground' => '#00a400',
        ),
        'keywords'        => array( '{keyword1}', '{keyword2}', '{keyword3}' ),
        'mode'            => 'preview',
        'supports'        => array(
            'align'  => false,
            'mode'   => true,
            'jsx'    => true,
            'anchor' => true,
        ),
        'enqueue_assets'  => function() {
            // Only include if block has custom CSS
            wp_enqueue_style(
                '{block-name}-block',
                get_template_directory_uri() . '/blocks/{block-name}/style.css',
                array(),
                JLBPARTNERS_VERSION
            );
        },
    )
);
```

### Functions.php Addition
```php
require_once JLBPARTNERS_THEME_DIR . '/blocks/{block-name}/fields.php';
```

### Editor Customization Addition
```php
// Add to the return array in jlbpartners_allowed_block_types()
'acf/{block-name}',
```

### Smart Asset Loading Addition (if custom CSS)
```php
// Add to $block_css_map array
'acf/{block-name}' => '/blocks/{block-name}/style.css',
```

---

## Naming Conventions

### Critical Field Names (for auto-population)

**Image Fields (source):**
- `image` ✅
- `background_image` ✅
- `photo` ✅
- `img` ✅

**Image Accessibility Fields:**
- `image_alt` ✅ (triggers fetch button)
- `image_caption` ✅ (triggers fetch button)
- `image_description` ✅ (triggers fetch button)
- `image_role` ✅ (decorative/informational)

**Video Fields (source):**
- `video` ✅
- `background_video` ✅
- `video_file` ✅

**Video Accessibility Fields:**
- `video_title` ✅ (triggers fetch button)
- `video_description` ✅ (triggers fetch button)

**Standard Content Fields:**
- `title` - Main title
- `subtitle` - Secondary title
- `description` - Main description
- `content` - WYSIWYG content
- `cta` - Call-to-action link

**Layout/Style Fields:**
- `alignment` - Text alignment
- `layout` - Layout variant
- `style` - Style variant
- `background_type` - Background selector

### Block Naming Convention
- **Block name:** `kebab-case` (e.g., `feature-card`, `testimonial-slider`)
- **Function names:** `snake_case` with `jlbpartners_` prefix
- **CSS classes:** `kebab-case` with BEM-ish structure

---

## Request Format

### How Developers Should Request Blocks

**Format:**
```
"Create a {block-name} block with {functionality-list} using the block development guidelines"
```

**Examples:**

```
"Create a testimonial-card block with image, quote, author name, company, and star rating using the block development guidelines"
```

```
"Create a pricing-table block with title, price, features list, CTA button, and highlight option using the block development guidelines"
```

```
"Create a team-member block with photo, name, job title, bio, and social links using the block development guidelines"
```

```
"Create a stats-counter block with icon, number, label, and animation trigger using the block development guidelines"
```

### AI Agent Response Should Include:

1. **All generated files** (fields.php, template.php, style.css if needed)
2. **Registration code** for all required files
3. **Manual tasks list** for the developer
4. **Testing instructions**
5. **Troubleshooting tips**

---

## Manual Tasks

### What Developers Must Do Themselves

#### 1. Block Icon Selection
- **Choose Dashicon:** Browse https://developer.wordpress.org/resource/dashicons/
- **Common choices:** `layout`, `id-alt`, `format-gallery`, `admin-users`, `chart-bar`
- **Update registration:** Change `{dashicon-name}` in registration code

#### 2. Block Preview Image (Required)
- **Create:** Block preview image showing the component in use
- **Save as:** `/assets/images/block-previews/{block-name}-preview.png`
- **Purpose:** Used by `jlbpartners_show_block_preview()` function in enhanced preview mode
- **Naming:** Must match block folder name exactly (e.g., `sponsor-logos-preview.png`)

#### 3. Block Screenshot (Optional)
- **Create:** 1200x600px screenshot of the block in use
- **Save as:** `/blocks/{block-name}/screenshot.png`
- **Purpose:** Block directory listings, documentation

#### 3. Content Creation
- **Add real content** to test the block thoroughly
- **Create example content** for other team members
- **Test edge cases** (very long text, missing images, etc.)

#### 4. Custom Styling (If Needed)
- **Review generated CSS** and adjust for design system
- **Add responsive breakpoints** specific to content
- **Test cross-browser compatibility**

#### 5. Performance Optimization
- **Optimize images** used in block examples
- **Test loading performance** with real content
- **Verify lazy loading** works correctly

---

## Testing & Verification

### Automated Checks (AI Agent Should Verify)
- [ ] All files created with correct naming
- [ ] Registration code added to all required files
- [ ] Field naming follows auto-population conventions
- [ ] Accessibility patterns implemented correctly
- [ ] Preview mode placeholder included
- [ ] Security (escaping, sanitization) implemented

### Manual Testing (Developer Must Do)
- [ ] Hard refresh editor (Cmd/Ctrl + Shift + R)
- [ ] Block appears in your theme's custom block category
- [ ] All fields display and save correctly
- [ ] Auto-population buttons appear and work
- [ ] Preview matches frontend exactly
- [ ] Responsive design works on all devices
- [ ] Accessibility testing with screen reader
- [ ] Performance testing with real content

### Code Quality Checks

**Required:** Run coding standards test on entire theme:
```bash
# Run PHPCS on entire theme (required for all new blocks)
./vendor/bin/phpcs --standard=WordPressVIPMinimum wp-content/themes/jlbpartners/

# Should show 0 ERRORS (warnings are acceptable)
# New blocks must not introduce any new errors
```

**Optional:** Check specific block files:
```bash
# Run PHPCS on generated files only
phpcs --standard=WordPressVIPMinimum blocks/{block-name}/

# Check for common issues
grep -r "echo \$" blocks/{block-name}/  # Should be empty (no unescaped output)
grep -r "http://" blocks/{block-name}/  # Should be empty (no hardcoded URLs)
```

---

## Troubleshooting

### Common Issues & Solutions

**Block doesn't appear in editor:**
- ✅ Check `inc/editor-customization.php` has `'acf/{block-name}'` in `jlbpartners_allowed_block_types()` function
- ✅ Hard refresh editor (Cmd/Ctrl + Shift + R)
- ✅ Check browser console for JavaScript errors

**Auto-population buttons missing:**
- ✅ Verify field naming: `image_alt`, `image_caption`, `video_title`, etc.
- ✅ Check source field exists: `image`, `video`, `background_image`, etc.
- ✅ Ensure fields are in same field group

**Preview doesn't match frontend:**
- ✅ Check template escaping: use `esc_html()`, `esc_attr()`, `esc_url()`
- ✅ Verify CSS classes match between preview and frontend
- ✅ Test with real content, not just placeholder text

**Template debugging not showing block:**
- ✅ Enable debugging: `define( 'JLBPARTNERS_DEBUG_TEMPLATES', true );` in functions.php
- ✅ Check block is actually rendering (not returning early due to empty fields)
- ✅ Verify block name matches registration

**CSS not loading:**
- ✅ Check `inc/smart-asset-loading.php` has correct path in `$block_css_map`
- ✅ Verify CSS file exists at specified path
- ✅ Clear any caching plugins

**PHPCS errors:**
- ✅ All output must be escaped: `echo esc_html( $var );`
- ✅ All input must be sanitized: `sanitize_text_field( $_POST['field'] );`
- ✅ No hardcoded URLs: use `home_url()`, `get_permalink()`, etc.
- ✅ Proper text domains: `__( 'Text', 'jlbpartners' )`

### Debug Commands
```bash
# Enable WordPress debugging
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );

# Enable template debugging
define( 'JLBPARTNERS_DEBUG_TEMPLATES', true );

# Check error log
tail -f wp-content/debug.log

# Test block registration
wp eval "print_r(WP_Block_Type_Registry::get_instance()->get_all_registered());"
```

This guide now enables any AI agent to automatically create complete, professional ACF blocks by following the established patterns and standards!

---

## WordPress VIP Coding Standards

### 🚨 Critical: Follow These Standards From Day One

**Why**: Following coding standards from the beginning prevents errors and saves time. The theme uses WordPress VIP Coding Standards which are stricter than regular WordPress standards.

**Check your code**: Run `./vendor/bin/phpcs --standard=WordPressVIPMinimum wp-content/themes/jlbpartners/blocks/{block-name}/` before committing.

### Security Requirements

#### 1. Always Escape Output
```php
// ❌ WRONG - Never output unescaped data
echo $title;
echo '<div>' . $content . '</div>';

// ✅ CORRECT - Always escape output
echo esc_html( $title );
echo '<div>' . wp_kses_post( $content ) . '</div>';
echo '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $alt_text ) . '">';
```

#### 2. Always Sanitize Input
```php
// ❌ WRONG - Never use raw $_GET, $_POST, $_REQUEST
$search = $_GET['search'];

// ✅ CORRECT - Always sanitize input
$search = isset( $_GET['search'] ) ? sanitize_text_field( wp_unslash( $_GET['search'] ) ) : '';

// For arrays
$items = isset( $_POST['items'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['items'] ) ) : array();
```

#### 3. Use Nonce Verification for Forms
```php
// ❌ WRONG - Processing form data without verification
if ( isset( $_POST['submit'] ) ) {
    // Process form
}

// ✅ CORRECT - Always verify nonce
if ( isset( $_POST['submit'] ) && wp_verify_nonce( $_POST['nonce'], 'action_name' ) ) {
    // Process form
}

// For public parameters (pagination, filters), add phpcs ignore
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public pagination parameter
if ( isset( $_GET['page'] ) ) {
    $page = intval( $_GET['page'] );
}
```

#### 4. Avoid Global Variable Conflicts
```php
// ❌ WRONG - These override WordPress globals
foreach ( $categories as $cat ) { }
foreach ( $links as $link ) { }
foreach ( $tags as $tag ) { }

// ✅ CORRECT - Use different variable names
foreach ( $categories as $category_item ) { }
foreach ( $links as $link_item ) { }
foreach ( $tags as $tag_item ) { }
```

### Performance Requirements

#### 1. Use Prepared Statements for Database Queries
```php
// ❌ WRONG - SQL injection risk
$results = $wpdb->get_results( "SELECT * FROM {$wpdb->posts} WHERE post_title = '$title'" );

// ✅ CORRECT - Use prepared statements
$results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->posts} WHERE post_title = %s", $title ) );
```

#### 2. Avoid Expensive Operations
```php
// ❌ WRONG - Expensive operations in loops
foreach ( $posts as $post ) {
    $meta = get_post_meta( $post->ID ); // Database query in loop
}

// ✅ CORRECT - Batch operations
$post_ids = wp_list_pluck( $posts, 'ID' );
$all_meta = get_post_meta_batch( $post_ids );
```

#### 3. Use WordPress Functions Instead of Direct File Access
```php
// ❌ WRONG - Direct file operations
file_get_contents( '/path/to/file' );
include '/path/to/template.php';

// ✅ CORRECT - Use WordPress functions
get_template_part( 'template-parts/content' );
wp_remote_get( $url );
```

### Code Quality Requirements

#### 1. Proper Function Documentation
```php
/**
 * Get formatted post data for display
 *
 * @param int    $post_id Post ID.
 * @param string $format  Output format ('html' or 'array').
 * @return array|string Formatted post data.
 */
function jlbpartners_get_post_data( $post_id, $format = 'array' ) {
    // Function implementation
}
```

#### 2. Handle Unused Parameters
```php
// ❌ WRONG - Unused parameter causes warning
function my_function( $used_param, $unused_param ) {
    return $used_param;
}

// ✅ CORRECT - Add phpcs ignore comment
function my_function( $used_param, $unused_param ) {
    // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- WordPress signature requirement
    $ignored_param = $unused_param;
    return $used_param;
}
```

#### 3. Proper Text Domain Usage
```php
// ❌ WRONG - Missing or incorrect text domain
__( 'Hello World' );
__( 'Hello World', 'wrong-domain' );

// ✅ CORRECT - Always use 'jlbpartners' text domain
__( 'Hello World', 'jlbpartners' );
esc_html__( 'Hello World', 'jlbpartners' );
```

### Block-Specific Standards

#### 1. Template File Structure
```php
<?php
/**
 * Block Name Block Template
 *
 * @package JLBPartners
 *
 * @param array  $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool   $is_preview True during AJAX preview.
 * @param int    $post_id The post ID this block is saved to.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// GET FIELD VALUES - Always use get_field()
$title = get_field( 'title' );
$content = get_field( 'content' );
$image = get_field( 'image' );

// VALIDATE REQUIRED FIELDS
if ( empty( $title ) ) {
    return; // Don't render if required fields are empty
}

// CREATE BLOCK ATTRIBUTES
$block_id = 'block-name-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
    $block_id = $block['anchor'];
}

$class_name = 'ss block-name';
if ( ! empty( $block['className'] ) ) {
    $class_name .= ' ' . $block['className'];
}

// PREVIEW MODE HANDLING
if ( $is_preview && empty( $title ) ) {
    ?>
    <div class="jlbpartners-block--preview">
        <!-- Preview placeholder -->
    </div>
    <?php
    return;
}

// RENDER OUTPUT - Always escape
?>
<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>">
    <div class="container">
        <h2><?php echo esc_html( $title ); ?></h2>
        <div><?php echo wp_kses_post( $content ); ?></div>
    </div>
</section>
```

#### 2. Fields File Structure
```php
<?php
/**
 * Block Name Block - ACF Fields
 *
 * @package JLBPartners
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register ACF fields for Block Name Block
 */
function jlbpartners_block_name_fields() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    acf_add_local_field_group(
        array(
            'key'    => 'group_block_name',
            'title'  => 'Block Name',
            'fields' => array(
                array(
                    'key'      => 'field_block_name_title',
                    'label'    => __( 'Title', 'jlbpartners' ),
                    'name'     => 'title',
                    'type'     => 'text',
                    'required' => 1,
                ),
                // More fields...
            ),
            'location' => array(
                array(
                    array(
                        'param'    => 'block',
                        'operator' => '==',
                        'value'    => 'acf/block-name',
                    ),
                ),
            ),
        )
    );
}
add_action( 'acf/init', 'jlbpartners_block_name_fields' );
```

### Common PHPCS Ignore Comments

Use these when necessary (sparingly):

```php
// For WordPress signature requirements (unused parameters)
// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- WordPress signature requirement

// For public parameters (pagination, filters)
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public pagination parameter

// For development/admin-only tools
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Debug tool for admins only

// For theme activation hooks
// phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rewrite_rules_flush_rewrite_rules -- Theme activation hook

// For hardcoded CSS content
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS content is hardcoded and safe

// For placeholder text in ACF fields
// phpcs:ignore WordPressVIPMinimum.JS.HTMLExecutingFunctions.script -- This is placeholder text for ACF field
```

### Pre-Commit Checklist

Before committing any block code:

- [ ] Run `./vendor/bin/phpcs --standard=WordPressVIPMinimum blocks/your-block/`
- [ ] All output is escaped (`esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()`)
- [ ] All input is sanitized (`sanitize_text_field()`, etc.)
- [ ] No global variable conflicts (`$cat`, `$link`, `$tag`, etc.)
- [ ] Text domain is 'jlbpartners' for all translatable strings
- [ ] No hardcoded URLs (use `home_url()`, `get_permalink()`, etc.)
- [ ] Proper PHPDoc blocks for all functions
- [ ] Exit if accessed directly check at top of files

### Quick Fix Commands

```bash
# Check specific file
./vendor/bin/phpcs --standard=WordPressVIPMinimum blocks/your-block/fields.php

# Check all blocks
./vendor/bin/phpcs --standard=WordPressVIPMinimum blocks/

# Auto-fix what's possible (limited)
./vendor/bin/phpcbf --standard=WordPressVIPMinimum blocks/your-block/

# Ignore CSS/JS files (focus on PHP)
./vendor/bin/phpcs --standard=WordPressVIPMinimum --ignore="*.css,*.js" blocks/
```

---

## Existing Helper Functions & Reusable Patterns

### Available Helper Functions

The theme provides several helper functions you can reuse in your blocks:

#### Image & Media Helpers
```php
// Get placeholder image (from theme settings)
$placeholder_url = jlbpartners_get_placeholder_image();

// Get post featured image with ADA compliance and placeholder fallback
// Note: This function may not exist yet - check functions.php for available helpers
```

#### Blog & Content Helpers
```php
// Get blog settings (useful for content-related blocks)
// Note: Check functions.php and inc/template-functions.php for available helper functions
```

#### Template & Asset Helpers
```php
// Get theme logo (custom or WordPress default)
$logo_html = jlbpartners_get_logo();

// Check if current post uses ACF components
$uses_acf = jlbpartners_uses_acf_components();
```

### Reusable Field Patterns

#### Standard Image Field with Full Accessibility
Copy this pattern from `hero-section/fields.php` for any image field:

```php
// Main image field
array(
    'key'           => 'field_block_image',
    'label'         => 'Image',
    'name'          => 'image',
    'type'          => 'image',
    'required'      => 1,
    'return_format' => 'id',
    'preview_size'  => 'large',
),

// Accessibility accordion (copy entire pattern from hero-section)
// Includes: image_role, image_alt, image_caption, image_description
```

#### Standard CTA/Link Field
```php
array(
    'key'           => 'field_block_cta',
    'label'         => 'Call to Action',
    'name'          => 'cta',
    'type'          => 'link',
    'instructions'  => 'Add a button or link',
    'required'      => 0,
    'return_format' => 'array',
),
```

#### Standard Title with Styled Spans
Use the pattern from existing blocks for titles with highlighted text:

```php
// Single row group with two text fields
array(
    'key'        => 'field_block_title_group',
    'label'      => 'Title',
    'name'       => 'title_group',
    'type'       => 'group',
    'layout'     => 'row',
    'sub_fields' => array(
        array(
            'key'   => 'field_block_title_main',
            'label' => 'Main Text',
            'name'  => 'main',
            'type'  => 'text',
        ),
        array(
            'key'   => 'field_block_title_highlight',
            'label' => 'Highlighted Text',
            'name'  => 'highlight',
            'type'  => 'text',
        ),
    ),
),
```

#### Standard Background Options
```php
array(
    'key'     => 'field_block_background_type',
    'label'   => 'Background Type',
    'name'    => 'background_type',
    'type'    => 'radio',
    'choices' => array(
        'none'  => 'None',
        'color' => 'Color',
        'image' => 'Image',
        'video' => 'Video',
    ),
    'default_value' => 'none',
),
// Add conditional fields for each type
```

### CSS Utility Classes

Use these existing utility classes in your templates:

#### Layout Classes
```css
.section               /* Base section/block class */
.container             /* Max-width container */
.container--narrow     /* Narrower container (if available) */
.container--wide       /* Wider container (if available) */
```

#### Typography Classes
```css
.l-heading-1           /* H1 styling */
.l-heading-2           /* H2 styling */
.l-body-text           /* Body text styling */
.l-small-text          /* Small text styling */
.l-link                /* Styled link */
.l-tag                 /* Tag/label styling */
```

#### Spacing Classes
```css
.visually-hidden       /* Screen reader only */
.sr-only               /* Screen reader only (alias) */
```

#### Component Classes
```css
.l-button              /* Button styling */
.l-card                /* Card component */
.search-result         /* Search result item */
```

### AJAX & Search Functionality

The theme includes built-in AJAX functionality you can extend:

#### Existing AJAX Handlers
```php
// In inc/ajax-handlers.php
// Check inc/ajax-handlers.php for available AJAX handlers
// Common patterns: jlbpartners_ajax_* functions
```

#### Blog Filtering System
```php
// Blog filtering functionality
// Check inc/ajax-handlers.php for blog-related AJAX handlers
```

#### Adding New AJAX Handlers
Follow this pattern in `inc/ajax-handlers.php`:

```php
function your_custom_ajax_handler() {
    // Verify nonce for security
    check_ajax_referer( 'jlbpartners_nonce', 'nonce' );
    
    // Get and sanitize input
    $input = isset( $_POST['input'] ) ? sanitize_text_field( wp_unslash( $_POST['input'] ) ) : '';
    
    // Process logic here
    
    // Return JSON response
    wp_send_json_success( $response_data );
}
add_action( 'wp_ajax_your_action', 'your_custom_ajax_handler' );
add_action( 'wp_ajax_nopriv_your_action', 'your_custom_ajax_handler' );
```

#### JavaScript AJAX Pattern
```javascript
// Use existing jlbpartnersData object (already localized)
const formData = new FormData();
formData.append('action', 'your_action');
formData.append('nonce', window.jlbpartnersData.nonce);
formData.append('data', yourData);

fetch(window.jlbpartnersData.ajaxUrl, {
    method: 'POST',
    body: formData
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        // Handle success
    }
});
```

### Security Features

The theme includes security enhancements:

#### REST API Security
- Protects sensitive endpoints (`/wp/v2/users`, `/wp/v2/settings`, etc.)
- Keeps public content accessible for search and frontend functionality
- Configurable in functions.php

#### XML-RPC Disabled
- Prevents brute force attacks via XML-RPC
- Safe to disable for modern WordPress sites

### Template Debugging System

Enable comprehensive debugging:

```php
// In functions.php
define( 'JLBPARTNERS_DEBUG_TEMPLATES', true );
```

**Debug panel shows:**
- Template file being used (`page.php`, `single.php`, etc.)
- Page type and post ID
- All ACF blocks rendered with their template files
- Gutenberg blocks used
- Template parts loaded

**Toggle on/off easily** - Set to `false` to disable all debugging

---

## Block Architecture

### Core Principles

1. **Component-Based**: Each block is self-contained with its own fields, template, and styles
2. **Accessibility-First**: WCAG 2.2 Level AA compliance built-in from the start
3. **Progressive Enhancement**: Works without JavaScript, enhanced with it
4. **Performance-Optimized**: Smart asset loading, lazy loading where appropriate
5. **Editor-Friendly**: WYSIWYG preview that matches frontend exactly

### Block Anatomy

```
blocks/
└── block-name/
    ├── fields.php          # ACF field definitions
    ├── template.php        # PHP rendering template
    ├── style.css           # Block-specific styles (optional)
    └── README.md          # Block documentation (optional)
```

---

## File Structure

### 1. Fields File (`fields.php`)

**Purpose**: Define all ACF fields for the block

**Structure**:
```php
<?php
/**
 * Block Name - ACF Fields
 *
 * @package JLBPartners
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register ACF fields for Block Name
 */
function jlbpartners_block_name_fields() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    acf_add_local_field_group(
        array(
            'key'    => 'group_block_name',
            'title'  => 'Block Name',
            'fields' => array(
                // Fields here
            ),
            'location' => array(
                array(
                    array(
                        'param'    => 'block',
                        'operator' => '==',
                        'value'    => 'acf/block-name',
                    ),
                ),
            ),
        )
    );
}
add_action( 'acf/init', 'jlbpartners_block_name_fields' );
```

### 2. Template File (`template.php`)

**Purpose**: Render the block output

**Structure**:
```php
<?php
/**
 * Block Name Block Template
 *
 * @package JLBPartners
 *
 * @param array  $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool   $is_preview True during AJAX preview.
 * @param int    $post_id The post ID this block is saved to.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 1. Get all field values
// 2. Create block attributes
// 3. Handle preview mode
// 4. Render output
```

### 3. Style File (`style.css`)

**Purpose**: Block-specific styles (if needed beyond main.css)

**Note**: Only create if block needs unique styles not in main.css

---

## ACF Field Organization

### Tab Structure

Organize fields into logical tabs for better UX:

```php
// Tab Examples:
'Content'         // Main content fields
'Media'          // Images, videos, files
'Settings'       // Layout, styling options
'Accessibility'  // ADA/WCAG fields
'Advanced'       // Optional technical fields
```

### Field Naming Convention

**Critical for Auto-Population:** Field names must follow these conventions for automatic functionality to work.

#### Standard Field Names
```php
// Main content fields
'title'                   // Main title
'subtitle'                // Secondary title
'description'             // Main description/content
'content'                 // WYSIWYG content

// Media fields (triggers auto-population)
'image'                   // Main image
'background_image'        // Background image
'video'                   // Main video
'background_video'        // Background video

// Accessibility fields (auto-detected by system)
'image_alt'               // Alt text for image
'image_caption'           // Image caption
'image_description'       // Detailed image description
'image_role'              // decorative/informational
'video_title'             // Video title
'video_description'       // Video description

// Interactive elements
'cta'                     // Call-to-action link
'button'                  // Button link
'link'                    // Generic link

// Layout/styling
'alignment'               // Text alignment
'background_type'         // Background type selector
'style'                   // Style variant
'layout'                  // Layout variant
```

#### Block-Specific Prefixes (Optional)
```php
// Use when you have multiple similar fields
'hero_title'              // ✅ Good for clarity
'hero_background_image'   // ✅ Good for multiple images
'section_title'           // ✅ Good for sections

// Avoid when unnecessary
'hero_cta'                // ❌ Just use 'cta' if only one
'hero_description'        // ❌ Just use 'description' if only one
```

#### Auto-Population Naming Rules

**Image Accessibility Fields:**
- Must end with `_alt`, `_caption`, `_description`, `_role`
- System finds source image by looking for: `image`, `background_image`, `img`, `photo`

**Video Accessibility Fields:**
- Must contain `video_title` or end with `_title`, `_description`
- System finds source video by looking for: `video`, `background_video`, `video_file`

**Examples:**
```php
'image'           → 'image_alt', 'image_caption'           // ✅ Auto-detected
'hero_image'      → 'hero_image_alt', 'hero_image_caption' // ✅ Auto-detected
'background_image' → 'background_image_alt'               // ✅ Auto-detected
'video'           → 'video_title', 'video_description'    // ✅ Auto-detected
```

### Field Organization Pattern

Based on Hero Section, organize fields as:

1. **Content First** - What the user sees
2. **Media Second** - Visual elements
3. **Settings Third** - Layout/styling options
4. **Accessibility Last** - But always included

### Required vs Optional Fields

```php
// Required fields (core functionality)
'required' => 1,

// Optional fields (enhancements)
'required' => 0,
```

**Rule**: Make only essential fields required. Provide sensible defaults.

---

## Accessibility (ADA/WCAG 2.2)

### Mandatory Accessibility Features

Every block must include:

1. **Semantic HTML** - Proper heading hierarchy, landmarks
2. **ARIA Labels** - For interactive elements
3. **Alt Text** - For all images
4. **Color Contrast** - Minimum 4.5:1 for text
5. **Keyboard Navigation** - All interactive elements accessible
6. **Screen Reader Support** - Hidden descriptions where needed
7. **Focus States** - Visible focus indicators

### Complete Image/Video Accessibility Pattern

**IMPORTANT:** When building any component with image or video fields, always reference the `hero-section` component as the canonical example. It includes comprehensive accessibility fields, auto-population from WordPress media library, and WCAG 2.2 compliance.

#### Image Fields Pattern

```php
// 1. Image Field (ID format for metadata access)
array(
    'key'           => 'field_{block}_image',
    'label'         => 'Image',
    'name'          => 'image',
    'type'          => 'image',
    'required'      => 1,
    'return_format' => 'id',  // ← Use ID for auto-population
    'preview_size'  => 'large',
),

// 2. Accessibility Accordion
array(
    'key'               => 'field_{block}_image_accessibility_accordion',
    'label'             => 'Accessibility Settings',
    'type'              => 'accordion',
    'open'              => 0,
    'multi_expand'      => 1,
    'endpoint'          => 0,
),

// 3. Image Role Field
array(
    'key'           => 'field_{block}_image_role',
    'label'         => 'Image Role',
    'name'          => 'image_role',
    'type'          => 'radio',
    'instructions'  => 'Is this image decorative or does it convey important information?',
    'choices'       => array(
        'decorative'    => 'Decorative (hidden from screen readers)',
        'informational' => 'Informational (needs description)',
    ),
    'default_value' => 'informational',
),

// 4. ALT Text Field (conditional on informational)
array(
    'key'               => 'field_{block}_image_alt',
    'label'             => 'ALT Text',
    'name'              => 'image_alt',
    'type'              => 'text',
    'instructions'      => 'Describe what the image shows',
    'conditional_logic' => array(
        array(
            array(
                'field'    => 'field_{block}_image_role',
                'operator' => '==',
                'value'    => 'informational',
            ),
        ),
    ),
    'maxlength' => 200,
),

// 5. Caption Field (optional, visible to all users)
array(
    'key'          => 'field_{block}_image_caption',
    'label'        => 'Caption',
    'name'         => 'image_caption',
    'type'         => 'text',
    'instructions' => 'Optional visible caption for the image',
    'maxlength'    => 150,
),

// 6. Description Field (conditional on informational, for screen readers)
array(
    'key'               => 'field_{block}_image_description',
    'label'             => 'Description',
    'name'              => 'image_description',
    'type'              => 'textarea',
    'instructions'      => 'Detailed context for screen reader users',
    'conditional_logic' => array(
        array(
            array(
                'field'    => 'field_{block}_image_role',
                'operator' => '==',
                'value'    => 'informational',
            ),
        ),
    ),
    'rows'      => 3,
    'maxlength' => 300,
),

// 7. Close Accessibility Accordion
array(
    'key'      => 'field_{block}_image_accessibility_accordion_end',
    'label'    => 'Accessibility End',
    'type'     => 'accordion',
    'endpoint' => 1,
),
```

#### Video Fields Pattern

```php
// 1. Video Field
array(
    'key'           => 'field_{block}_video',
    'label'         => 'Video File',
    'name'          => 'video',
    'type'          => 'file',
    'instructions'  => 'Upload video file (MP4 recommended)',
    'required'      => 1,
    'return_format' => 'array',
    'mime_types'    => 'mp4,webm',
),

// 2. Accessibility Accordion
array(
    'key'          => 'field_{block}_video_accessibility_accordion',
    'label'        => 'Accessibility Settings',
    'type'         => 'accordion',
    'open'         => 0,
    'multi_expand' => 1,
    'endpoint'     => 0,
),

// 3. Video Title
array(
    'key'          => 'field_{block}_video_title',
    'label'        => 'Video Title',
    'name'         => 'video_title',
    'type'         => 'text',
    'instructions' => 'Descriptive title for the video',
    'maxlength'    => 100,
),

// 4. Video Description
array(
    'key'          => 'field_{block}_video_description',
    'label'        => 'Video Description',
    'name'         => 'video_description',
    'type'         => 'textarea',
    'instructions' => 'Describe what happens in the video for screen reader users',
    'rows'         => 3,
    'maxlength'    => 300,
),

// 5. Transcript URL (WCAG requirement)
array(
    'key'          => 'field_{block}_video_transcript_url',
    'label'        => 'Transcript URL',
    'name'         => 'video_transcript_url',
    'type'         => 'url',
    'instructions' => 'Link to text transcript (WCAG requirement)',
),

// 6. Close Accessibility Accordion
array(
    'key'      => 'field_{block}_video_accessibility_accordion_end',
    'label'    => 'Accessibility End',
    'type'     => 'accordion',
    'endpoint' => 1,
),

// 7. Poster Image (separate accordion)
array(
    'key'          => 'field_{block}_video_poster_accordion',
    'label'        => 'Poster Image',
    'type'         => 'accordion',
    'open'         => 0,
    'multi_expand' => 1,
    'endpoint'     => 0,
),
array(
    'key'           => 'field_{block}_video_poster',
    'label'         => 'Fallback Image',
    'name'          => 'video_poster',
    'type'          => 'image',
    'instructions'  => 'Image displayed before video loads and for users with reduced motion preferences',
    'return_format' => 'id',
),
array(
    'key'      => 'field_{block}_video_poster_accordion_end',
    'label'    => 'Poster End',
    'type'     => 'accordion',
    'endpoint' => 1,
),
```

#### Template Implementation with Auto-Population

```php
// Get image accessibility fields
$image_role        = get_field( 'image_role' ) ?: 'informational';
$image_alt         = get_field( 'image_alt' );
$image_caption     = get_field( 'image_caption' );
$image_description = get_field( 'image_description' );

// Get image data from WordPress
$image_url  = wp_get_attachment_image_url( $image, 'full' );
$image_meta = wp_get_attachment_metadata( $image );
$image_width  = isset( $image_meta['width'] ) ? $image_meta['width'] : 1920;
$image_height = isset( $image_meta['height'] ) ? $image_meta['height'] : 1080;

// Auto-populate alt text from WordPress media library if not provided
$alt_text = $image_alt ? $image_alt : get_post_meta( $image, '_wp_attachment_image_alt', true );

// Fallback to image title if no alt text
if ( ! $alt_text ) {
    $alt_text = get_the_title( $image );
}
?>

<figure aria-hidden="<?php echo 'decorative' === $image_role ? 'true' : 'false'; ?>">
    <img 
        src="<?php echo esc_url( $image_url ); ?>" 
        alt="<?php echo 'decorative' === $image_role ? '' : esc_attr( $alt_text ); ?>" 
        width="<?php echo esc_attr( $image_width ); ?>" 
        height="<?php echo esc_attr( $image_height ); ?>"
        loading="lazy"
        <?php if ( 'decorative' === $image_role ) : ?>
            role="presentation"
        <?php endif; ?>
    >
    
    <?php if ( $image_caption ) : ?>
        <figcaption><?php echo esc_html( $image_caption ); ?></figcaption>
    <?php endif; ?>
    
    <?php if ( 'informational' === $image_role && $image_description && $image_description !== $alt_text ) : ?>
        <figcaption class="visually-hidden">
            <?php echo esc_html( $image_description ); ?>
        </figcaption>
    <?php endif; ?>
</figure>
```

#### JavaScript Fetch Button Integration (Automatic)

**Good news!** The fetch button system is now **fully automatic**. You don't need to configure anything!

**How it works:**

The `/assets/js/admin/acf-media-fetch.js` file automatically:

1. **Scans all ACF fields** in your component
2. **Detects accessibility fields** by naming convention:
   - Fields ending with `_alt` → ALT text
   - Fields ending with `_caption` → Caption
   - Fields ending with `_description` → Description
   - Fields ending with `_title` or containing `video_title` → Title
3. **Finds the source media field** in the same block context:
   - For image fields: looks for `image`, `background_image`, `img`, `photo`
   - For video fields: looks for `video`, `background_video`, `video_file`
4. **Adds fetch buttons** automatically with the download icon (📥)

**Field Naming Requirements:**

Just follow these naming conventions and the system will automatically detect them:

```php
// Image accessibility fields
'image_alt'         // ✅ Auto-detected
'background_image_alt' // ✅ Auto-detected
'photo_alt'         // ✅ Auto-detected

'image_caption'     // ✅ Auto-detected
'image_description' // ✅ Auto-detected

// Video accessibility fields
'video_title'       // ✅ Auto-detected
'video_description' // ✅ Auto-detected
```

**Source Media Field Names:**

The system will automatically find these media field names:
- **Image fields**: `image`, `background_image`, `img`, `photo`
- **Video fields**: `video`, `background_video`, `video_file`

**No manual configuration needed!** As long as you follow the naming conventions from the hero-section pattern, fetch buttons will automatically appear.

### Interactive Element Accessibility Pattern

```php
// For buttons, links, CTAs
array(
    'key'          => 'field_{block}_element_aria_label',
    'label'        => 'ARIA Label',
    'name'         => 'element_aria_label',
    'type'         => 'text',
    'instructions' => 'Custom label for screen readers (optional)',
    'required'     => 0,
    'maxlength'    => 150,
),
```

### Section/Region Accessibility Pattern

```php
// For major sections
array(
    'key'          => 'field_{block}_section_aria_label',
    'label'        => 'Section ARIA Label',
    'name'         => 'section_aria_label',
    'type'         => 'text',
    'instructions' => 'Descriptive label for screen readers (optional)',
    'required'     => 0,
    'maxlength'    => 100,
),
```

### Accessibility Accordion Pattern

Group accessibility fields in collapsible accordions:

```php
// Open accordion
array(
    'key'          => 'field_{block}_accessibility_accordion',
    'label'        => 'Accessibility Settings',
    'type'         => 'accordion',
    'open'         => 0,          // Collapsed by default
    'multi_expand' => 1,          // Allow multiple open
    'endpoint'     => 0,
),

// ... accessibility fields here ...

// Close accordion
array(
    'key'      => 'field_{block}_accessibility_accordion_end',
    'label'    => 'Accessibility End',
    'type'     => 'accordion',
    'endpoint' => 1,
),
```

### Template Implementation

```php
// Get accessibility fields
$aria_label = get_field( 'section_aria_label' );
$image_role = get_field( 'image_role' ) ?: 'decorative';
$image_alt  = get_field( 'image_alt' );

// Apply in template
?>
<section 
    role="region"
    aria-label="<?php echo $aria_label ? esc_attr( $aria_label ) : esc_attr( wp_strip_all_tags( $title ) ); ?>"
>
    <img 
        src="<?php echo esc_url( $image_url ); ?>"
        alt="<?php echo 'decorative' === $image_role ? '' : esc_attr( $image_alt ); ?>"
        <?php if ( 'decorative' === $image_role ) : ?>
            role="presentation"
        <?php endif; ?>
    >
</section>
```

---

## Template Development

### Structure Pattern

```php
<?php
// 1. SECURITY CHECK
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 2. GET FIELD VALUES
$field_1 = get_field( 'field_1' );
$field_2 = get_field( 'field_2' ) ?: 'default_value';

// 3. CREATE BLOCK ATTRIBUTES
$block_id = 'block-name-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
    $block_id = $block['anchor'];
}

$class_name = 'section {block-name}';
if ( ! empty( $block['className'] ) ) {
    $class_name .= ' ' . $block['className'];
}

// 4. PREVIEW MODE HANDLING
if ( $is_preview && empty( $required_field ) ) {
    // Show empty state placeholder
    return;
}

// 5. SKIP RENDERING
if ( empty( $required_field ) ) {
    return;
}

// 6. RENDER OUTPUT
?>
<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>">
    <!-- Block content -->
</section>
```

### Field Retrieval Best Practices

```php
// ✅ Good: Default values for optional fields
$alignment = get_field( 'alignment' ) ?: 'center';
$style     = get_field( 'style' ) ?: 'default';

// ✅ Good: Check existence before use
$image = get_field( 'image' );
if ( $image ) {
    $image_url = wp_get_attachment_image_url( $image, 'full' );
}

// ✅ Good: Fallback chain
$alt_text = get_field( 'custom_alt' ) 
    ?: get_post_meta( $image_id, '_wp_attachment_image_alt', true )
    ?: get_the_title( $image_id );

// ❌ Bad: No defaults
$alignment = get_field( 'alignment' );

// ❌ Bad: No existence check
$image_url = wp_get_attachment_image_url( get_field( 'image' ), 'full' );
```

### Output Escaping

**ALWAYS escape output**:

```php
// Plain Text (no HTML allowed)
<?php echo esc_html( $text ); ?>

// HTML Content (WYSIWYG fields, rich text)
<?php echo wp_kses_post( $wysiwyg_content ); ?>

// Pre-constructed HTML (already escaped in PHP logic)
<?php echo $final_title; // Already escaped, don't escape again ?>

// HTML attributes
<div class="<?php echo esc_attr( $class ); ?>">

// URLs
<a href="<?php echo esc_url( $url ); ?>">

// Textareas (preserve line breaks)
<?php echo wp_kses_post( wpautop( $textarea ) ); ?>
```

**Rules**: 
- Use `esc_html()` for plain text fields only
- Use `wp_kses_post()` for WYSIWYG fields with HTML content
- For titles with styled spans, construct HTML in PHP with proper escaping, then output directly (see "Title with Styled Spans Pattern")

### Conditional Rendering

```php
// ✅ Good: Check before rendering
<?php if ( $cta && ! empty( $cta['url'] ) ) : ?>
    <a href="<?php echo esc_url( $cta['url'] ); ?>">
        <?php echo esc_html( $cta['title'] ); ?>
    </a>
<?php endif; ?>

// ✅ Good: Multiple conditions
<?php if ( 'image' === $background_type && $background_image ) : ?>
    <!-- Image markup -->
<?php elseif ( 'video' === $background_type && $background_video ) : ?>
    <!-- Video markup -->
<?php endif; ?>
```

---

## Preview Mode

### Enhanced Preview Mode Pattern (Recommended)

Use the enhanced preview mode with block preview images:

```php
// Enhanced preview mode - show image preview if no content.
if ( $is_preview && empty( $required_field ) ) {
    jlbpartners_show_block_preview( '{block-name}', __( '{Block Title} Block', 'jlbpartners' ) );
    return;
}
```

**Requirements:**
- Block preview image must exist at `/assets/images/block-previews/{block-name}-preview.png`
- Image name must match block folder name exactly
- The `jlbpartners_show_block_preview()` function automatically displays the image with helpful text

### Legacy Empty State Pattern (Fallback)

For blocks without preview images, use the manual placeholder:

```php
if ( $is_preview && empty( $required_field ) ) {
    ?>
    <div class="jlbpartners-block--preview" style="min-height: 400px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%); border: 2px dashed #00a400; border-radius: 8px; padding: 40px; text-align: center;">
        <div class="jlbpartners-block__preview-placeholder" style="max-width: 500px;">
            <div style="font-size: 48px; margin-bottom: 16px;">🎨</div>
            <h3 style="margin: 0 0 12px 0; color: #1d2327; font-size: 20px; font-weight: 600;">Add Your Content</h3>
            <p style="margin: 0 0 8px 0; color: #646970; font-size: 14px; line-height: 1.6;">
                <?php esc_html_e( 'Add content to see your block preview.', 'jlbpartners' ); ?>
            </p>
            <div style="margin-top: 20px; padding: 12px; background: rgba(255, 255, 255, 0.7); border-radius: 4px; font-size: 12px; color: #646970;">
                <strong>✨ Quick Tips:</strong><br>
                • Use the fields panel on the right<br>
                • Preview updates in real-time<br>
                • All settings are optional
            </div>
        </div>
    </div>
    <?php
    return;
}
```

### Preview Considerations

1. **Always show something** - Never render blank in preview
2. **Match frontend exactly** - Same HTML structure
3. **Handle all states** - Empty, partial, complete
4. **Performance** - Keep preview rendering fast
5. **Helpful feedback** - Guide users with messages

---

## Styling Guidelines

### CSS Organization

```css
/* Block: Block Name */
.ss.block-name {
    /* Layout */
    position: relative;
    display: flex;
    
    /* Spacing */
    padding: clamp(2rem, 4vw, 4rem) 0;
    margin: 0;
    
    /* Visual */
    background-color: var(--color-bg);
}

/* Block Elements */
.ss.block-name .block-name__element {
    /* Element styles */
}

/* Modifiers */
.ss.block-name.block-name--variant {
    /* Variant styles */
}

/* Responsive */
@media screen and (min-width: 768px) {
    .ss.block-name {
        /* Tablet styles */
    }
}

@media screen and (min-width: 1199px) {
    .ss.block-name {
        /* Desktop styles */
    }
}
```

### Naming Conventions

**BEM-ish approach**:
```css
.section.block-name               /* Block */
.section.block-name__element      /* Element */
.section.block-name--modifier     /* Modifier */
```

### Responsive Design

Use clamp() for fluid typography:
```css
font-size: clamp(1rem, 2vw + 0.5rem, 2rem);
```

Use mobile-first breakpoints:
```css
/* Mobile: default styles */

@media screen and (min-width: 768px) {
    /* Tablet */
}

@media screen and (min-width: 991px) {
    /* Small desktop */
}

@media screen and (min-width: 1199px) {
    /* Desktop */
}

@media screen and (min-width: 1320px) {
    /* Large desktop */
}
```

---

## Code Standards

### PHP Standards

**Follow WordPress VIP Coding Standards**:

```bash
# Check code
phpcs --standard=WordPressVIPMinimum blocks/block-name/

# Auto-fix where possible
phpcbf --standard=WordPressVIPMinimum blocks/block-name/
```

### Required Standards

1. **All inputs sanitized**
   ```php
   $text = sanitize_text_field( $_POST['text'] );
   $html = wp_kses_post( $_POST['html'] );
   ```

2. **All outputs escaped**
   ```php
   echo esc_html( $text );
   echo esc_attr( $class );
   echo esc_url( $url );
   ```

3. **No hardcoded URLs**
   ```php
   // ✅ Good
   home_url( '/' )
   get_permalink()
   admin_url( 'admin.php' )
   
   // ❌ Bad
   'https://example.com'
   '/wp-admin/'
   ```

4. **Proper text domains**
   ```php
   __( 'Text', 'jlbpartners' )
   esc_html__( 'Text', 'jlbpartners' )
   esc_attr__( 'Text', 'jlbpartners' )
   ```

5. **PHPDoc blocks**
   ```php
   /**
    * Function description
    *
    * @param string $param Description.
    * @return bool Description.
    */
   ```

### File Organization

```php
<?php
/**
 * File description
 *
 * @package JLBPartners
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Code here
```

---

## Registration

### Register Block

In `/inc/blocks/block-registration.php`:

```php
// Block Name Block.
acf_register_block_type(
    array(
        'name'            => 'block-name',
        'title'           => __( 'Block Name', 'jlbpartners' ),
        'description'     => __( 'Brief description of block functionality', 'jlbpartners' ),
        'render_template' => get_template_directory() . '/blocks/block-name/template.php',
        'category'        => 'jlbpartners-blocks',
        'icon'            => array(
            'src'        => 'layout', // Dashicon name
            'foreground' => '#00a400',
        ),
        'keywords'        => array( 'keyword1', 'keyword2', 'keyword3' ),
        'mode'            => 'preview',
        'supports'        => array(
            'align'  => false,  // Alignment toolbar is disabled for all components
            'mode'   => true,
            'jsx'    => true,
            'anchor' => true,
        ),
        'example'         => array(
            'attributes' => array(
                'mode' => 'preview',
                'data' => array(
                    // Example data for block inserter preview
                ),
            ),
        ),
        'enqueue_assets'  => function() {
            // Only if block needs specific CSS
            wp_enqueue_style(
                'block-name-block',
                get_template_directory_uri() . '/blocks/block-name/style.css',
                array(),
                JLBPARTNERS_VERSION
            );
        },
    )
);
```

### Load Block Fields

In `/functions.php`:

```php
/**
 * Load individual block field configurations.
 */
require_once JLBPARTNERS_THEME_DIR . '/blocks/block-name/fields.php';
```

### Add to Smart Asset Loading

In `/inc/smart-asset-loading.php`:

```php
$block_css_map = array(
    'acf/hero-section' => '/blocks/hero-section/style.css',
    'acf/block-name'   => '/blocks/block-name/style.css', // Add here
);
```

### ⚠️ CRITICAL: Add to Allowed Block Types

**This is required or your block won't appear in the editor!**

In `/inc/editor-customization.php`, add your block to the `jlbpartners_allowed_block_types()` function:
```php
/**
 * Filter which blocks can be used in the editor
 */
function your_theme_allowed_block_types( $allowed_blocks, $editor_context ) {
    // Allow only your custom ACF blocks.
    return array(
        'acf/hero-section',
        'acf/about',
        'acf/title-block',
        // ... add your blocks here
    );
}
add_filter( 'allowed_block_types_all', 'your_theme_allowed_block_types', 10, 2 );
```

**Purpose:**

This theme intentionally filters out all default WordPress blocks (core blocks, third-party blocks) to provide a clean, component-based editing experience. This filter controls which blocks are visible in the block inserter.

**Common Issue:**  
If you register a block but forget to add it to this filter, the block will be registered in ACF and WordPress, but **will not appear in the block inserter UI**. Always add new blocks to this list!

---

## Complete Example: Creating a "Feature Card" Block

This example shows how to create a complete block from start to finish, following all best practices.

### 1. Create Fields File (`blocks/feature-card/fields.php`)

```php
<?php
/**
 * Feature Card Block - ACF Fields
 *
 * @package JLBPartners
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register ACF fields for Feature Card Block
 */
function jlbpartners_feature_card_fields() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    acf_add_local_field_group(
        array(
            'key'    => 'group_feature_card',
            'title'  => 'Feature Card',
            'fields' => array(
                // Content Tab
                array(
                    'key'   => 'field_feature_card_content_tab',
                    'label' => 'Content',
                    'type'  => 'tab',
                ),
                array(
                    'key'      => 'field_feature_card_title',
                    'label'    => 'Title',
                    'name'     => 'title',
                    'type'     => 'text',
                    'required' => 1,
                ),
                array(
                    'key'  => 'field_feature_card_description',
                    'label' => 'Description',
                    'name'  => 'description',
                    'type'  => 'textarea',
                    'rows'  => 3,
                ),
                array(
                    'key'           => 'field_feature_card_cta',
                    'label'         => 'Call to Action',
                    'name'          => 'cta',
                    'type'          => 'link',
                    'return_format' => 'array',
                ),

                // Media Tab
                array(
                    'key'   => 'field_feature_card_media_tab',
                    'label' => 'Media',
                    'type'  => 'tab',
                ),
                array(
                    'key'           => 'field_feature_card_image',
                    'label'         => 'Image',
                    'name'          => 'image',
                    'type'          => 'image',
                    'required'      => 1,
                    'return_format' => 'id',
                    'preview_size'  => 'medium',
                ),

                // Accessibility accordion (copy from hero-section)
                array(
                    'key'          => 'field_feature_card_accessibility_accordion',
                    'label'        => 'Accessibility Settings',
                    'type'         => 'accordion',
                    'open'         => 0,
                    'multi_expand' => 1,
                    'endpoint'     => 0,
                ),
                array(
                    'key'           => 'field_feature_card_image_role',
                    'label'         => 'Image Role',
                    'name'          => 'image_role',
                    'type'          => 'radio',
                    'instructions'  => 'Is this image decorative or informational?',
                    'choices'       => array(
                        'decorative'    => 'Decorative (hidden from screen readers)',
                        'informational' => 'Informational (needs description)',
                    ),
                    'default_value' => 'informational',
                ),
                array(
                    'key'               => 'field_feature_card_image_alt',
                    'label'             => 'ALT Text',
                    'name'              => 'image_alt',
                    'type'              => 'text',
                    'instructions'      => 'Describe what the image shows',
                    'conditional_logic' => array(
                        array(
                            array(
                                'field'    => 'field_feature_card_image_role',
                                'operator' => '==',
                                'value'    => 'informational',
                            ),
                        ),
                    ),
                    'maxlength' => 200,
                ),
                array(
                    'key'          => 'field_feature_card_image_caption',
                    'label'        => 'Caption',
                    'name'         => 'image_caption',
                    'type'         => 'text',
                    'instructions' => 'Optional visible caption',
                    'maxlength'    => 150,
                ),
                array(
                    'key'      => 'field_feature_card_accessibility_accordion_end',
                    'label'    => 'Accessibility End',
                    'type'     => 'accordion',
                    'endpoint' => 1,
                ),

                // Settings Tab
                array(
                    'key'   => 'field_feature_card_settings_tab',
                    'label' => 'Settings',
                    'type'  => 'tab',
                ),
                array(
                    'key'           => 'field_feature_card_layout',
                    'label'         => 'Layout',
                    'name'          => 'layout',
                    'type'          => 'radio',
                    'choices'       => array(
                        'image-left'  => 'Image Left',
                        'image-right' => 'Image Right',
                        'image-top'   => 'Image Top',
                    ),
                    'default_value' => 'image-left',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param'    => 'block',
                        'operator' => '==',
                        'value'    => 'acf/feature-card',
                    ),
                ),
            ),
        )
    );
}
add_action( 'acf/init', 'jlbpartners_feature_card_fields' );
```

### 2. Create Template File (`blocks/feature-card/template.php`)

```php
<?php
/**
 * Feature Card Block Template
 *
 * @package JLBPartners
 *
 * @param array  $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool   $is_preview True during AJAX preview.
 * @param int    $post_id The post ID this block is saved to.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get field values
$title       = get_field( 'title' );
$description = get_field( 'description' );
$cta         = get_field( 'cta' );
$image       = get_field( 'image' );
$layout      = get_field( 'layout' ) ?: 'image-left';

// Accessibility fields
$image_role    = get_field( 'image_role' ) ?: 'informational';
$image_alt     = get_field( 'image_alt' );
$image_caption = get_field( 'image_caption' );

// Create block attributes
$block_id = 'feature-card-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
    $block_id = $block['anchor'];
}

$class_name = 'section feature-card feature-card--' . $layout;
if ( ! empty( $block['className'] ) ) {
    $class_name .= ' ' . $block['className'];
}

// Preview mode handling
if ( $is_preview && ( empty( $title ) || empty( $image ) ) ) {
    ?>
    <div class="jlbpartners-block--preview" style="min-height: 300px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%); border: 2px dashed #00a400; border-radius: 8px; padding: 40px; text-align: center;">
        <div>
            <div style="font-size: 48px; margin-bottom: 16px;">🎯</div>
            <h3 style="margin: 0 0 12px 0; color: #1d2327;">Feature Card</h3>
            <p style="margin: 0; color: #646970;">Add a title and image to see your feature card.</p>
        </div>
    </div>
    <?php
    return;
}

// Skip rendering if required fields are empty
if ( empty( $title ) || empty( $image ) ) {
    return;
}

// Get image data
$image_url = wp_get_attachment_image_url( $image, 'large' );
$image_meta = wp_get_attachment_metadata( $image );
$image_width = isset( $image_meta['width'] ) ? $image_meta['width'] : 800;
$image_height = isset( $image_meta['height'] ) ? $image_meta['height'] : 600;

// Auto-populate alt text if not provided
$alt_text = $image_alt ? $image_alt : get_post_meta( $image, '_wp_attachment_image_alt', true );
if ( ! $alt_text ) {
    $alt_text = get_the_title( $image );
}
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>">
    <div class="container">
        <div class="feature-card__inner">
            
            <!-- Image -->
            <div class="feature-card__image">
                <figure <?php echo 'decorative' === $image_role ? 'aria-hidden="true"' : ''; ?>>
                    <img 
                        src="<?php echo esc_url( $image_url ); ?>" 
                        alt="<?php echo 'decorative' === $image_role ? '' : esc_attr( $alt_text ); ?>" 
                        width="<?php echo esc_attr( $image_width ); ?>" 
                        height="<?php echo esc_attr( $image_height ); ?>"
                        loading="lazy"
                        <?php if ( 'decorative' === $image_role ) : ?>
                            role="presentation"
                        <?php endif; ?>
                    >
                    <?php if ( $image_caption ) : ?>
                        <figcaption><?php echo esc_html( $image_caption ); ?></figcaption>
                    <?php endif; ?>
                </figure>
            </div>

            <!-- Content -->
            <div class="feature-card__content">
                <h2 class="feature-card__title"><?php echo esc_html( $title ); ?></h2>
                
                <?php if ( $description ) : ?>
                    <div class="feature-card__description">
                        <?php echo wp_kses_post( wpautop( $description ) ); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ( $cta && ! empty( $cta['url'] ) ) : ?>
                    <div class="feature-card__cta">
                        <a 
                            href="<?php echo esc_url( $cta['url'] ); ?>" 
                            class="l-button"
                            <?php if ( $cta['target'] ) : ?>
                                target="<?php echo esc_attr( $cta['target'] ); ?>"
                            <?php endif; ?>
                        >
                            <?php echo esc_html( $cta['title'] ); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</section>
```

### 3. Create Styles (Optional - `blocks/feature-card/style.css`)

```css
/* Feature Card Block */
.section.feature-card {
    padding: clamp(2rem, 4vw, 4rem) 0;
}

.feature-card__inner {
    display: flex;
    align-items: center;
    gap: clamp(1.5rem, 4vw, 3rem);
}

.feature-card__image {
    flex: 0 0 50%;
}

.feature-card__image img {
    width: 100%;
    height: auto;
    border-radius: 8px;
}

.feature-card__content {
    flex: 1;
}

.feature-card__title {
    font-size: clamp(1.5rem, 3vw, 2.5rem);
    margin-bottom: 1rem;
}

.feature-card__description {
    margin-bottom: 1.5rem;
    color: #666;
}

/* Layout Variants */
.feature-card--image-right .feature-card__inner {
    flex-direction: row-reverse;
}

.feature-card--image-top .feature-card__inner {
    flex-direction: column;
    text-align: center;
}

/* Responsive */
@media screen and (max-width: 767px) {
    .feature-card__inner {
        flex-direction: column;
    }
    
    .feature-card__image {
        flex: none;
    }
}
```

### 4. Register the Block (`inc/blocks/block-registration.php`)

```php
// Feature Card Block.
acf_register_block_type(
    array(
        'name'            => 'feature-card',
        'title'           => __( 'Feature Card', 'jlbpartners' ),
        'description'     => __( 'Highlight a feature with image, title, description and CTA', 'jlbpartners' ),
        'render_template' => get_template_directory() . '/blocks/feature-card/template.php',
        'category'        => 'jlbpartners-blocks',
        'icon'            => array(
            'src'        => 'id-alt',
            'foreground' => '#00a400',
        ),
        'keywords'        => array( 'feature', 'card', 'highlight', 'cta' ),
        'mode'            => 'preview',
        'supports'        => array(
            'align'  => false,
            'mode'   => true,
            'jsx'    => true,
            'anchor' => true,
        ),
        'enqueue_assets'  => function() {
            wp_enqueue_style(
                'feature-card-block',
                get_template_directory_uri() . '/blocks/feature-card/style.css',
                array(),
                JLBPARTNERS_VERSION
            );
        },
    )
);
```

### 5. Update Required Files

**functions.php:**
```php
require_once JLBPARTNERS_THEME_DIR . '/blocks/feature-card/fields.php';
```

**inc/smart-asset-loading.php:**
```php
$block_css_map = array(
    // ... existing blocks
    'acf/feature-card' => '/blocks/feature-card/style.css',
);
```

**inc/editor-customization.php:**
```php
function jlbpartners_allowed_block_types( $allowed_blocks, $editor_context ) {
    return array(
        // ... existing blocks
        'acf/feature-card',
    );
}
```

### 6. Test Your Block

1. **User Steps:**

1. **Create page** - Pages → Add New
2. **Add block** - Look for "Feature Card" in your theme's block category
3. **Fill fields** - Add title, description, icon
4. **Publish** - Click Publish buttons for alt text
4. **Test preview** - Should match frontend exactly
5. **Test accessibility** - Check with screen reader
6. **Test responsive** - Check mobile/tablet layouts

This complete example shows all the patterns and best practices in action!

---

## Testing Checklist

### Functionality Tests

- [ ] Block appears in inserter
- [ ] All fields save correctly
- [ ] Preview matches frontend exactly
- [ ] Required fields work properly
- [ ] Optional fields have defaults
- [ ] Conditional logic works
- [ ] Empty states display properly

### Accessibility Tests

- [ ] All images have proper alt text
- [ ] Interactive elements have ARIA labels
- [ ] Proper heading hierarchy
- [ ] Keyboard navigation works
- [ ] Screen reader compatible
- [ ] Color contrast meets WCAG AA
- [ ] Focus states visible

### Browser/Device Tests

- [ ] Chrome (desktop)
- [ ] Firefox (desktop)
- [ ] Safari (desktop)
- [ ] Edge (desktop)
- [ ] Mobile Safari (iOS)
- [ ] Chrome Mobile (Android)

### Performance Tests

- [ ] Images properly sized
- [ ] Lazy loading implemented (if needed)
- [ ] No console errors
- [ ] Fast rendering in editor
- [ ] CSS only loads when needed

### Code Quality Tests

```bash
# Run PHPCS
phpcs --standard=WordPressVIPMinimum blocks/block-name/

# Check for common issues
- All inputs sanitized
- All outputs escaped
- No hardcoded URLs
- Proper text domains
- PHPDoc blocks present
```

---

## Quick Reference

### Field Types Reference

```php
// Text
'type' => 'text'

// Textarea
'type' => 'textarea'

// WYSIWYG Editor
'type' => 'wysiwyg'
'tabs' => 'visual'          // Options: 'all', 'visual', 'text'
'toolbar' => 'basic'        // Options: 'full', 'basic', 'minimal'
'media_upload' => 0         // Disable media upload if not needed

// Image
'type' => 'image'
'return_format' => 'id' // or 'url' or 'array'

// File
'type' => 'file'
'return_format' => 'array'

// Link
'type' => 'link'
'return_format' => 'array'

// Select
'type' => 'select'
'choices' => array( 'value' => 'Label' )

// Radio
'type' => 'radio'
'choices' => array( 'value' => 'Label' )

// Button Group
'type' => 'button_group'
'choices' => array( 'value' => 'Label' )

// Range
'type' => 'range'
'min' => 0
'max' => 100
'step' => 1

// Tab
'type' => 'tab'
'placement' => 'top'

// Accordion
'type' => 'accordion'
'open' => 0
'multi_expand' => 1
'endpoint' => 0 // or 1 to close

// Message (info)
'type' => 'message'
'message' => 'Your message here'
'esc_html' => 0
```

### Title with Styled Spans Pattern

For titles that need part of the text styled differently (e.g., "Wildlife visit popular <span>water hole</span>"), use a **single-row Group field** with just two text inputs:

**Fields Setup** (Simplest Single Row Layout):
```php
// Title Group - Two fields in one row (60% + 40%)
array(
    'key'          => 'field_title_group',
    'label'        => 'Title',
    'name'         => 'title_group',
    'type'         => 'group',
    'instructions' => 'Main title text followed by optional highlighted text',
    'layout'       => 'row',
    'sub_fields'   => array(
        // Main title (60% width, required)
        array(
            'key'         => 'field_title',
            'label'       => 'Main Title',
            'name'        => 'title',
            'type'        => 'text',
            'required'    => 1,
            'wrapper'     => array( 'width' => '60' ),
            'placeholder' => 'e.g., Wildlife visit popular',
            'maxlength'   => 100,
        ),
        // Highlighted text (40% width, optional)
        array(
            'key'         => 'field_span',
            'label'       => 'Highlighted Text',
            'name'        => 'span',
            'type'        => 'text',
            'required'    => 0,
            'wrapper'     => array( 'width' => '40' ),
            'placeholder' => 'e.g., water hole',
            'maxlength'   => 50,
        ),
    ),
),
```

**Template Logic**:
```php
// Get title group fields
$title_group = get_field( 'title_group' );
$title       = isset( $title_group['title'] ) ? $title_group['title'] : '';
$title_span  = isset( $title_group['span'] ) ? $title_group['span'] : '';

// Construct title with optional span at the end
$final_title = '';
if ( ! empty( $title ) ) {
    $final_title = esc_html( $title );
    
    // Append highlighted text if provided
    if ( ! empty( $title_span ) ) {
        $final_title .= ' <span>' . esc_html( $title_span ) . '</span>';
    }
}

// Output (already escaped, don't escape again)
echo $final_title;
```

**Output Example**:
```html
<!-- Input: Main Title = "Wildlife visit popular", Highlighted Text = "water hole" -->
<!-- Output: -->
<h2>Wildlife visit popular <span>water hole</span></h2>
```

**Benefits**:
- ✅ **Ultra-simple UX** - Just two text fields side by side
- ✅ **Single row layout** - 60% main title + 40% highlighted text
- ✅ **No conditional logic** - Both fields always visible
- ✅ **Clear purpose** - Highlighted text always appends at the end
- ✅ **Secure** - Properly escaped (no XSS vulnerabilities)
- ✅ **Fast** - No complex position logic or calculations

---

### Common Patterns

**Conditional Logic**:
```php
'conditional_logic' => array(
    array(
        array(
            'field'    => 'field_key',
            'operator' => '==', // ==, !=, >, <, ==empty, !=empty
            'value'    => 'value',
        ),
    ),
),
```

**Multiple Conditions (AND)**:
```php
'conditional_logic' => array(
    array(
        array( /* condition 1 */ ),
        array( /* condition 2 */ ), // Both must be true
    ),
),
```

**Multiple Conditions (OR)**:
```php
'conditional_logic' => array(
    array(
        array( /* condition 1 */ ),
    ),
    array(
        array( /* condition 2 */ ), // Either can be true
    ),
),
```

---

## Version History

- **v1.0.0** - October 26, 2025 - Initial guidelines based on Hero Section component

---

## Additional Resources

- [WordPress Block Editor Handbook](https://developer.wordpress.org/block-editor/)
- [ACF Block Documentation](https://www.advancedcustomfields.com/resources/)
- [WordPress VIP Coding Standards](https://docs.wpvip.com/technical-references/vip-codebase/)
- [Setup Guide](SETUP.md)
- [Contributing Guidelines](CONTRIBUTING.md)

---

**Golden Template Documentation**  
*Part of the WordPress Golden Template Repository*.
