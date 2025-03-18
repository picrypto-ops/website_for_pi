#!/bin/bash
# BEM Test Setup Script
# This script sets up everything needed to test the BEM styles

# Ensure we're in the right directory
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$DIR"

echo "========================================="
echo "Setting up BEM testing environment"
echo "========================================="

# Check for SASS/SCSS compiler
if ! command -v sass >/dev/null 2>&1; then
    echo "SASS compiler not found. Installing SASS..."
    npm install -g sass
fi

# Create CSS output directory if it doesn't exist
CSS_DIR="../../../assets/css"
if [ ! -d "$CSS_DIR" ]; then
    echo "Creating CSS output directory..."
    mkdir -p "$CSS_DIR"
fi

# Make sure all required base files exist
BASE_FILES=("../base/_typography.scss" "../base/_animations.scss" "../state/_states.scss" "../themes/_theme.scss")
for file in "${BASE_FILES[@]}"; do
    if [ ! -f "$file" ]; then
        echo "Creating placeholder for $file..."
        mkdir -p $(dirname "$file")
        echo "/* This file was automatically created by the BEM test setup script */" > "$file"
    fi
done

# Compile the BEM SCSS to CSS
echo "Compiling BEM SCSS..."
php compile-bem.php

echo "========================================="
echo "Setup complete!"
echo "========================================="
echo
echo "You can now test the BEM implementation by:"
echo "1. Opening style-tester.php in your browser"
echo "2. Using the Visual Diff tool to compare the original and BEM styles"
echo 
echo "Example URL: http://localhost/your_site_path/assets/scss-bem/tools/style-tester.php"
echo "=========================================" 