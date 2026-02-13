# WordPress Golden Template

A production-ready WordPress starter template with ACF block-based architecture, VIP coding standards, and automated project renaming.

## 🚀 Overview

This is a **golden template** repository designed to be the foundation for all WordPress projects. It includes:

- ✅ **ACF Block-based architecture** - Flexible, reusable components
- ✅ **WordPress VIP coding standards** - Production-ready code quality
- ✅ **Custom MU plugin** for core functionality
- ✅ **Automated rename script** - Clone and customize in minutes
- ✅ **Comprehensive documentation** - Block development guidelines included
- ✅ **Modern development workflow** - Composer, PHPCS, organized structure

## 🎯 Quick Start

### New Project Setup

1. **Clone this repository**
   ```bash
   git clone <repository-url> my-new-project
   cd my-new-project
   ```

2. **Run the rename script**
   ```bash
   ./rename-project.sh my-project-name
   ```
   
   This will rename all instances of `golden-template` to your project name throughout the codebase.

3. **Install dependencies**
   ```bash
   composer install
   ```

4. **Read the detailed setup guide**
   ```bash
   cat SETUP.md
   ```

## 📁 What's Included

### Theme (`wp-content/themes/golden-template/`)
- Modern ACF block-based architecture
- Organized component system
- Responsive styles with CSS custom properties
- JavaScript modules for interactivity
- Custom templates for post types

### MU Plugin (`wp-content/mu-plugins/golden-template-core/`)
- Core theme functionality
- Update management
- Plugin dependency checks
- Branding and settings management
- WordPress VIP compliant

### Documentation
- **[SETUP.md](SETUP.md)** - Complete setup guide for new projects
- **[RENAME-GUIDE.md](RENAME-GUIDE.md)** - How to use the rename script
- **[BLOCK_DEVELOPMENT_GUIDELINES.md](BLOCK_DEVELOPMENT_GUIDELINES.md)** - ACF block creation system
- **[CONTRIBUTING.md](CONTRIBUTING.md)** - Development guidelines
- **[STRUCTURE.md](STRUCTURE.md)** - Repository architecture

## 🛠️ Technology Stack

- **WordPress** 6.0+
- **PHP** 8.1+
- **Advanced Custom Fields (ACF)** Pro
- **Composer** for dependency management
- **PHPCS** with WordPress VIP standards
- **Modern CSS** with custom properties
- **Vanilla JavaScript** (no jQuery dependency for modern features)

## 🎨 Creating ACF Blocks

This template includes a comprehensive block development system. To create a new block:

```bash
# AI agents can automatically create blocks using the guidelines
"Create a testimonial-card block with image, quote, author, and rating 
using the Golden Template guidelines"
```

See [BLOCK_DEVELOPMENT_GUIDELINES.md](BLOCK_DEVELOPMENT_GUIDELINES.md) for complete documentation.

## 📋 Features

### Development Features
- ✅ WordPress VIP coding standards pre-configured
- ✅ Template debugging system (enable in functions.php)
- ✅ Smart asset loading (conditional CSS/JS)
- ✅ Cache optimization built-in
- ✅ ACF block preview system

### Production Features
- ✅ Performance optimized
- ✅ Security hardened (XML-RPC disabled, etc.)
- ✅ SEO ready
- ✅ Accessibility focused
- ✅ Mobile responsive

## 🔧 Development Workflow

### Code Quality

Run coding standards check:
```bash
./vendor/bin/phpcs --standard=WordPress-VIP-Minimum wp-content/themes/golden-template/
```

### Testing

Enable template debugging in `functions.php`:
```php
define( 'GOLDEN_TEMPLATE_DEBUG_TEMPLATES', true );
```

## 📖 Documentation

| Document | Purpose |
|----------|---------|
| [SETUP.md](SETUP.md) | Environment setup and installation |
| [RENAME-GUIDE.md](RENAME-GUIDE.md) | Using the rename script |
| [BLOCK_DEVELOPMENT_GUIDELINES.md](BLOCK_DEVELOPMENT_GUIDELINES.md) | Creating ACF blocks |
| [CONTRIBUTING.md](CONTRIBUTING.md) | Development standards |
| [STRUCTURE.md](STRUCTURE.md) | Codebase organization |

## 🤝 Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for development guidelines and workflow.

## ⚠️ Important Notes

- **Always backup** before running the rename script
- **Test thoroughly** after renaming
- **Follow WordPress VIP standards** for all new code
- **Use the block guidelines** for creating ACF blocks


## 🆘 Support

For issues or questions:
1. Check the relevant documentation file
2. Review [BLOCK_DEVELOPMENT_GUIDELINES.md](BLOCK_DEVELOPMENT_GUIDELINES.md) for block-specific questions

---

**Ready to start?** → Read [SETUP.md](SETUP.md) for detailed setup instructions!
