# Frontend CSS Files

This folder contains CSS files used on the public-facing website (frontend).

## Files:

- **main.css** - Main theme styles with CSS variables and base styles

## Usage:

These files are loaded on the public website that visitors see. They do NOT affect the WordPress admin area.

## Loaded by:

- `functions.php` - Loads `main.css` via `golden_template_scripts()`

## Additional Frontend Styles:

- Individual block styles are located in their respective block folders (e.g., `/blocks/hero-section/style.css`)
- Smart asset loading ensures block styles only load when blocks are used on a page

