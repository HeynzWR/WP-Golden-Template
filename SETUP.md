# WordPress Golden Template - Setup Guide

Complete guide for setting up a new WordPress project using this golden template.

## Prerequisites

Before you begin, ensure you have:

- ✅ **PHP 8.1+** installed
- ✅ **Composer** installed ([getcomposer.org](https://getcomposer.org/))
- ✅ **Node.js & npm** (if using build tools)
- ✅ **Local development environment** (DDEV, Local by Flywheel, XAMPP, etc.)
- ✅ **Git** for version control
- ✅ **ACF Pro license** (required for blocks to work)

## Step-by-Step Setup

### 1. Clone the Template

```bash
# Clone the repository
git clone <repository-url> my-new-project
cd my-new-project

# Remove the existing git history (optional)
rm -rf .git
git init
```

### 2. Run the Rename Script

The rename script will update all references from `golden-template` to your project name.

```bash
# Make script executable (if not already)
chmod +x rename-project.sh

# Test with dry-run first (recommended)
./rename-project.sh my-project-name --dry-run

# Run the actual rename
./rename-project.sh my-project-name
```

**Naming requirements:**
- Use lowercase letters, numbers, hyphens, or underscores only
- Must start with a letter
- Examples: `my-site`, `acme-corp`, `awesome_project`

The script will:
- ✅ Create a backup (in `.backup_YYYYMMDD_HHMMSS/`)
- ✅ Rename theme directory
- ✅ Rename MU plugin directory
- ✅ Update all PHP files (classes, functions, constants)
- ✅ Update all CSS files (class names)
- ✅ Update all JavaScript files
- ✅ Update theme header in style.css

**If something goes wrong:**
```bash
./rollback-rename.sh
```

### 3. Install Dependencies

```bash
# Install PHP dependencies (includes PHPCS)
composer install
```

### 4. Set Up Local Environment

Choose your preferred local development environment:

#### Option A: DDEV (Recommended)

```bash
# Initialize DDEV (if not already configured)
ddev config --project-type=wordpress --php-version=8.1

# Start the environment
ddev start

# Import database (if you have one)
# Option 1: Use the import script (recommended - handles renaming automatically)
./import-database.sh [your-project-name] [database-file.sql.gz]

# Option 2: Direct DDEV import (if you haven't renamed the project yet)
ddev import-db --file=your-database.sql.gz
```

#### Option B: Local by Flywheel

1. Create a new site in Local
2. Point to this directory as the app folder
3. Configure PHP 8.1+
4. Start the site

#### Option C: Manual Setup

1. Set up virtual host pointing to this directory
2. Create database
3. Configure wp-config.php

### 5. Configure WordPress

#### First Time Setup

If starting fresh:

1. Navigate to your local site URL
2. Complete WordPress installation
3. Install **ACF Pro** plugin (required)
4. Activate your renamed theme

#### Existing Database

If you have an existing database:

1. Import your database
2. Update wp-config.php with database credentials
3. Update site URL if needed:
   ```bash
   wp search-replace 'oldsite.com' 'newsite.local' --all-tables
   ```

### 6. Install Required Plugins

**Must-have plugins:**
- ✅ **Advanced Custom Fields (ACF) Pro** - Required for all blocks
  
**Recommended plugins:**
- Classic Editor (if you prefer classic editor)
- Yoast SEO or Rank Math
- Wordfence Security
- WP Rocket (caching)

### 7. Configure Theme Settings

After activation, configure:

1. **Permalinks**: Settings → Permalinks → Post name
2. **Menus**: Appearance → Menus → Create Primary and Footer menus
3. **Homepage**: Settings → Reading → Set static homepage (if needed)
4. **ACF Options**: Configure any ACF option pages

### 8. Verify Installation

Run these checks to ensure everything is working:

#### Check Code Standards

```bash
./vendor/bin/phpcs --standard=WordPress-VIP-Go wp-content/themes/your-project-name/
```

Should show 0 ERRORS (warnings are acceptable).

#### Enable Template Debugging

In `wp-content/themes/your-project-name/functions.php`, temporarily set:

```php
define( 'GOLDEN_TEMPLATE_DEBUG_TEMPLATES', true );  // Update this constant name
```

Visit your site - you should see a debug panel in the bottom-left showing which template is being used.

#### Test ACF Blocks

1. Create a new page
2. Click "+" to add block
3. Look for "Your Project Components" category
4. Add a block and configure it
5. Preview and publish

### 9. Import Database (if you have one)

If you have an existing database dump to import:

```bash
# Make script executable
chmod +x import-database.sh

# Import with default "golden-template" naming
./import-database.sh

# OR import and rename to your project name (run AFTER rename-project.sh)
./import-database.sh your-project-name your-database.sql.gz
```

**The import script automatically:**
- ✅ Renames table names (e.g., `wp_jlbpartners_logs` → `wp_your_project_logs`)
- ✅ Updates option names and settings
- ✅ Updates theme references
- ✅ Handles constants and function prefixes

**Note:** If you've already run `rename-project.sh`, the import script will automatically rename database references to match your new project name.

### 10. Optional: Manual Database Search & Replace

If you imported a database before running the rename script, you may need to manually update references:

```bash
# Using WP-CLI
wp search-replace 'golden-template' 'your-project-name' --all-tables

# Or use Better Search Replace plugin
# Install it, go to Tools → Better Search Replace
# Search for: golden-template
# Replace with: your-project-name
# Select all tables
# Run dry-run first!
```

**Common database locations to update:**
- Option names: `golden_template_logo`, `golden_template_*`
- Post meta keys
- Widget settings
- Theme mods

### 11. Clean Up

```bash
# Remove backup if rename was successful
rm -rf .backup_*

# Remove this setup guide if desired (keep in git history)
# git rm SETUP.md

# Commit your changes
git add .
git commit -m "Initial project setup with renamed theme"
```

## Post-Setup Checklist

- [ ] Rename script completed successfully
- [ ] Dependencies installed (Composer)
- [ ] Local environment running
- [ ] ACF Pro installed and activated
- [ ] Theme activated
- [ ] Menus created and assigned
- [ ] Homepage set (if applicable)
- [ ] Code standards check passing
- [ ] Test blocks working
- [ ] Git repository initialized
- [ ] Initial commit made

## Troubleshooting

### "Class not found" errors

**Cause**: Old class names still in database or cache

**Solution**:
1. Clear WordPress object cache
2. Clear opcache: `ddev exec "sudo service php*-fpm reload"` (DDEV)
3. Run database search/replace

### Blocks not appearing

**Cause**: ACF Pro not installed or old block registrations

**Solution**:
1. Verify ACF Pro is installed and activated
2. Check `inc/blocks/block-registration.php` - ensure block names match renamed project
3. Clear browser cache and hard refresh (Cmd+Shift+R)

### PHPCS errors after rename

**Cause**: Incorrect replacements in code

**Solution**:
1. Check the specific files mentioned
2. Verify text domain matches everywhere
3. If needed, rollback and re-run rename script

### Styles not loading

**Cause**: Old enqueued style handles

**Solution**:
1. Check `functions.php` - ensure all `wp_enqueue_style` handles are updated
2. Clear browser cache
3. Check browser console for 404 errors

### White screen / Fatal error

**Cause**: PHP syntax error from find/replace

**Solution**:
1. Enable WordPress debug mode in wp-config.php:
   ```php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   ```
2. Check `wp-content/debug.log` for errors
3. Rollback using `./rollback-rename.sh`

## Next Steps

- Read [BLOCK_DEVELOPMENT_GUIDELINES.md](BLOCK_DEVELOPMENT_GUIDELINES.md) to create custom blocks
- Review [CONTRIBUTING.md](CONTRIBUTING.md) for development standards
- Check [STRUCTURE.md](STRUCTURE.md) to understand the codebase organization

## Getting Help

If you encounter issues:
1. Check this troubleshooting section
2. Review the [RENAME-GUIDE.md](RENAME-GUIDE.md) for script-specific issues
3. Check the backup location (printed after running rename script)
4. Contact your development team lead

---

**Setup complete?** Start building! Check the [Block Development Guidelines](BLOCK_DEVELOPMENT_GUIDELINES.md) to create your first custom block.
