#!/bin/bash

################################################################################
# WordPress Golden Template - Project Rename Script
# 
# This script renames all instances of the template project name throughout
# the WordPress theme and mu-plugin files.
#
# Usage: ./rename-project.sh <new-project-name> [--dry-run]
#
# Example: ./rename-project.sh my-awesome-site
#
# IMPORTANT: Always backup your project before running this script!
################################################################################

set -e  # Exit on error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Current project identifiers
OLD_SLUG="jlbpartners"
OLD_CONSTANT_PREFIX="JLBPARTNERS"
OLD_FUNCTION_PREFIX="jlbpartners"
OLD_CLASS_PREFIX="JLBPartners"
OLD_TEXT_DOMAIN="jlbpartners"
OLD_MU_PLUGIN_SLUG="jlbpartners-core"
OLD_MU_PLUGIN_CLASS="JLBPartners_Core"

# Script variables
DRY_RUN=false
BACKUP_DIR=""
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

################################################################################
# Helper Functions
################################################################################

print_header() {
    echo -e "${CYAN}"
    echo "╔════════════════════════════════════════════════════════════════╗"
    echo "║         WordPress Golden Template - Rename Script             ║"
    echo "╚════════════════════════════════════════════════════════════════╝"
    echo -e "${NC}"
}

print_success() {
    echo -e "${GREEN}✓${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

print_info() {
    echo -e "${BLUE}ℹ${NC} $1"
}

print_step() {
    echo -e "\n${CYAN}▶${NC} $1"
}

################################################################################
# Validation Functions
################################################################################

validate_project_name() {
    local name=$1
    
    # Check if empty
    if [[ -z "$name" ]]; then
        print_error "Project name cannot be empty"
        return 1
    fi
    
    # Check format (letters, numbers, hyphens, underscores only)
    if [[ ! "$name" =~ ^[a-zA-Z0-9_-]+$ ]]; then
        print_error "Project name can only contain letters, numbers, hyphens, and underscores"
        return 1
    fi
    
    # Check if starts with letter
    if [[ ! "$name" =~ ^[a-zA-Z] ]]; then
        print_error "Project name must start with a letter"
        return 1
    fi
    
    return 0
}

################################################################################
# Name Generation Functions
################################################################################

generate_variants() {
    local base_name=$1
    
    # Slug: lowercase with hyphens (my-project)
    NEW_SLUG=$(echo "$base_name" | tr '[:upper:]' '[:lower:]' | tr '_' '-')
    
    # Constant prefix: UPPERCASE with underscores (MY_PROJECT)
    NEW_CONSTANT_PREFIX=$(echo "$base_name" | tr '[:lower:]' '[:upper:]' | tr '-' '_')
    
    # Function prefix: lowercase with underscores (my_project)
    NEW_FUNCTION_PREFIX=$(echo "$base_name" | tr '[:upper:]' '[:lower:]' | tr '-' '_')
    
    # Class prefix: PascalCase (MyProject)
    # Use awk or sed for PascalCase conversion
    NEW_CLASS_PREFIX=$(echo "$base_name" | awk 'BEGIN{FS="-|_"}{for(i=1;i<=NF;i++){$i=toupper(substr($i,1,1)) substr($i,2)}}1' OFS='')
    
    # Text domain: lowercase with hyphens (my-project)
    NEW_TEXT_DOMAIN="$NEW_SLUG"
    
    # MU Plugin slug
    NEW_MU_PLUGIN_SLUG="${NEW_SLUG}-core"
    
    # MU Plugin class
    NEW_MU_PLUGIN_CLASS="${NEW_CLASS_PREFIX}_Core"
}

display_preview() {
    echo -e "\n${CYAN}Preview of changes:${NC}"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    printf "%-25s → %s\n" "Slug:" "$OLD_SLUG → $NEW_SLUG"
    printf "%-25s → %s\n" "Constant Prefix:" "$OLD_CONSTANT_PREFIX → $NEW_CONSTANT_PREFIX"
    printf "%-25s → %s\n" "Function Prefix:" "$OLD_FUNCTION_PREFIX → $NEW_FUNCTION_PREFIX"
    printf "%-25s → %s\n" "Class Prefix:" "$OLD_CLASS_PREFIX → $NEW_CLASS_PREFIX"
    printf "%-25s → %s\n" "Text Domain:" "$OLD_TEXT_DOMAIN → $NEW_TEXT_DOMAIN"
    printf "%-25s → %s\n" "MU Plugin:" "$OLD_MU_PLUGIN_SLUG → $NEW_MU_PLUGIN_SLUG"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
}

################################################################################
# Backup Functions
################################################################################

create_backup() {
    if [[ "$DRY_RUN" = true ]]; then
        print_info "DRY RUN: Would create backup"
        return 0
    fi
    
    print_step "Creating backup..."
    
    TIMESTAMP=$(date +%Y%m%d_%H%M%S)
    BACKUP_DIR="${SCRIPT_DIR}/.backup_${TIMESTAMP}"
    
    mkdir -p "$BACKUP_DIR"
    
    # Backup theme
    if [[ -d "wp-content/themes/$OLD_SLUG" ]]; then
        cp -r "wp-content/themes/$OLD_SLUG" "$BACKUP_DIR/theme_$OLD_SLUG"
        print_success "Theme backed up to: $BACKUP_DIR/theme_$OLD_SLUG"
    fi
    
    # Backup mu-plugin
    if [[ -d "wp-content/mu-plugins/$OLD_MU_PLUGIN_SLUG" ]]; then
        cp -r "wp-content/mu-plugins/$OLD_MU_PLUGIN_SLUG" "$BACKUP_DIR/mu-plugin_$OLD_MU_PLUGIN_SLUG"
        print_success "MU-Plugin backed up to: $BACKUP_DIR/mu-plugin_$OLD_MU_PLUGIN_SLUG"
    fi
    
    # Backup loader
    if [[ -f "wp-content/mu-plugins/${OLD_MU_PLUGIN_SLUG}-loader.php" ]]; then
        cp "wp-content/mu-plugins/${OLD_MU_PLUGIN_SLUG}-loader.php" "$BACKUP_DIR/"
        print_success "MU-Plugin loader backed up"
    fi
    
    echo "$BACKUP_DIR" > .last_backup_path
    print_success "Backup created at: $BACKUP_DIR"
}

################################################################################
# File Content Update Functions
################################################################################

update_file_content() {
    local file=$1
    
    if [[ "$DRY_RUN" = true ]]; then
        return 0
    fi
    
    # Create temp file
    local temp_file="${file}.tmp"
    
    # Perform replacements
    sed -e "s/${OLD_CLASS_PREFIX}_Core/${NEW_MU_PLUGIN_CLASS}/g" \
        -e "s/${OLD_CLASS_PREFIX}/${NEW_CLASS_PREFIX}/g" \
        -e "s/${OLD_CONSTANT_PREFIX}/${NEW_CONSTANT_PREFIX}/g" \
        -e "s/${OLD_FUNCTION_PREFIX}_/${NEW_FUNCTION_PREFIX}_/g" \
        -e "s/'${OLD_TEXT_DOMAIN}'/'${NEW_TEXT_DOMAIN}'/g" \
        -e "s/\"${OLD_TEXT_DOMAIN}\"/\"${NEW_TEXT_DOMAIN}\"/g" \
        -e "s/${OLD_MU_PLUGIN_SLUG}/${NEW_MU_PLUGIN_SLUG}/g" \
        -e "s/${OLD_SLUG}/${NEW_SLUG}/g" \
        "$file" > "$temp_file"
    
    # Replace original file
    mv "$temp_file" "$file"
}

update_css_file() {
    local file=$1
    
    if [[ "$DRY_RUN" = true ]]; then
        return 0
    fi
    
    local temp_file="${file}.tmp"
    
    # Update CSS class prefixes and other references
    sed -e "s/\.${OLD_SLUG}/\.${NEW_SLUG}/g" \
        -e "s/#${OLD_SLUG}/#${NEW_SLUG}/g" \
        -e "s/${OLD_SLUG}-/${NEW_SLUG}-/g" \
        "$file" > "$temp_file"
    
    mv "$temp_file" "$file"
}

update_js_file() {
    local file=$1
    
    if [[ "$DRY_RUN" = true ]]; then
        return 0
    fi
    
    local temp_file="${file}.tmp"
    
    # Update JavaScript object names and references
    sed -e "s/${OLD_FUNCTION_PREFIX}/${NEW_FUNCTION_PREFIX}/g" \
        -e "s/'${OLD_SLUG}'/'${NEW_SLUG}'/g" \
        -e "s/\"${OLD_SLUG}\"/\"${NEW_SLUG}\"/g" \
        -e "s/${OLD_SLUG}-/${NEW_SLUG}-/g" \
        "$file" > "$temp_file"
    
    mv "$temp_file" "$file"
}

################################################################################
# Main Rename Functions
################################################################################

rename_theme_directory() {
    print_step "Renaming theme directory..."
    
    local old_path="wp-content/themes/$OLD_SLUG"
    local new_path="wp-content/themes/$NEW_SLUG"
    
    if [[ ! -d "$old_path" ]]; then
        print_warning "Theme directory not found: $old_path"
        return 0
    fi
    
    if [[ "$DRY_RUN" = true ]]; then
        print_info "DRY RUN: Would rename $old_path → $new_path"
        return 0
    fi
    
    mv "$old_path" "$new_path"
    print_success "Renamed: $old_path → $new_path"
}

rename_mu_plugin_directory() {
    print_step "Renaming MU-plugin directory..."
    
    local old_path="wp-content/mu-plugins/$OLD_MU_PLUGIN_SLUG"
    local new_path="wp-content/mu-plugins/$NEW_MU_PLUGIN_SLUG"
    
    if [[ ! -d "$old_path" ]]; then
        print_warning "MU-plugin directory not found: $old_path"
        return 0
    fi
    
    if [[ "$DRY_RUN" = true ]]; then
        print_info "DRY RUN: Would rename $old_path → $new_path"
        return 0
    fi
    
    mv "$old_path" "$new_path"
    print_success "Renamed: $old_path → $new_path"
}

rename_mu_plugin_loader() {
    print_step "Renaming MU-plugin loader..."
    
    local old_path="wp-content/mu-plugins/${OLD_MU_PLUGIN_SLUG}-loader.php"
    local new_path="wp-content/mu-plugins/${NEW_MU_PLUGIN_SLUG}-loader.php"
    
    if [[ ! -f "$old_path" ]]; then
        print_warning "MU-plugin loader not found: $old_path"
        return 0
    fi
    
    if [[ "$DRY_RUN" = true ]]; then
        print_info "DRY RUN: Would rename $old_path → $new_path"
        return 0
    fi
    
    mv "$old_path" "$new_path"
    print_success "Renamed: $old_path → $new_path"
}

update_all_php_files() {
    print_step "Updating PHP files..."
    
    local count=0
    while IFS= read -r -d '' file; do
        update_file_content "$file"
        ((count++))
    done < <(find wp-content/themes/$NEW_SLUG wp-content/mu-plugins/$NEW_MU_PLUGIN_SLUG -type f -name "*.php" -print0 2>/dev/null)
    
    # Update loader file
    if [[ -f "wp-content/mu-plugins/${NEW_MU_PLUGIN_SLUG}-loader.php" ]]; then
        update_file_content "wp-content/mu-plugins/${NEW_MU_PLUGIN_SLUG}-loader.php"
        ((count++))
    fi
    
    print_success "Updated $count PHP files"
}

update_all_css_files() {
    print_step "Updating CSS files..."
    
    local count=0
    while IFS= read -r -d '' file; do
        update_css_file "$file"
        ((count++))
    done < <(find wp-content/themes/$NEW_SLUG -type f -name "*.css" -print0 2>/dev/null)
    
    while IFS= read -r -d '' file; do
        update_css_file "$file"
        ((count++))
    done < <(find wp-content/mu-plugins/$NEW_MU_PLUGIN_SLUG -type f -name "*.css" -print0 2>/dev/null)
    
    print_success "Updated $count CSS files"
}

update_all_js_files() {
    print_step "Updating JavaScript files..."
    
    local count=0
    while IFS= read -r -d '' file; do
        update_js_file "$file"
        ((count++))
    done < <(find wp-content/themes/$NEW_SLUG -type f -name "*.js" -print0 2>/dev/null)
    
    print_success "Updated $count JavaScript files"
}

update_style_css() {
    print_step "Updating theme header in style.css..."
    
    local style_file="wp-content/themes/$NEW_SLUG/style.css"
    
    if [[ ! -f "$style_file" ]]; then
        print_warning "style.css not found"
        return 0
    fi
    
    if [[ "$DRY_RUN" = true ]]; then
        print_info "DRY RUN: Would update $style_file"
        return 0
    fi
    
    # Update theme header fields
    local temp_file="${style_file}.tmp"
    sed -e "s/Theme Name: JLB Partners/Theme Name: ${NEW_CLASS_PREFIX}/g" \
        -e "s/Text Domain: ${OLD_TEXT_DOMAIN}/Text Domain: ${NEW_TEXT_DOMAIN}/g" \
        -e "s/jlbpartners/${NEW_SLUG}/g" \
        "$style_file" > "$temp_file"
    
    mv "$temp_file" "$style_file"
    print_success "Updated style.css theme header"
}

################################################################################
# Main Script
################################################################################

main() {
    print_header
    
    # Parse arguments
    if [[ $# -lt 1 ]]; then
        print_error "Usage: $0 <new-project-name> [--dry-run]"
        echo ""
        echo "Example: $0 my-awesome-site"
        echo "         $0 my-awesome-site --dry-run"
        exit 1
    fi
    
    PROJECT_NAME=$1
    
    if [[ "${2:-}" == "--dry-run" ]]; then
        DRY_RUN=true
        print_warning "DRY RUN MODE - No files will be modified"
    fi
    
    # Validate project name
    print_step "Validating project name..."
    if ! validate_project_name "$PROJECT_NAME"; then
        exit 1
    fi
    print_success "Project name is valid"
    
    # Generate name variants
    print_step "Generating name variants..."
    generate_variants "$PROJECT_NAME"
    display_preview
    
    # Confirm before proceeding
    if [[ "$DRY_RUN" = false ]]; then
        echo ""
        read -p "$(echo -e ${YELLOW}Proceed with renaming? [y/N]: ${NC})" -n 1 -r
        echo ""
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            print_info "Operation cancelled"
            exit 0
        fi
    fi
    
    # Create backup
    create_backup
    
    # Perform renames
    rename_theme_directory
    rename_mu_plugin_directory
    rename_mu_plugin_loader
    
    # Update file contents
    update_all_php_files
    update_all_css_files
    update_all_js_files
    update_style_css
    
    # Final message
    echo ""
    echo -e "${GREEN}╔════════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${GREEN}║                    ✓ Rename Complete!                         ║${NC}"
    echo -e "${GREEN}╚════════════════════════════════════════════════════════════════╝${NC}"
    
    if [[ "$DRY_RUN" = false ]]; then
        echo ""
        print_info "Backup location: $BACKUP_DIR"
        print_info "To rollback, run: ./rollback-rename.sh"
        echo ""
        print_warning "Next steps:"
        echo "  1. Clear WordPress cache"
        echo "  2. Test your site thoroughly"
        echo "  3. Update wp-config.php if needed"
        echo "  4. Search database for old references (optional)"
        echo ""
    else
        echo ""
        print_info "This was a dry run. No files were modified."
        print_info "Remove --dry-run flag to perform actual rename."
        echo ""
    fi
}

# Run main function
main "$@"
