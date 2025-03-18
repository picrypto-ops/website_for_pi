# PI Website Includes

This directory contains the core functionality for the PI Website, including helper functions, classes, and templates.

## Language Switcher

The website supports multilingual content with English (en) and Hebrew (he) languages. A language selector is implemented in `functions.php` through the `renderLanguageSwitcher()` function.

### How to Use the Language Switcher

To add a language switcher to any page, use the following code:

```php
<?php echo renderLanguageSwitcher($lang); ?>
```

#### Parameters:

- `$lang` (string): Current language code ('en' or 'he')
- `$additionalClasses` (string, optional): Additional CSS classes to apply to the language switcher container
- `$includeWrapper` (boolean, optional, default: true): Whether to include the outer div wrapper

#### Example with Additional Classes:

```php
<?php echo renderLanguageSwitcher($lang, 'my-custom-class another-class'); ?>
```

#### Example Without Wrapper Div:

```php
<!-- Use this when you already have a container div -->
<div class="my-container">
    <?php echo renderLanguageSwitcher($lang, '', false); ?>
</div>
```

### Styling the Language Switcher

The language switcher HTML structure is as follows:

```html
<div class="language-switcher [additional-classes]">
    <a href="?lang=en&..." class="lang-switch [active]" data-lang="en">EN</a>
    <span class="separator">|</span>
    <a href="?lang=he&..." class="lang-switch [active]" data-lang="he">עב</a>
</div>
```

CSS styling for the language switcher is defined in:
- `assets/scss/layout/_header.scss` - For the header language switcher
- `assets/scss/modules/_home.scss` - For the home page hero language switcher

### JavaScript Behavior

The language switcher's interactive behavior is controlled by `assets/js/language-switcher.js`, which:
1. Captures the current scroll position and active section
2. Stores this information in sessionStorage
3. Restores the position after language switch
4. Handles RTL/LTR switching based on the selected language

## Other Functionality

This directory also contains:
- Translation handling (`language.php`)
- Cache management (`cache.php`)
- Image optimization (`image-optimizer.php`)
- Structured data for SEO (`structured-data.php`)
- Various component templates and helpers 