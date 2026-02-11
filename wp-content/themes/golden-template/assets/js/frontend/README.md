# Frontend JavaScript Files

This folder contains JavaScript files used on the public-facing website (frontend).

## Files:

- **header.js** - Header navigation functionality (mobile menu, dropdowns, search modal, keyboard navigation)
- **main.js** - General theme scripts (smooth scroll, lazy loading, etc.)

## Usage:

These files are loaded on the public website that visitors see. They do NOT affect the WordPress admin area.

## Features:

### header.js:
- Mobile menu toggle with proper ARIA attributes
- Dropdown menu functionality
- Search modal with focus management
- Keyboard navigation support (Arrow keys, Escape, Tab)
- Focus trap for accessibility
- Body scroll prevention when modals are open

### main.js:
- Smooth scrolling for anchor links
- Image lazy loading support
- Mobile menu toggle (jQuery-based)

## Loaded by:

- `functions.php` - Loads both scripts via `jlbpartners_scripts()`
  - `header.js` - Loaded in footer, no dependencies
  - `main.js` - Loaded in footer, depends on jQuery

## Dependencies:

- **header.js**: Pure vanilla JavaScript (no dependencies)
- **main.js**: Requires jQuery (included with WordPress)

