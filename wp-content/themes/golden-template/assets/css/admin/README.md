# Admin CSS Files

This folder contains CSS files used in the WordPress Admin area (backend).

## Files:

- **blocks-editor.css** - Styles for ACF blocks in the Gutenberg block editor
- **editor-customization.css** - Enhanced UI/UX for the WordPress block editor (inserter, toolbar, sidebar)

## Usage:

These files are loaded ONLY when editing pages/posts in the WordPress block editor. They do NOT affect the frontend website or the JLB Partners Settings page.

## Loaded by:

- `inc/blocks/block-registration.php` - Loads `blocks-editor.css`
- `inc/editor-customization.php` - Loads `editor-customization.css`

