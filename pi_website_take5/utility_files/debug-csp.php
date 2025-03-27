<?php
/**
 * Standalone CSP Debug File
 * 
 * This file can be accessed directly to test Content Security Policy implementation.
 * Access this page using: https://localhost/debug-csp.php
 */

// Set error reporting to help diagnose issues
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define a minimal function for testing CSP if the utility isn't available
if (!function_exists('applyCSPReportOnly')) {
    function applyCSPReportOnly() {
        if (headers_sent()) {
            return;
        }
        
        // Define CSP directives (same as production but in report-only mode)
        $csp = "default-src 'self'; ";
        $csp .= "script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://unpkg.com; ";
        $csp .= "style-src 'self' 'unsafe-inline' https://unpkg.com; ";
        $csp .= "img-src 'self' data:; ";
        $csp .= "font-src 'self'; ";
        $csp .= "connect-src 'self'; ";
        $csp .= "frame-src 'self'; ";
        $csp .= "worker-src 'self'; ";
        $csp .= "manifest-src 'self'; ";
        $csp .= "form-action 'self'; ";
        $csp .= "frame-ancestors 'self'; ";
        $csp .= "base-uri 'self'; ";
        $csp .= "upgrade-insecure-requests;";
        $csp .= "connect-src 'self' https://jsonplaceholder.typicode.com;";
        
        // Set report-only header (won't block anything, just reports violations)
        header("Content-Security-Policy-Report-Only: $csp");
    }
}

// Apply CSP in report-only mode
applyCSPReportOnly();

// Set other security headers
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("X-Frame-Options: SAMEORIGIN");
header("Referrer-Policy: strict-origin-when-cross-origin");

// Basic CSS for styling
$css = <<<CSS
body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    color: #333;
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}
h1, h2, h3 {
    color: #0066cc;
}
.container {
    background-color: #f8f9fa;
    border-radius: 5px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.debug-box {
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 3px;
    padding: 15px;
    margin-bottom: 15px;
}
ul {
    padding-left: 20px;
}
li {
    margin-bottom: 5px;
}
.test-result {
    min-height: 20px;
    margin-top: 10px;
    font-weight: bold;
}
button {
    background-color: #0066cc;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 4px;
    cursor: pointer;
}
button:hover {
    background-color: #0055aa;
}
.card {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px;
    margin-bottom: 15px;
    background-color: #fff;
}
CSS;

// Get current headers
$currentHeaders = headers_list();
$cspHeader = '';
foreach ($currentHeaders as $header) {
    if (strpos($header, 'Content-Security-Policy') === 0) {
        $cspHeader = $header;
        break;
    }
}

// Parse CSP directives for display
$cspDirectives = [];
if (!empty($cspHeader)) {
    $parts = explode(':', $cspHeader, 2);
    if (count($parts) > 1) {
        $directives = explode(';', trim($parts[1]));
        foreach ($directives as $directive) {
            if (!empty(trim($directive))) {
                $cspDirectives[] = trim($directive);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSP Debug Page</title>
    <style><?php echo $css; ?></style>
</head>
<body>
    <h1>Content Security Policy Debug Page</h1>
    <p>This standalone page tests the Content Security Policy implementation.</p>
    
    <div class="container">
        <h2>Current CSP Directives</h2>
        <div class="debug-box">
            <?php if (!empty($cspDirectives)): ?>
                <ul>
                    <?php foreach ($cspDirectives as $directive): ?>
                        <li><?php echo htmlspecialchars($directive); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No CSP headers found. Check if headers are being sent correctly.</p>
            <?php endif; ?>
        </div>
        
        <h2>All Current Headers</h2>
        <div class="debug-box">
            <ul>
                <?php foreach ($currentHeaders as $header): ?>
                    <li><?php echo htmlspecialchars($header); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    
    <div class="container">
        <h2>CSP Tests</h2>
        
        <div class="card">
            <h3>Inline Script Test</h3>
            <button id="test-inline-script">Run Inline Script Test</button>
            <div id="inline-script-result" class="test-result"></div>
            <script>
                // This script tests if inline scripts are allowed
                document.getElementById('test-inline-script').addEventListener('click', function() {
                    document.getElementById('inline-script-result').textContent = 'Inline script executed successfully!';
                    document.getElementById('inline-script-result').style.color = 'green';
                });
            </script>
        </div>
        
        <div class="card">
            <h3>External Resource Test</h3>
            <button id="test-external-resource">Test External Resource</button>
            <div id="external-resource-result" class="test-result"></div>
            <script>
                // This script tests if external resources can be loaded
                document.getElementById('test-external-resource').addEventListener('click', function() {
                    const resultEl = document.getElementById('external-resource-result');
                    resultEl.textContent = 'Testing...';
                    
                    fetch('https://jsonplaceholder.typicode.com/todos/1')
                        .then(response => {
                            if (response.ok) {
                                resultEl.textContent = 'External resource loaded successfully!';
                                resultEl.style.color = 'green';
                            } else {
                                resultEl.textContent = 'External resource load failed: ' + response.status;
                                resultEl.style.color = 'red';
                            }
                        })
                        .catch(error => {
                            resultEl.textContent = 'External resource blocked: ' + error.message;
                            resultEl.style.color = 'red';
                        });
                });
            </script>
        </div>
        
        <div class="card">
            <h3>Inline Style Test</h3>
            <button id="test-inline-style">Test Inline Style</button>
            <div id="inline-style-result" class="test-result"></div>
            <script>
                // This script tests if inline styles are allowed
                document.getElementById('test-inline-style').addEventListener('click', function() {
                    const resultEl = document.getElementById('inline-style-result');
                    try {
                        resultEl.setAttribute('style', 'color: green; font-weight: bold;');
                        resultEl.textContent = 'Inline style applied successfully!';
                    } catch (error) {
                        resultEl.textContent = 'Inline style blocked: ' + error.message;
                        resultEl.style.color = 'red';
                    }
                });
            </script>
        </div>
        
        <div class="card">
            <h3>External Script Test</h3>
            <p>Testing loading a script from cdnjs (should be allowed by CSP):</p>
            <div id="external-script-result" class="test-result">Not tested yet</div>
            <button id="load-external-script">Load External Script</button>
            <script>
                document.getElementById('load-external-script').addEventListener('click', function() {
                    const script = document.createElement('script');
                    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js';
                    script.onload = function() {
                        // If moment.js loads successfully, it will be defined
                        if (typeof moment !== 'undefined') {
                            document.getElementById('external-script-result').textContent = 
                                'External script loaded successfully! Current time: ' + 
                                moment().format('MMMM Do YYYY, h:mm:ss a');
                            document.getElementById('external-script-result').style.color = 'green';
                        }
                    };
                    script.onerror = function() {
                        document.getElementById('external-script-result').textContent = 
                            'Failed to load external script!';
                        document.getElementById('external-script-result').style.color = 'red';
                    };
                    document.head.appendChild(script);
                });
            </script>
        </div>
    </div>
    
    <div class="container">
        <h2>Help Information</h2>
        <p>If you're experiencing issues with the CSP implementation:</p>
        <ol>
            <li>Check that .htaccess is properly enabled in your Apache configuration</li>
            <li>Verify that mod_headers is enabled in Apache</li>
            <li>Make sure headers aren't already being sent before the CSP headers are applied</li>
            <li>Look for any console errors in your browser's developer tools</li>
        </ol>
        <p>This page is using the Content-Security-Policy-Report-Only header, which will report violations but not block content.</p>
    </div>
    
    <footer>
        <p>&copy; <?php echo date('Y'); ?> PI Group - CSP Debug Tool</p>
    </footer>
</body>
</html>