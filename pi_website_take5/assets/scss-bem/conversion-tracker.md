# SCSS Conversion to BEM Methodology

This document tracks the progress of converting the existing SCSS files to follow BEM naming conventions.

## Base Files

- [x] variables.scss - Converted to maps for better organization
- [x] mixins.scss - Created with BEM helpers
- [x] reset.scss - Basic reset maintained, modified to use new variable structure
- [ ] typography.scss
- [ ] animations.scss
- [ ] general.scss
- [ ] accessibility.scss

## Layout Files

- [x] header.scss - Converted to `.site-header` block with proper BEM elements
- [ ] footer.scss
- [ ] grid.scss

## Module Files

- [x] home.scss - Converted to `.home` block with proper BEM elements and modifiers
- [ ] team.scss
- [ ] team-member.scss
- [ ] cards.scss
- [ ] buttons.scss
- [ ] forms.scss
- [ ] section.scss
- [ ] navigation.scss
- [ ] breadcrumbs.scss
- [ ] segment.scss
- [ ] segment_display.scss

## State Files

- [ ] states.scss

## Theme Files

- [ ] theme.scss

## HTML Updates Required

After converting the SCSS files, the HTML templates will need to be updated to use the new class names. The changes follow this pattern:

### Before:
```html
<header class="initially-hidden">
  <div class="header-content">
    <a href="#" class="logo">
      <img src="logo.png" alt="Logo">
      <span class="group-name">Company Name</span>
    </a>
    <!-- More content -->
  </div>
</header>
```

### After:
```html
<header class="site-header site-header--hidden">
  <div class="site-header__content">
    <a href="#" class="site-header__logo">
      <img src="logo.png" alt="Logo" class="site-header__logo__image">
      <span class="site-header__logo__text">Company Name</span>
    </a>
    <!-- More content -->
  </div>
</header>
```

## Implementation Plan

1. Convert one component at a time, starting with the most commonly used ones
2. Test each component after conversion
3. Update HTML templates to use new class names
4. Verify styling remains consistent after conversion

## Benefits of BEM Implementation

- Eliminates class name conflicts
- Makes it clear which styles affect which elements
- Creates more maintainable and modular code
- Improves developer experience and reduces debugging time 