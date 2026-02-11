# Project Rename Guide

Detailed documentation for the `rename-project.sh` script included in this WordPress golden template.

## Overview

The rename script automates the process of customizing this template for your specific project. It renames all instances of `jlbpartners` throughout the codebase to your project name.

## Quick Usage

```bash
# Dry run (recommended first)
./rename-project.sh my-new-project --dry-run

# Actual rename
./rename-project.sh my-new-project
```

## What Gets Renamed

### Directory Names

| Before | After (example: "acme-site") |
|--------|------------------------------|
| `wp-content/themes/jlbpartners/` | `wp-content/themes/acme-site/` |
| `wp-content/mu-plugins/jlbpartners-core/` | `wp-content/mu-plugins/acme-site-core/` |
| `wp-content/mu-plugins/jlbpartners-core-loader.php` | `wp-content/mu-plugins/acme-site-core-loader.php` |

### File Contents

#### PHP Files

| Type | Before | After (example: "acme_site") |
|------|--------|------------------------------|
| Constants | `JLBPARTNERS_VERSION` | `ACME_SITE_VERSION` |
| Functions | `jlbpartners_setup()` | `acme_site_setup()` |
| Classes | `JLBPartners_Core` | `AcmeSite_Core` |
| Text Domain | `'jlbpartners'` | `'acme-site'` |

#### CSS Files

| Type | Before | After |
|------|--------|-------|
| Classes | `.jlbpartners-header` | `.acme-site-header` |
| IDs | `#jlbpartners-nav` | `#acme-site-nav` |
| Prefixes | `.jlbpartners-` | `.acme-site-` |

#### JavaScript Files

| Type | Before | After |
|------|--------|-------|
| Objects | `jlbpartnersData` | `acmeSiteData` |
| Functions | `jlbpartners_init()` | `acme_site_init()` |

#### Theme Header (style.css)

```css
/* Before */
Theme Name: JLB Partners
Text Domain: jlbpartners

/* After (example: "Acme Site") */
Theme Name: AcmeSite
Text Domain: acme-site
```

## Naming Conventions

The script generates multiple naming variations from your input:

### Input: `my-awesome-site`

| Variation | Result | Used For |
|-----------|--------|----------|
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
- `my site` (no spaces allowed)
- `my@site` (special characters not allowed)
- `-mysite` (cannot start with hyphen)

**Rules:**
- Must start with a letter
- Can contain: letters, numbers, hyphens, underscores
- No spaces or special characters

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
├── theme_jlbpartners/          # Full theme backup
├── mu-plugin_jlbpartners-core/ # MU plugin backup
└── jlbpartners-core-loader.php # Loader file backup
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
4. Preserve the backup directory

## Step-by-Step Process

When you run the script, it performs these steps:

1. **Validation**
   - Validates project name format
   - Generates all naming variations
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

## Example Run

```bash
$ ./rename-project.sh acme-corp

╔════════════════════════════════════════════════════════════════╗
║         WordPress Golden Template - Rename Script             ║
╚════════════════════════════════════════════════════════════════╝

▶ Validating project name...
✓ Project name is valid

▶ Generating name variants...

Preview of changes:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Slug:                    → jlbpartners → acme-corp
Constant Prefix:         → JLBPARTNERS → ACME_CORP
Function Prefix:         → jlbpartners → acme_corp
Class Prefix:            → JLBPartners → AcmeCorp
Text Domain:             → jlbpartners → acme-corp
MU Plugin:               → jlbpartners-core → acme-corp-core
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

⚠ Proceed with renaming? [y/N]: y

▶ Creating backup...
✓ Theme backed up to: .backup_20260211_144500/theme_jlbpartners
✓ MU-Plugin backed up to: .backup_20260211_144500/mu-plugin_jlbpartners-core
✓ MU-Plugin loader backed up
✓ Backup created at: .backup_20260211_144500

▶ Renaming theme directory...
✓ Renamed: wp-content/themes/jlbpartners → wp-content/themes/acme-corp

▶ Renaming MU-plugin directory...
✓ Renamed: wp-content/mu-plugins/jlbpartners-core → wp-content/mu-plugins/acme-corp-core

▶ Renaming MU-plugin loader...
✓ Renamed: wp-content/mu-plugins/jlbpartners-core-loader.php → wp-content/mu-plugins/acme-corp-core-loader.php

▶ Updating PHP files...
✓ Updated 127 PHP files

▶ Updating CSS files...
✓ Updated 43 CSS files

▶ Updating JavaScript files...
✓ Updated 18 JavaScript files

▶ Updating theme header in style.css...
✓ Updated style.css theme header

╔════════════════════════════════════════════════════════════════╗
║                    ✓ Rename Complete!                         ║
╚════════════════════════════════════════════════════════════════╝

ℹ Backup location: .backup_20260211_144500
ℹ To rollback, run: ./rollback-rename.sh

⚠ Next steps:
  1. Clear WordPress cache
  2. Test your site thoroughly
  3. Update wp-config.php if needed
  4. Search database for old references (optional)
```

## Post-Rename Steps

### Required Steps

1. **Clear Caches**
   ```bash
   # WordPress object cache
   wp cache flush
   
   # PHP opcache (DDEV example)
   ddev exec "sudo service php8.1-fpm reload"
   ```

2. **Test the Site**
   - Visit your local site
   - Check for errors in browser console
   - Test block functionality
   - Verify theme is active

3. **Verify Code Quality**
   ```bash
   ./vendor/bin/phpcs --standard=WordPress-VIP-Go wp-content/themes/acme-corp/
   ```

### Optional Steps

1. **Database Search & Replace** (if you have existing data)
   ```bash
   wp search-replace 'jlbpartners' 'acme-corp' --all-tables --dry-run
   # If looks good, run without --dry-run
   ```

2. **Update wp-config.php** (if needed)
   - Update table prefix if changed
   - Update any theme-specific constants

3. **Remove Backup** (after verifying success)
   ```bash
   rm -rf .backup_*
   ```

## What's NOT Renamed

The script does NOT automatically update:

- ❌ Database content (option names, post meta, etc.)
- ❌ Uploaded files in `wp-content/uploads/`
- ❌ Plugin files (except the MU plugin included)
- ❌ WordPress core files
- ❌ `.git` directory
- ❌ `vendor/` directory
- ❌ `node_modules/` directory

**For database updates**, see the "Optional: Database Search & Replace" section in [SETUP.md](SETUP.md).

## Troubleshooting

### "Permission denied" when running script

```bash
chmod +x rename-project.sh
./rename-project.sh my-project
```

### Script stops with "command not found"

**Cause**: Missing `sed` or other Unix utilities

**Solution**: Ensure you're running on macOS, Linux, or WSL (Windows Subsystem for Linux)

### Changes look incorrect

**Solution**: 
1. Run rollback: `./rollback-rename.sh`
2. Try again with dry-run first: `./rename-project.sh my-project --dry-run`
3. Verify the output looks correct
4. Run actual rename

### Want to rename again

If you need to rename to a different name:

1. Rollback first: `./rollback-rename.sh`
2. Run rename with new name: `./rename-project.sh new-name`

OR

1. Manually update the script variables at the top:
   ```bash
   OLD_SLUG="current-name"  # Your current name
   OLD_CONSTANT_PREFIX="CURRENT_NAME"
   # ... etc
   ```
2. Run the rename script

## Advanced Usage

### Custom Find & Replace

If you need to rename something specific:

```bash
# Find all occurrences
grep -r "old-text" wp-content/themes/your-theme/

# Replace in all files
find wp-content/themes/your-theme/ -type f -exec sed -i '' 's/old-text/new-text/g' {} +
```

### Backup Management

```bash
# List all backups
ls -la | grep .backup_

# Remove old backups
rm -rf .backup_*

# Keep specific backup
mv .backup_20260211_144500 backups/golden-template-backup
```

## Best Practices

1. ✅ **Always run dry-run first**
2. ✅ **Verify the preview before confirming**
3. ✅ **Keep the backup until fully tested**
4. ✅ **Test thoroughly after renaming**
5. ✅ **Run code standards check**
6. ✅ **Commit to git after successful rename**

## Support

For issues with the rename script:
1. Check this guide's troubleshooting section
2. Review the script output for specific errors
3. Run with dry-run to preview changes
4. Contact your development team lead

---

**Ready to rename?** Run `./rename-project.sh your-project-name --dry-run` to get started!
