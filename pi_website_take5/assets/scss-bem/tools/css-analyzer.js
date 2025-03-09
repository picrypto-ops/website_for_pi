/**
 * CSS Analyzer
 * 
 * A Node.js script to help analyze CSS/SCSS usage and detect duplicate class names
 * across files.
 * 
 * Usage:
 * 1. Ensure Node.js is installed
 * 2. Run: node css-analyzer.js
 */

const fs = require('fs');
const path = require('path');
const glob = require('glob');

// Configuration
const config = {
  // Source directories to analyze
  scssDir: path.resolve(__dirname, '../'),
  // HTML/PHP files to check for class usage
  htmlFiles: path.resolve(__dirname, '../../../**/*.{html,php}'),
  // JavaScript files to check for class usage
  jsFiles: path.resolve(__dirname, '../../../**/*.js'),
  // Output file for the report
  outputFile: path.resolve(__dirname, './css-analysis-report.md')
};

// Regular expression to extract class names from SCSS files
const scssClassRegex = /\.([a-zA-Z0-9_-]+)(?:\s|,|{|:)/g;
// Regular expression for BEM class names (for validation)
const bemClassRegex = /^[a-z]([a-z0-9-])*(__([a-z0-9]+-?)+)?(--([a-z0-9]+-?)+)?$/;
// Regular expression to extract class names from HTML/PHP files
const htmlClassRegex = /class=["']([^"']+)["']/g;
// Regular expression to extract class names from JavaScript files
const jsClassRegex = /(?:addClass|removeClass|hasClass|toggleClass)\(["']([^"']+)["']\)|(?:className=|\.classList\.(?:add|remove|toggle|contains)\(["'])([^"']+)["']/g;

// Main function
async function analyzeCSS() {
  console.log('Starting CSS analysis...');
  
  // Step 1: Extract class names from SCSS files
  const scssFiles = glob.sync(path.join(config.scssDir, '**/*.scss'));
  console.log(`Found ${scssFiles.length} SCSS files to analyze`);
  
  const classesInFiles = {};
  const allScssClasses = new Set();
  const duplicateClasses = {};
  
  // Extract all class names from SCSS files
  scssFiles.forEach(file => {
    const relativePath = path.relative(config.scssDir, file);
    const content = fs.readFileSync(file, 'utf8');
    const classes = new Set();
    
    let match;
    while ((match = scssClassRegex.exec(content)) !== null) {
      const className = match[1];
      
      // Skip .scss-specific functionality
      if (className.startsWith('scss') || 
          className === 'root' || 
          className === 'before' || 
          className === 'after' ||
          className === 'hover' ||
          className === 'focus' ||
          className.startsWith('nth-child')) {
        continue;
      }
      
      // Add to file-specific and global class sets
      classes.add(className);
      
      // Check for duplicates across files
      if (allScssClasses.has(className)) {
        if (!duplicateClasses[className]) {
          duplicateClasses[className] = [];
        }
        duplicateClasses[className].push(relativePath);
      } else {
        allScssClasses.add(className);
      }
    }
    
    classesInFiles[relativePath] = [...classes];
  });
  
  // Step 2: Extract class names from HTML/PHP files
  const htmlFiles = glob.sync(config.htmlFiles);
  console.log(`Found ${htmlFiles.length} HTML/PHP files to analyze`);
  
  const usedClasses = new Set();
  
  htmlFiles.forEach(file => {
    const content = fs.readFileSync(file, 'utf8');
    let match;
    
    while ((match = htmlClassRegex.exec(content)) !== null) {
      const classNames = match[1].split(/\s+/);
      classNames.forEach(className => {
        if (className.trim()) {
          usedClasses.add(className.trim());
        }
      });
    }
  });
  
  // Step 3: Extract class names from JavaScript files
  const jsFiles = glob.sync(config.jsFiles);
  console.log(`Found ${jsFiles.length} JavaScript files to analyze`);
  
  jsFiles.forEach(file => {
    const content = fs.readFileSync(file, 'utf8');
    let match;
    
    while ((match = jsClassRegex.exec(content)) !== null) {
      const className = match[1] || match[2];
      if (className) {
        const classNames = className.split(/\s+/);
        classNames.forEach(cn => {
          if (cn.trim()) {
            usedClasses.add(cn.trim());
          }
        });
      }
    }
  });
  
  // Step 4: Generate report
  console.log('Generating report...');
  
  // Identify unused classes
  const unusedClasses = new Set();
  allScssClasses.forEach(className => {
    if (!usedClasses.has(className)) {
      unusedClasses.add(className);
    }
  });
  
  // Validate against BEM naming convention
  const nonBemClasses = new Set();
  allScssClasses.forEach(className => {
    if (!bemClassRegex.test(className)) {
      nonBemClasses.add(className);
    }
  });
  
  // Generate the report
  let report = `# CSS Analysis Report\n\n`;
  report += `Generated: ${new Date().toLocaleString()}\n\n`;
  
  report += `## Summary\n\n`;
  report += `- Total SCSS files: ${scssFiles.length}\n`;
  report += `- Total unique class names: ${allScssClasses.size}\n`;
  report += `- Classes not following BEM convention: ${nonBemClasses.size}\n`;
  report += `- Duplicate class names across files: ${Object.keys(duplicateClasses).length}\n`;
  report += `- Unused classes: ${unusedClasses.size}\n\n`;
  
  // Duplicate classes section
  if (Object.keys(duplicateClasses).length > 0) {
    report += `## Duplicate Class Names\n\n`;
    
    Object.keys(duplicateClasses).sort().forEach(className => {
      report += `### .${className}\n\n`;
      report += `Found in files:\n`;
      duplicateClasses[className].forEach(file => {
        report += `- \`${file}\`\n`;
      });
      report += `\n`;
    });
  }
  
  // Non-BEM classes section
  if (nonBemClasses.size > 0) {
    report += `## Classes Not Following BEM Convention\n\n`;
    [...nonBemClasses].sort().forEach(className => {
      report += `- \`.${className}\`\n`;
    });
    report += `\n`;
  }
  
  // Unused classes section
  if (unusedClasses.size > 0) {
    report += `## Potentially Unused Classes\n\n`;
    report += `The following classes were not found in HTML/PHP or JavaScript files:\n\n`;
    [...unusedClasses].sort().forEach(className => {
      report += `- \`.${className}\`\n`;
    });
    report += `\n`;
    report += `Note: This may include classes used dynamically or in files not analyzed.\n\n`;
  }
  
  // Write the report to file
  fs.writeFileSync(config.outputFile, report);
  
  console.log(`Analysis complete! Report saved to ${config.outputFile}`);
}

analyzeCSS().catch(err => console.error('Error during CSS analysis:', err)); 