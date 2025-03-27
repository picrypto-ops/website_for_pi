<?php
/**
 * Process Contact Form
 * Handles the contact form submission and email sending
 * 
 * @package PI-Website
 */

// Configure error reporting based on debug mode
if (defined('PHP_DEBUG_MODE') && PHP_DEBUG_MODE === true) {
    // Debug mode: Show all errors and log verbose information
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    // Enable detailed logging
    $detailed_logging = true;
} else {
    // Production mode: Only log critical errors, don't display them
    ini_set('display_errors', 0);
    error_reporting(E_ERROR | E_PARSE);
    // Disable detailed logging
    $detailed_logging = false;
}

// Always log errors to a file regardless of debug mode
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/contact_form_errors.log');

// Start error logging if in debug mode
if ($detailed_logging) {
    error_log("Contact form processing started");
}

try {
    // Include the configuration file
    require_once __DIR__ . '/../config/config.php';
    
    if ($detailed_logging) {
        error_log("Config loaded");
    }

    // Include utility functions
    require_once __DIR__ . '/../utility/functions.php';
    
    if ($detailed_logging) {
        error_log("Utility functions loaded");
    }

    // Check if vendor directory exists
    if (!file_exists(__DIR__ . '/../../../vendor')) {
        // Critical error, log it but don't expose details
        error_log("Vendor directory not found");
    }

    // Flag to track if PHPMailer is available
    $phpmailer_available = false;

    // Try to load PHPMailer
    try {
        // Check if autoload file exists
        if (!file_exists(__DIR__ . '/../../../vendor/autoload.php')) {
            throw new \Exception("Autoload file not found");
        }
        
        require_once __DIR__ . '/../../../vendor/autoload.php';
        
        // Check if PHPMailer classes exist
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            throw new \Exception("PHPMailer class not found");
        }
        
        $phpmailer_available = true;
    } catch (\Exception $e) {
        error_log("PHPMailer loading error: " . $e->getMessage());
        $phpmailer_available = false;
    }

    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Get current language
    $lang = $_SESSION['lang'] ?? DEFAULT_LANG;

    // Initialize variables for response
    $response = [
        'success' => false,
        'message' => '',
        'errors' => []
    ];

    // Process only POST requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($detailed_logging) {
            error_log("Processing POST request");
        }
        
        // First check CSRF token
        if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
            $response['message'] = 'Invalid security token. Please reload the page and try again.';
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        // Define expected fields
        $expectedFields = ['name', 'email', 'subject', 'message'];
        
        // Sanitize inputs and validate required fields
        $sanitizedData = [];
        $validationErrors = [];
        
        foreach ($expectedFields as $field) {
            if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
                $validationErrors[$field] = "The {$field} field is required.";
            } else {
                // Sanitize the input based on field type
                switch ($field) {
                    case 'email':
                        $email = filter_var(trim($_POST[$field]), FILTER_SANITIZE_EMAIL);
                        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $validationErrors[$field] = "Please enter a valid email address.";
                        } else {
                            $sanitizedData[$field] = $email;
                        }
                        break;
                    
                    case 'name':
                        // Name should contain only letters, spaces, and limited special characters
                        $name = trim($_POST[$field]);
                        if (!preg_match('/^[A-Za-z\s\'-]{2,50}$/', $name)) {
                            $validationErrors[$field] = "Please enter a valid name (2-50 characters).";
                        } else {
                            $sanitizedData[$field] = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
                        }
                        break;
                    
                    case 'subject':
                        // Subject should be between 3-100 characters
                        $subject = trim($_POST[$field]);
                        if (strlen($subject) < 3 || strlen($subject) > 100) {
                            $validationErrors[$field] = "Subject must be between 3 and 100 characters.";
                        } else {
                            $sanitizedData[$field] = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
                        }
                        break;
                    
                    case 'message':
                        // Message should be between 10-1000 characters
                        $message = trim($_POST[$field]);
                        if (strlen($message) < 10 || strlen($message) > 1000) {
                            $validationErrors[$field] = "Message must be between 10 and 1000 characters.";
                        } else {
                            $sanitizedData[$field] = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
                        }
                        break;
                }
            }
        }
        
        // Process only if no validation errors
        if (empty($validationErrors)) {
            // Setup debug information (minimal for production)
            $debug_info = [
                'smtp_host' => SMTP_HOST,
                'smtp_port' => SMTP_PORT,
                'phpmailer_available' => $phpmailer_available
            ];
            
            if ($phpmailer_available) {
                try {
                    // Create a new PHPMailer instance with fully qualified name
                    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                    
                    // Server settings
                    $mail->SMTPDebug = 0;                      // Disable verbose debug output
                    $mail->isSMTP();                           // Send using SMTP
                    $mail->Host       = SMTP_HOST;             // Set the SMTP server to send through
                    $mail->Port       = SMTP_PORT;             // TCP port to connect to, use 587 for TLS
                    $mail->SMTPSecure = false;                 // No encryption for port 25
                    $mail->SMTPAuth   = false;                 // No authentication needed for this SMTP relay
                    
                    // Recipients
                    $mail->setFrom(SMTP_FROM, 'PI Website Contact Form');
                    $mail->addAddress(SMTP_TO);                // Add a recipient
                    $mail->addReplyTo($sanitizedData['email'], $sanitizedData['name']);
                    
                    // Email subject
                    $mail->Subject = '[Contact Form] ' . $sanitizedData['subject'];
                    
                    // Prepare email body
                    $emailBody = "
                    <!DOCTYPE html>
                    <html lang='en'>
                    <head>
                        <meta charset='UTF-8'>
                        <style>
                            body { 
                                font-family: Arial, sans-serif; 
                                line-height: 1.6; 
                                color: #333;
                            }
                            .container { 
                                max-width: 600px; 
                                margin: 0 auto; 
                                padding: 20px;
                            }
                            .header {
                                background-color: #003366;
                                color: #ffffff;
                                padding: 20px;
                                text-align: center;
                            }
                            .content {
                                padding: 20px;
                                background-color: #f9f9f9;
                                border: 1px solid #e0e0e0;
                            }
                            .field {
                                margin-bottom: 15px;
                            }
                            .label {
                                font-weight: bold;
                                color: #003366;
                            }
                            .message {
                                background-color: #ffffff;
                                padding: 15px;
                                border: 1px solid #e0e0e0;
                                margin-top: 20px;
                            }
                            .footer {
                                text-align: center;
                                padding: 20px;
                                color: #666;
                                font-size: 12px;
                                margin-top: 20px;
                            }
                        </style>
                    </head>
                    <body>
                        <div class='container'>
                            <div class='header'>
                                <h2>New Contact Form Submission</h2>
                            </div>
                            <div class='content'>
                                <div class='field'>
                                    <span class='label'>Name:</span> 
                                    {$sanitizedData['name']}
                                </div>
                                <div class='field'>
                                    <span class='label'>Email:</span> 
                                    {$sanitizedData['email']}
                                </div>
                                <div class='field'>
                                    <span class='label'>Subject:</span> 
                                    {$sanitizedData['subject']}
                                </div>
                                <div class='field'>
                                    <span class='label'>Message:</span>
                                    <div class='message'>" . nl2br($sanitizedData['message']) . "</div>
                                </div>
                            </div>
                            <div class='footer'>
                                <p>This email was sent from the PI Website Contact Form</p>
                            </div>
                        </div>
                    </body>
                    </html>
                    ";
                    
                    // Content
                    $mail->isHTML(true);
                    $mail->CharSet = 'UTF-8';
                    $mail->Body    = $emailBody;
                    $mail->AltBody = "Name: {$sanitizedData['name']}\nEmail: {$sanitizedData['email']}\nSubject: {$sanitizedData['subject']}\nMessage: {$sanitizedData['message']}";
                    
                    // Send the email
                    $mail->send();
                    
                    // Email sent successfully
                    $response['success'] = true;
                    $response['message'] = 'Thank you for your message. We will contact you shortly.';
                    
                } catch (\Exception $e) {
                    // Log the error
                    error_log("Error sending email: " . $e->getMessage());
                    $response['message'] = 'Sorry, there was an error sending your message. Please try again later.';
                }
            } else {
                // Fallback to PHP mail() function if PHPMailer is not available
                try {
                    // Prepare email headers and body
                    $headers = "From: " . SMTP_FROM . "\r\n";
                    $headers .= "Reply-To: " . $sanitizedData['email'] . "\r\n";
                    $headers .= "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                    
                    // Email subject
                    $emailSubject = '[Contact Form] ' . $sanitizedData['subject'];
                    
                    // Prepare email body
                    $emailBody = "
                    <!DOCTYPE html>
                    <html lang='en'>
                    <head>
                        <meta charset='UTF-8'>
                        <style>
                            body { 
                                font-family: Arial, sans-serif; 
                                line-height: 1.6; 
                                color: #333;
                            }
                            .container { 
                                max-width: 600px; 
                                margin: 0 auto; 
                                padding: 20px;
                            }
                            .header {
                                background-color: #003366;
                                color: #ffffff;
                                padding: 20px;
                                text-align: center;
                            }
                            .content {
                                padding: 20px;
                                background-color: #f9f9f9;
                                border: 1px solid #e0e0e0;
                            }
                            .field {
                                margin-bottom: 15px;
                            }
                            .label {
                                font-weight: bold;
                                color: #003366;
                            }
                            .message {
                                background-color: #ffffff;
                                padding: 15px;
                                border: 1px solid #e0e0e0;
                                margin-top: 20px;
                            }
                            .footer {
                                text-align: center;
                                padding: 20px;
                                color: #666;
                                font-size: 12px;
                                margin-top: 20px;
                            }
                        </style>
                    </head>
                    <body>
                        <div class='container'>
                            <div class='header'>
                                <h2>New Contact Form Submission</h2>
                            </div>
                            <div class='content'>
                                <div class='field'>
                                    <span class='label'>Name:</span> 
                                    {$sanitizedData['name']}
                                </div>
                                <div class='field'>
                                    <span class='label'>Email:</span> 
                                    {$sanitizedData['email']}
                                </div>
                                <div class='field'>
                                    <span class='label'>Subject:</span> 
                                    {$sanitizedData['subject']}
                                </div>
                                <div class='field'>
                                    <span class='label'>Message:</span>
                                    <div class='message'>" . nl2br($sanitizedData['message']) . "</div>
                                </div>
                            </div>
                            <div class='footer'>
                                <p>This email was sent from the PI Website Contact Form</p>
                            </div>
                        </div>
                    </body>
                    </html>
                    ";
                    
                    // Configure mail
                    ini_set('SMTP', SMTP_HOST);
                    ini_set('smtp_port', SMTP_PORT);
                    
                    // Send email
                    $mailSent = mail(SMTP_TO, $emailSubject, $emailBody, $headers);
                    
                    if ($mailSent) {
                        // Set success response
                        $response['success'] = true;
                        $response['message'] = 'Thank you for your message. We will contact you shortly.';
                    } else {
                        // Email sending failed
                        $response['message'] = 'Sorry, there was an error sending your message. Please try again later.';
                        error_log("Mail sending failed");
                    }
                } catch (\Exception $e) {
                    // Log the error
                    error_log("Error sending email with PHP mail(): " . $e->getMessage());
                    $response['message'] = 'Sorry, there was an error sending your message. Please try again later.';
                }
            }
        } else {
            // If there are validation errors, include them in the response
            $response['errors'] = $validationErrors;
            $response['message'] = 'Please correct the errors in the form.';
        }
    } else {
        $response['message'] = 'Invalid request method.';
    }

    // Always send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
} catch (\Exception $e) {
    // Catch any unexpected errors
    error_log("Unexpected error: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred. Please try again later.',
        'errors' => []
    ]);
    exit;
}