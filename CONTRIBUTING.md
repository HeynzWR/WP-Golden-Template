# Contributing to WordPress Projects

Development standards and guidelines for contributing to WordPress projects built with this golden template.

## Code Standards

### WordPress VIP Coding Standards

All code must follow [WordPress VIP Coding Standards](https://docs.wpvip.com/technical-references/vip-codebase/). This is stricter than regular WordPress standards.

**Required before committing:**

```bash
# Check your code
./vendor/bin/phpcs --standard=WordPress-VIP-Go wp-content/themes/your-theme-name/

# Should show 0 ERRORS
# Warnings are acceptable but should be minimized
```

### Key Standards

#### 1. Always Escape Output

```php
// ❌ WRONG
echo $title;

// ✅ CORRECT
echo esc_html( $title );
echo esc_attr( $attribute );
echo esc_url( $url );
echo wp_kses_post( $content );  // For HTML content
```

#### 2. Always Sanitize Input

```php
// ❌ WRONG
$search = $_GET['search'];

// ✅ CORRECT
$search = isset( $_GET['search'] ) ? sanitize_text_field( wp_unslash( $_GET['search'] ) ) : '';
```

#### 3. Use Proper Naming

```php
// Function names: your_project_function_name
function acme_corp_setup() { }

// Class names: Your_Project_Class_Name
class AcmeCorp_Widget { }

// Constants: YOUR_PROJECT_CONSTANT
define( 'ACME_CORP_VERSION', '1.0.0' );

// Text domain: your-project-slug
__( 'Text', 'acme-corp' );
```

#### 4. Avoid Global Variable Conflicts

```php
// ❌ WRONG
foreach ( $categories as $cat ) { }
foreach ( $links as $link ) { }

// ✅ CORRECT
foreach ( $categories as $category_item ) { }
foreach ( $links as $link_item ) { }
```

## Git Workflow

### Branch Naming

```bash
# Features
feature/add-testimonial-block
feature/contact-form

# Bug fixes
fix/header-menu-spacing
fix/mobile-responsive-issue

# Improvements
improve/performance-optimization
improve/accessibility-updates
```

### Commit Messages

Follow conventional commits format:

```bash
# Format
<type>(<scope>): <description>

# Examples
feat(blocks): add testimonial slider block
fix(header): resolve mobile menu toggle issue
style(footer): update spacing and colors
docs(readme): add setup instructions
refactor(theme): reorganize asset loading
perf(images): optimize hero section images
```

**Types:**
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation
- `style`: CSS/styling changes
- `refactor`: Code refactoring
- `perf`: Performance improvements
- `test`: Adding tests
- `chore`: Maintenance tasks

### Pull Request Process

1. **Create Feature Branch**
   ```bash
   git checkout -b feature/your-feature-name
   ```

2. **Make Changes**
   - Follow coding standards
   - Test thoroughly
   - Update documentation if needed

3. **Run Code Quality Checks**
   ```bash
   # PHPCS
   ./vendor/bin/phpcs --standard=WordPress-VIP-Go wp-content/themes/your-theme/
   
   # No ERRORS should appear
   ```

4. **Commit Changes**
   ```bash
   git add .
   git commit -m "feat(blocks): add pricing table block"
   ```

5. **Push and Create PR**
   ```bash
   git push origin feature/your-feature-name
   ```

6. **PR Requirements**
   - Clear description of changes
   - Screenshots for visual changes
   - No PHPCS errors
   - Tested on local environment

## Creating ACF Blocks

### Use the Block Development Guidelines

Always follow [BLOCK_DEVELOPMENT_GUIDELINES.md](BLOCK_DEVELOPMENT_GUIDELINES.md) when creating new blocks.

### Block Checklist

Before creating a block:
- [ ] Read the block guidelines completely
- [ ] Understand the naming conventions
- [ ] Know which fields you need
- [ ] Have a design/mockup ready

While creating:
- [ ] Use proper file structure (`blocks/block-name/`)
- [ ] Follow field naming for auto-population
- [ ] Include accessibility fields for images
- [ ] Add preview image
- [ ] Register in all required files

After creating:
- [ ] Run PHPCS check
- [ ] Test in block editor
- [ ] Test on frontend
- [ ] Test responsively (mobile, tablet, desktop)
- [ ] Test accessibility with screen reader

## File Organization

### Theme Structure

```
wp-content/themes/your-theme/
├── blocks/               # ACF blocks
│   └── block-name/
│       ├── fields.php    # ACF field definitions
│       ├── template.php  # Block template
│       └── style.css     # Block styles (optional)
├── inc/                  # Theme includes
│   ├── blocks/          # Block registration
│   ├── post-types/      # Custom post types
│   └── taxonomies/      # Custom taxonomies
├── assets/              # Theme assets
│   ├── css/
│   ├── js/
│   └── images/
└── functions.php        # Main theme file
```

### File Naming

- **PHP files**: `kebab-case.php`
- **CSS files**: `kebab-case.css`
- **JS files**: `kebab-case.js`
- **Directories**: `kebab-case/`

## CSS Guidelines

### Use CSS Custom Properties

```css
/* Define in :root */
:root {
    --color-primary: #00a400;
    --spacing-md: 2rem;
}

/* Use in styles */
.button {
    background: var(--color-primary);
    padding: var(--spacing-md);
}
```

### BEM-ish Naming Convention

```css
/* Block */
.block-name { }

/* Element */
.block-name__element { }

/* Modifier */
.block-name--modifier { }

/* Example */
.testimonial-card { }
.testimonial-card__image { }
.testimonial-card__quote { }
.testimonial-card--featured { }
```

### Mobile-First Responsive

```css
/* Base (mobile) */
.element {
    font-size: 16px;
}

/* Tablet up */
@media screen and (min-width: 768px) {
    .element {
        font-size: 18px;
    }
}

/* Desktop up */
@media screen and (min-width: 1199px) {
    .element {
        font-size: 20px;
    }
}
```

## JavaScript Guidelines

### Modern ES6+

```javascript
// Use const/let, not var
const elements = document.querySelectorAll('.item');

// Arrow functions
elements.forEach(el => {
    el.addEventListener('click', () => {
        // Handle click
    });
});

// Template literals
const message = `Hello, ${username}!`;
```

### Avoid jQuery for New Code

For new code, use vanilla JavaScript:

```javascript
// ❌ jQuery
$('.element').addClass('active');

// ✅ Vanilla JS
document.querySelector('.element').classList.add('active');
```

**Exception**: If jQuery is already loaded (legacy code), you can use it.

## Performance Best Practices

### Images

- Use WebP format when possible
- Provide width and height attributes
- Use `loading="lazy"` for below-the-fold images
- Optimize images before uploading (use tools like ImageOptim)

### CSS

- Minimize use of `!important`
- Avoid deeply nested selectors
- Use CSS custom properties for repeated values
- Minify for production

### JavaScript

- Load scripts in footer when possible
- Use async/defer appropriately
- Minimize DOM manipulation
- Debounce scroll/resize events

## Accessibility Requirements

### Semantic HTML

```html
<!-- ✅ GOOD -->
<header>
    <nav>
        <ul>
            <li><a href="/">Home</a></li>
        </ul>
    </nav>
</header>

<!-- ❌ BAD -->
<div class="header">
    <div class="nav">
        <div class="menu-item">
            <span onclick="...">Home</span>
        </div>
    </div>
</div>
```

### ARIA Labels

```html
<!-- For decorative images -->
<img src="pattern.jpg" alt="" aria-hidden="true">

<!-- For informational images -->
<img src="product.jpg" alt="Blue cotton t-shirt">

<!-- For buttons without text -->
<button aria-label="Close menu">
    <svg>...</svg>
</button>
```

### Keyboard Navigation

- All interactive elements must be keyboard accessible
- Use proper focus states
- Maintain logical tab order
- Test with keyboard only

## Testing Checklist

Before submitting code:

### Functionality
- [ ] Works as expected in latest WordPress
- [ ] No JavaScript errors in console
- [ ] No PHP errors or warnings
- [ ] ACF blocks appear and function correctly

### Code Quality
- [ ] PHPCS passes (0 errors)
- [ ] No hardcoded URLs or paths
- [ ] All output escaped properly
- [ ] All input sanitized

### Cross-Browser
- [ ] Chrome/Edge
- [ ] Firefox
- [ ] Safari

### Responsive
- [ ] Mobile (320px - 767px)
- [ ] Tablet (768px - 1198px)
- [ ] Desktop (1199px+)

### Accessibility
- [ ] Keyboard navigable
- [ ] Screen reader tested (VoiceOver/NVDA)
- [ ] Sufficient color contrast
- [ ] Proper heading hierarchy

## Documentation

### Code Comments

```php
/**
 * Function description in sentence case.
 *
 * @param string $param Parameter description.
 * @return bool Return value description.
 */
function your_function( $param ) {
    // Single-line comment for explanation
    
    /*
     * Multi-line comment for
     * longer explanations
     */
}
```

### Update README

When adding major features:
- Update README.md
- Update relevant documentation files
- Add usage examples if needed

## Questions?

- Check [BLOCK_DEVELOPMENT_GUIDELINES.md](BLOCK_DEVELOPMENT_GUIDELINES.md) for block-specific questions
- Review [SETUP.md](SETUP.md) for environment questions
- Contact your development team lead

---

**Remember**: Code quality matters! Take time to write clean, well-documented code that follows standards.
