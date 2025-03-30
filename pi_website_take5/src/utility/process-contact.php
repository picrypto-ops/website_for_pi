<?php
/**
 * Process Contact Form
 * Handles the contact form submission and email sending
 * 
 * @package PI-Website
 */

// Import the PHPMailer classes at the top level
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Include the configuration file
require_once __DIR__ . '/../config/config.php';

// Include utility functions
require_once __DIR__ . '/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get current language
$lang = $_SESSION['lang'] ?? DEFAULT_LANG;

// Load contact page data
$contactTranslatable = TranslatableFactory::page('contact');
$contactDetails = $contactTranslatable->getContent($lang, 'contact_details', []);
if (!is_array($contactDetails)) {
    $contactDetails = json_decode($contactDetails, true);
}

// Initialize variables for response
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

// Process only POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // First check CSRF token
    if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
        $response['message'] = 'Invalid security token. Please reload the page and try again.';
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // Load the PHPMailer library
    require_once __DIR__ . '/../vendor/autoload.php';
    
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

    // Verify Google reCAPTCHA if enabled and configured
    if (defined('USE_RECAPTCHA') && USE_RECAPTCHA && 
        defined('RECAPTCHA_SECRET_KEY') && !empty(RECAPTCHA_SECRET_KEY)) {
        if (!isset($_POST['g-recaptcha-response']) || empty($_POST['g-recaptcha-response'])) {
            $validationErrors['recaptcha'] = "Please complete the reCAPTCHA verification.";
        } else {
            $recaptcha = $_POST['g-recaptcha-response'];
            $secretKey = RECAPTCHA_SECRET_KEY;
            
            // Make the verification request to Google
            $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secretKey.'&response='.$recaptcha);
            
            $responseData = json_decode($verifyResponse);
            
            if (!$responseData->success) {
                $validationErrors['recaptcha'] = "reCAPTCHA verification failed. Please try again.";
            }
        }
    }
    
    // Process only if no validation errors
    if (empty($validationErrors)) {
        try {
            // Create a new PHPMailer instance
            $mail = new PHPMailer(true);
            
            // Server settings
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = false; // No authentication required
            $mail->Port = SMTP_PORT;
            $mail->CharSet = 'UTF-8';
            
            // Set who the message is from (using the configured SMTP_FROM)
            $mail->setFrom(SMTP_FROM, 'PI Website Contact Form');
            
            // Add a recipient (using the configured SMTP_TO)
            $mail->addAddress(SMTP_TO, 'PI Group');
            
            // Add reply-to address (the sender's email)
            $mail->addReplyTo($sanitizedData['email'], $sanitizedData['name']);
            
            // Set email subject and body
            $mail->isHTML(true);
            $mail->Subject = '[Contact Form] ' . $sanitizedData['subject'];
            
            // Prepare email body with proper formatting
            $mailBody = "
            <!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <style>
                    body { 
                        font-family: Arial, sans-serif; 
                        line-height: 1.6; 
                        color: #333;
                        margin: 0;
                        padding: 0;
                    }
                    .container { 
                        max-width: 600px; 
                        margin: 0 auto; 
                        padding: 20px;
                        background-color: #ffffff;
                    }
                    .header {
                        background-color: #003366;
                        color: #ffffff;
                        padding: 20px;
                        text-align: center;
                        border-radius: 5px 5px 0 0;
                    }
                    .content {
                        padding: 20px;
                        background-color: #f9f9f9;
                        border: 1px solid #e0e0e0;
                        border-top: none;
                        border-radius: 0 0 5px 5px;
                    }
                    .field {
                        margin-bottom: 15px;
                    }
                    .label {
                        font-weight: bold;
                        color: #003366;
                        display: block;
                        margin-bottom: 5px;
                    }
                    .value {
                        background-color: #ffffff;
                        padding: 10px;
                        border-radius: 4px;
                        border: 1px solid #e0e0e0;
                    }
                    .message {
                        background-color: #ffffff;
                        padding: 15px;
                        border-radius: 4px;
                        border: 1px solid #e0e0e0;
                        margin-top: 20px;
                    }
                    .footer {
                        text-align: center;
                        padding: 20px;
                        color: #666;
                        font-size: 12px;
                        margin-top: 20px;
                        border-top: 1px solid #e0e0e0;
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
                            <div class='value'>{$sanitizedData['name']}</div>
                        </div>
                        <div class='field'>
                            <span class='label'>Email:</span>
                            <div class='value'>{$sanitizedData['email']}</div>
                        </div>
                        <div class='field'>
                            <span class='label'>Subject:</span>
                            <div class='value'>{$sanitizedData['subject']}</div>
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
            
            $mail->Body = $mailBody;
            $mail->AltBody = strip_tags(str_replace('<br>', "\n", $mailBody));
            
            // Send the email
            $mail->send();
            
            // Set success response with debug information
            $response['success'] = true;
            $response['message'] = 'Thank you for your message. We will contact you shortly.';
            $response['debug'] = [
                'smtp_host' => SMTP_HOST,
                'smtp_port' => SMTP_PORT,
                'smtp_auth' => false,
                'from_email' => SMTP_FROM,
                'to_email' => SMTP_TO,
                'reply_to' => $sanitizedData['email']
            ];
            
            // Send confirmation email to the user if enabled
            if (defined('SEND_CONFIRMATION_EMAIL') && SEND_CONFIRMATION_EMAIL) {
                sendConfirmationEmail($sanitizedData, $lang);
            }
            
        } catch (Exception $e) {
            // Log the error and include it in the debug information
            error_log("Error sending email: {$mail->ErrorInfo}");
            $response['message'] = 'Sorry, there was an error sending your message. Please try again later.';
            $response['debug'] = [
                'error' => $mail->ErrorInfo,
                'smtp_host' => SMTP_HOST,
                'smtp_port' => SMTP_PORT,
                'smtp_auth' => false,
                'from_email' => SMTP_FROM,
                'to_email' => SMTP_TO
            ];
        }
    } else {
        // If there are validation errors, include them in the response
        $response['errors'] = $validationErrors;
        $response['message'] = 'Please correct the errors in the form.';
    }
}

// Always send JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit;

/**
 * Send confirmation email to the user
 * 
 * @param array $data Sanitized form data
 * @param string $lang Current language
 * @return void
 */
function sendConfirmationEmail(array $data, string $lang): void
{
    // Only proceed if there's valid user data
    if (empty($data['email']) || empty($data['name'])) {
        return;
    }
    
    try {
        // Load translatable content - assuming TranslatableFactory is available
        require_once __DIR__ . '/src/utility/functions.php';
        
        // Create a new PHPMailer instance
        $mail = new PHPMailer(true);
        
        // Server settings
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;
        $mail->CharSet = 'UTF-8';
        
        // Set who the message is from
        $mail->setFrom(SMTP_USER, 'PI Group');
        
        // Add the recipient (the user)
        $mail->addAddress($data['email'], $data['name']);
        
        // Set email subject and body
        $mail->isHTML(true);
        
        // Get translated content if TranslatableFactory is available
        if (class_exists('TranslatableFactory')) {
            $general = TranslatableFactory::general();
            $subject = $general->getContent($lang, 'contact_confirmation_subject', 'Thank you for contacting us');
            $greeting = $general->getContent($lang, 'contact_confirmation_greeting', 'Dear');
            $thankYou = $general->getContent($lang, 'contact_confirmation_thank_you', 'Thank you for contacting PI Group. We have received your message and will respond as soon as possible.');
            $regards = $general->getContent($lang, 'contact_confirmation_regards', 'Best regards');
            $signature = $general->getContent($lang, 'contact_confirmation_signature', 'PI Group Team');
        } else {
            // Fallback to English
            $subject = 'Thank you for contacting us';
            $greeting = 'Dear';
            $thankYou = 'Thank you for contacting PI Group. We have received your message and will respond as soon as possible.';
            $regards = 'Best regards';
            $signature = 'PI Group Team';
        }
        
        $mail->Subject = $subject;
        
        // Prepare email body
        $mailBody = "
        <!DOCTYPE html>
        <html lang='{$lang}'>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                h2 { color: #003366; }
                .content { margin: 20px 0; }
                .signature { margin-top: 30px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h2>{$subject}</h2>
                <div class='content'>
                    <p>{$greeting} {$data['name']},</p>
                    <p>{$thankYou}</p>
                    <p>{$regards},</p>
                    <p class='signature'>{$signature}</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $mail->Body = $mailBody;
        $mail->AltBody = strip_tags(str_replace('<br>', "\n", $mailBody));
        
        // Send the email
        $mail->send();
        
    } catch (Exception $e) {
        // Log the error but continue execution
        error_log("Error sending confirmation email: {$mail->ErrorInfo}");
    }
} 