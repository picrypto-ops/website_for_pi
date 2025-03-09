# BEM Test Setup Script for Windows
# This PowerShell script sets up everything needed to test the BEM styles

# Ensure we're in the right directory
$scriptPath = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location -Path $scriptPath

Write-Host "========================================="
Write-Host "Setting up BEM testing environment"
Write-Host "========================================="

# Check for SASS/SCSS compiler
$sassInstalled = $false
try {
    $sassVersion = sass --version
    $sassInstalled = $true
    Write-Host "SASS compiler found: $sassVersion"
} catch {
    Write-Host "SASS compiler not found. Please install SASS using npm:"
    Write-Host "npm install -g sass"
    Write-Host "After installing SASS, run this script again."
    exit 1
}

# Create CSS output directory if it doesn't exist
$cssDir = "..\..\..\assets\css"
if (-not (Test-Path $cssDir)) {
    Write-Host "Creating CSS output directory..."
    New-Item -ItemType Directory -Force -Path $cssDir | Out-Null
}

# Make sure all required base files exist
$baseFiles = @(
    "..\base\_typography.scss", 
    "..\base\_animations.scss", 
    "..\state\_states.scss", 
    "..\themes\_theme.scss"
)

foreach ($file in $baseFiles) {
    if (-not (Test-Path $file)) {
        Write-Host "Creating placeholder for $file..."
        $directory = Split-Path -Parent $file
        
        if (-not (Test-Path $directory)) {
            New-Item -ItemType Directory -Force -Path $directory | Out-Null
        }
        
        Set-Content -Path $file -Value "/* This file was automatically created by the BEM test setup script */"
    }
}

# Create index files for each directory
$indexFiles = @(
    "..\base\_index.scss",
    "..\layout\_index.scss",
    "..\state\_index.scss",
    "..\themes\_index.scss"
)

foreach ($file in $indexFiles) {
    if (-not (Test-Path $file)) {
        Write-Host "Creating index file $file..."
        $directory = Split-Path -Parent $file
        $baseName = Split-Path -Leaf $directory
        
        $content = "/**
 * $baseName Index - BEM Implementation
 * 
 * This file forwards all $baseName files to be imported by main.scss.
 */

"
        
        # Get all SCSS files in the directory
        $scssFiles = Get-ChildItem -Path $directory -Filter "*.scss" | Where-Object { $_.Name -ne "_index.scss" }
        foreach ($scssFile in $scssFiles) {
            $fileName = $scssFile.Name -replace "^_" -replace "\.scss$", ""
            $content += "@forward '$fileName';`n"
        }
        
        Set-Content -Path $file -Value $content
    }
}

# Compile the BEM SCSS to CSS
Write-Host "Compiling BEM SCSS..."
php compile-bem.php

Write-Host "========================================="
Write-Host "Setup complete!"
Write-Host "========================================="
Write-Host ""
Write-Host "You can now test the BEM implementation by:"
Write-Host "1. Opening style-tester.php in your browser"
Write-Host "2. Using the Visual Diff tool to compare the original and BEM styles"
Write-Host ""
Write-Host "Example URL: http://localhost/your_site_path/assets/scss-bem/tools/style-tester.php"
Write-Host "=========================================" 