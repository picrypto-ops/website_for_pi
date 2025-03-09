# BEM SCSS Architecture

This directory contains a BEM (Block, Element, Modifier) implementation of the website's SCSS structure. BEM is a naming convention that helps to create reusable components and code sharing in front-end development.

## What is BEM?

BEM stands for Block, Element, Modifier:

- **Block**: A standalone entity that is meaningful on its own (e.g., `site-header`, `button`)
- **Element**: A part of a block that has no standalone meaning (e.g., `site-header__logo`, `button__icon`)
- **Modifier**: A flag on a block or element to change appearance or behavior (e.g., `site-header--hidden`, `button--primary`)

## Naming Convention

```
.block {}                     /* Block */
.block__element {}            /* Element */
.block--modifier {}           /* Modifier */
.block__element--modifier {}  /* Element with modifier */
```

## File Structure

```
scss-bem/
|-- base/              # Foundation styles
|   |-- _variables.scss    # Variables using maps for organization
|   |-- _mixins.scss       # Mixins including BEM helpers
|   |-- _reset.scss        # Basic reset styles
|   |-- _typography.scss   # Typography styles
|   |-- _animations.scss   # Animation definitions
|   `-- _index.scss        # Forwards all base styles
|
|-- layout/            # Layout components
|   |-- _header.scss       # Header styles
|   |-- _footer.scss       # Footer styles
|   |-- _grid.scss         # Grid system
|   `-- _index.scss        # Forwards all layout styles
|
|-- modules/           # Reusable components
|   |-- _home.scss         # Home page specific components
|   |-- _buttons.scss      # Button styles
|   |-- _cards.scss        # Card components
|   |-- _forms.scss        # Form components
|   `-- _index.scss        # Forwards all module styles
|
|-- state/             # State changes
|   |-- _states.scss       # State-based styles
|   `-- _index.scss        # Forwards all state styles
|
|-- themes/            # Theme variations
|   |-- _theme.scss        # Theme definitions
|   `-- _index.scss        # Forwards all theme styles
|
|-- tools/             # Utility scripts
|   |-- css-analyzer.js    # Analyzes CSS usage and duplicates
|   `-- html-converter.js  # Helps convert HTML to use BEM classes
|
|-- main.scss          # Main file that imports all partials
|-- conversion-tracker.md  # Tracks progress of SCSS conversion
`-- README.md          # This file
```

## How to Use

### In SCSS Files

Use the provided mixins to build BEM components:

```scss
.my-component {
  // Block styles
  background-color: color('primary');
  
  @include element('title') {
    // Element styles
    font-size: 1.5rem;
    
    @include modifier('large') {
      // Modifier styles
      font-size: 2rem;
    }
  }
}
```

This will generate:

```css
.my-component {
  background-color: #4A6B9F;
}
.my-component__title {
  font-size: 1.5rem;
}
.my-component__title--large {
  font-size: 2rem;
}
```

### Accessing Variables

Variables are organized in Sass maps for better structure:

```scss
// Old way
color: $primary-color;

// New way
color: color('primary');
```

For nested component properties:

```scss
color: component-color('button.primary');
```

### In HTML Files

```html
<!-- Example of BEM structure in HTML -->
<header class="site-header">
  <div class="site-header__content">
    <a href="#" class="site-header__logo">
      <img src="logo.png" class="site-header__logo__image">
      <span class="site-header__logo__text">Company Name</span>
    </a>
    <nav class="site-header__nav">
      <ul>
        <li class="site-header__nav__item">
          <a href="#" class="site-header__nav__link site-header__nav__link--active">Home</a>
        </li>
      </ul>
    </nav>
  </div>
</header>
```

## Conversion Process

To convert an existing component:

1. Identify the logical "blocks" in the component
2. Rename base classes to follow BEM naming (e.g., `.header` → `.site-header`)
3. Rename elements to use the block name with double underscore (e.g., `.logo` → `.site-header__logo`)
4. Rename modifiers to use double hyphens (e.g., `.active` → `.site-header--active`)
5. Update the HTML templates to use the new class names

Use the tools provided in the `/tools` directory to help with this process.

## Benefits of BEM

- **Clarity**: Makes the relationship between HTML and CSS clear
- **Modularity**: Makes components more modular and reusable
- **Scalability**: Helps avoid selector specificity issues
- **Team Friendly**: Makes code easier to read and maintain 