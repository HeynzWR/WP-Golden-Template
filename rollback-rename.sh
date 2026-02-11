#!/bin/bash

################################################################################
# WordPress Golden Template - Rollback Script
# 
# This script restores the previous state from the most recent backup
# created by rename-project.sh
#
# Usage: ./rollback-rename.sh
################################################################################

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Template originals
TEMPLATE_SLUG="golden-template"
TEMPLATE_MU_SLUG="golden-template-core"

print_error() {
    echo -e "${RED}✗${NC} $1"
}

print_success() {
    echo -e "${GREEN}✓${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

print_info() {
    echo -e "${BLUE}ℹ${NC} $1"
}

echo -e "${YELLOW}"
echo "╔════════════════════════════════════════════════════════════════╗"
echo "║              Rollback Rename Operation                        ║"
echo "╚════════════════════════════════════════════════════════════════╝"
echo -e "${NC}"

# Check for backup path file
if [[ ! -f .last_backup_path ]]; then
    print_error "No backup found. Cannot rollback."
    print_info "Backup path file (.last_backup_path) not found."
    exit 1
fi

BACKUP_DIR=$(cat .last_backup_path)

if [[ ! -d "$BACKUP_DIR" ]]; then
    print_error "Backup directory not found: $BACKUP_DIR"
    exit 1
fi

print_info "Backup found: $BACKUP_DIR"
echo ""

# Confirm rollback
read -p "$(echo -e ${YELLOW}Are you sure you want to rollback? This will restore from backup. [y/N]: ${NC})" -n 1 -r
echo ""

if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    print_info "Rollback cancelled"
    exit 0
fi

# Restore theme
if [[ -d "$BACKUP_DIR/theme_${TEMPLATE_SLUG}" ]]; then
    print_info "Restoring theme..."
    
    # Remove current theme variations
    find wp-content/themes -maxdepth 1 -type d ! -name "themes" ! -name "index.php" -exec rm -rf {} + 2>/dev/null || true
    
    # Restore from backup
    cp -r "$BACKUP_DIR/theme_${TEMPLATE_SLUG}" "wp-content/themes/${TEMPLATE_SLUG}"
    print_success "Theme restored"
fi

# Restore MU plugin
if [[ -d "$BACKUP_DIR/mu-plugin_${TEMPLATE_MU_SLUG}" ]]; then
    print_info "Restoring MU plugin..."
    
    # Remove current mu-plugin variations
    find wp-content/mu-plugins -maxdepth 1 -type d -name "*-core" -exec rm -rf {} + 2>/dev/null || true
    
    cp -r "$BACKUP_DIR/mu-plugin_${TEMPLATE_MU_SLUG}" "wp-content/mu-plugins/${TEMPLATE_MU_SLUG}"
    print_success "MU plugin restored"
fi

# Restore loader
if [[ -f "$BACKUP_DIR/${TEMPLATE_MU_SLUG}-loader.php" ]]; then
    print_info "Restoring MU plugin loader..."
    
    # Remove other loaders
    find wp-content/mu-plugins -maxdepth 1 -type f -name "*-core-loader.php" -delete 2>/dev/null || true
    
    cp "$BACKUP_DIR/${TEMPLATE_MU_SLUG}-loader.php" "wp-content/mu-plugins/${TEMPLATE_MU_SLUG}-loader.php"
    print_success "MU plugin loader restored"
fi

# Remove backup path file
rm .last_backup_path

echo ""
echo -e "${GREEN}╔════════════════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║                  ✓ Rollback Complete!                         ║${NC}"
echo -e "${GREEN}╚════════════════════════════════════════════════════════════════╝${NC}"
echo ""
print_info "Project has been restored to: ${TEMPLATE_SLUG}"
print_warning "Backup preserved at: $BACKUP_DIR"
echo ""
