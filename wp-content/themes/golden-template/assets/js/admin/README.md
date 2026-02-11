# Admin JavaScript Files

This folder contains JavaScript files used in the WordPress Admin area (backend).

## Files:

- **editor-customization.js** - Enhances the Gutenberg block editor UI/UX for JLB Partners components

## Usage:

These files are loaded ONLY when editing pages/posts in the WordPress block editor. They do NOT affect the frontend website or the JLB Partners Settings page.

## Features:

- Customizes block inserter to highlight JLB Partners components
- Adds component badges and preview indicators
- Adds branding indicator in editor sidebar
- Implements keyboard shortcuts for better workflow
- Auto-switches ACF blocks to preview mode
- Adds helper text and instructions

## Loaded by:

- `inc/editor-customization.php` - Loads `editor-customization.js` via `enqueue_block_editor_assets`

