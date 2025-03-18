# BEM Testing Files

These HTML files help test the BEM styling directly:

1. **original.html** - Uses the original CSS
2. **bem.html** - Uses the BEM CSS

## Testing Procedure

1. Open each file in a browser
2. Compare the visual appearance
3. They should look identical if the BEM implementation is correct
4. If there are differences, check the browser console for errors

## CSS Paths

If the styles aren't loading, check that the CSS paths are correct. You might need to adjust:

- The relative paths in the `<link>` tags
- Ensure the CSS files have been compiled successfully
- Check if file permissions allow reading the CSS files

---

For more comprehensive testing, use the style-tester.php and style-visual-diff.php tools.