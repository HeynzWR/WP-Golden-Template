# Project Rename Guide

Detailed documentation for the `rename-project.sh` script included in this WordPress Golden Template.

## Overview

The rename script automates the process of customizing this template for your specific project. It renames all instances of `Golden Template` and its associated slugs/prefixes throughout the codebase to your project name.

## Quick Usage

```bash
# Make script executable
chmod +x rename-project.sh

# Dry run (recommended first)
./rename-project.sh my-new-project --dry-run

# Actual rename
./rename-project.sh my-new-project
```

## What Gets Renamed

### Directory Names

| Before | After (example: "acme-site") |
|--------|------------------------------|
| `wp-content/themes/golden-template/` | `wp-content/themes/acme-site/` |
| `wp-content/mu-plugins/golden-template-core/` | `wp-content/mu-plugins/acme-site-core/` |
| `wp-content/mu-plugins/golden-template-core-loader.php` | `wp-content/mu-plugins/acme-site-core-loader.php` |

### File Contents

#### PHP Files

| Type | Before | After (example: "acme_site") |
|------|--------|------------------------------|
| Constants | `GOLDEN_TEMPLATE_VERSION` | `ACME_SITE_VERSION` |
| Functions | `golden_template_setup()` | `acme_site_setup()` |
| Classes | `GoldenTemplate_Core` | `AcmeSite_Core` |
| Text Domain | `'golden-template'` | `'acme-site'` |
| Display Name | `Golden Template` | `Acme Site` |

#### CSS Files

| Type | Before | After |
|------|--------|-------|
| Classes | `.golden-template-header` | `.acme-site-header` |
| IDs | `#golden-template-nav` | `#acme-site-nav` |
| Prefixes | `.golden-template-` | `.acme-site-` |

#### JavaScript Files

| Type | Before | After |
|------|--------|-------|
| Objects | `goldenTemplateData` | `acmeSiteData` |
| Functions | `golden_template_init()` | `acme_site_init()` |

#### Theme Header (style.css)

```css
/* Before */
Theme Name: Golden Template
Text Domain: golden-template

/* After (example: "Acme Site") */
Theme Name: Acme Site
Text Domain: acme-site
```

## Naming Conventions

The script generates multiple naming variations from your input:

### Input: `my-awesome-site`

| Variation | Result | Used For |
|-----------|--------|----------|
| Display Name | `My Awesome Site` | Documentation, Theme Name |
| Slug | `my-awesome-site` | Directories, text domain, URLs |
| Constant Prefix | `MY_AWESOME_SITE` | PHP constants |
| Function Prefix | `my_awesome_site` | PHP functions |
| Class Prefix | `MyAwesomeSite` | PHP classes |

### Naming Requirements

✅ **Valid names:**
- `my-site`
- `acme_corp`
- `awesome-project-2024`
- `client123`

❌ **Invalid names:**
- `123-site` (must start with letter)
- `my site` (no spaces allowed - script handles hyphens/underscores)
- `my@site` (special characters not allowed)
- `-mysite` (cannot start with hyphen)

**Rules:**
- Must start with a letter
- Can contain: letters, numbers, hyphens, underscores
- No spaces allowed in the command line argument (use hyphens)

## Script Features

### Dry Run Mode

Test the rename without making changes:

```bash
./rename-project.sh my-project --dry-run
```

**Output:**
- Shows what would be renamed
- Displays all naming variants
- No files are modified
- No backup is created

### Automatic Backup

Before making changes, the script creates a backup:

```bash
.backup_20260211_144500/
├── theme_golden-template/          # Full theme backup
├── mu-plugin_golden-template-core/ # MU plugin backup
└── golden-template-core-loader.php # Loader file backup
```

**Backup location** is saved in `.last_backup_path` for easy rollback.

### Rollback Support

If something goes wrong, restore from backup:

```bash
./rollback-rename.sh
```

This will:
1. Check for most recent backup
2. Confirm rollback action
3. Restore all files from backup
4. Restore original names (`golden-template`)

## Step-by-Step Process

When you run the script, it performs these steps:

1. **Validation**
   - Validates project name format
   - Generates all naming variations (Slug, Constant, Function, Class)
   - Shows preview of changes

2. **Confirmation**
   - Displays what will change
   - Asks for user confirmation

3. **Backup Creation**
   - Creates timestamped backup directory
   - Copies theme, MU plugin, and loader

4. **Directory Renaming**
   - Renames theme directory
   - Renames MU plugin directory
   - Renames MU plugin loader file

5. **File Content Updates**
   - Updates all PHP files (classes, functions, constants)
   - Updates all CSS files (class names, IDs)
   - Updates all JavaScript files
   - Updates theme header in style.css

6. **Completion**
   - Shows success message
   - Displays backup location
   - Provides next steps

## Troubleshooting

### "Permission denied" when running script

```bash
chmod +x rename-project.sh
./rename-project.sh my-project
```

### "sed: RE error: illegal byte sequence"

The script includes `export LC_ALL=C` and `export LANG=C` to handle this common issue on macOS when files contain special characters or non-UTF8 encoding.

### Changes look incorrect

**Solution**: 
1. Run rollback: `./rollback-rename.sh`
2. Try again with dry-run first: `./rename-project.sh my-project --dry-run`
3. Verify the output looks correct

## Post-Rename Steps

### Required Steps

1. **Clear Caches**
   - WordPress object cache
   - PHP opcache

2. **Test the Site**
   - Visit your local site
   - Check for errors in browser console
   - Test block functionality (ACF Pro)

3. **Verify Code Quality**
   ```bash
   ./vendor/bin/phpcs --standard=WordPress-VIP-Go wp-content/themes/your-project-name/
   ```

### Optional Steps

1. **Database Search & Replace** (if you have existing data)
   ```bash
   wp search-replace 'golden-template' 'your-project-name' --all-tables --dry-run
   ```

2. **Remove Backup** (after verifying success)
   ```bash
   rm -rf .backup_*
   ```

## Best Practices

1. ✅ **Always run dry-run first**
2. ✅ **Verify the preview before confirming**
3. ✅ **Keep the backup until fully tested**
4. ✅ **Test thoroughly after renaming**
5. ✅ **Commit to git after successful rename**

---

**Ready to rename?** Run `./rename-project.sh your-project-name --dry-run` to get started!
