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
require_once __DIR__ . '/src/config/config.php';

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
    // Load the PHPMailer library
    require_once __DIR__ . '/vendor/autoload.php';
    
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
                    
                default:
                    // For other fields, sanitize by removing special chars and limiting length
                    $sanitizedData[$field] = htmlspecialchars(
                        substr(trim($_POST[$field]), 0, 1000),
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    break;
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
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USER;
            $mail->Password = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = SMTP_PORT;
            $mail->CharSet = 'UTF-8';
            
            // Set who the message is from
            $mail->setFrom(SMTP_USER, 'PI Website Contact Form');
            
            // Add a recipient
            $mail->addAddress(SMTP_USER, 'PI Group');
            
            // Add reply-to address (the sender's email)
            $mail->addReplyTo($sanitizedData['email'], $sanitizedData['name']);
            
            // Set email subject and body
            $mail->isHTML(true);
            $mail->Subject = '[Contact Form] ' . $sanitizedData['subject'];
            
            // Prepare email body
            $mailBody = "
            <!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    h2 { color: #003366; }
                    .info { margin-bottom: 20px; }
                    .label { font-weight: bold; }
                    .message { background-color: #f5f5f5; padding: 15px; border-left: 4px solid #003366; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <h2>New Contact Form Submission</h2>
                    <div class='info'>
                        <p><span class='label'>Name:</span> {$sanitizedData['name']}</p>
                        <p><span class='label'>Email:</span> {$sanitizedData['email']}</p>
                        <p><span class='label'>Subject:</span> {$sanitizedData['subject']}</p>
                    </div>
                    <div class='message'>
                        <p><span class='label'>Message:</span></p>
                        <p>" . nl2br($sanitizedData['message']) . "</p>
                    </div>
                </div>
            </body>
            </html>
            ";
            
            $mail->Body = $mailBody;
            $mail->AltBody = strip_tags(str_replace('<br>', "\n", $mailBody));
            
            // Send the email
            $mail->send();
            
            // Set success response
            $response['success'] = true;
            $response['message'] = 'Thank you for your message. We will contact you shortly.';
            
            // Optional: send confirmation email to the user
            sendConfirmationEmail($sanitizedData, $lang);
            
        } catch (Exception $e) {
            // Log the error (to error log file rather than displaying to user)
            error_log("Error sending email: {$mail->ErrorInfo}");
            $response['message'] = 'Sorry, there was an error sending your message. Please try again later.';
        }
    } else {
        // If there are validation errors, include them in the response
        $response['errors'] = $validationErrors;
        $response['message'] = 'Please correct the errors in the form.';
    }
}

// Store response in session for display after redirect
$_SESSION['contact_form_response'] = $response;

// Redirect back to contact page
header("Location: " . BASE_URL . "/contact" . ($response['success'] ? '?success=1' : '?error=1'));
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