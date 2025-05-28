<?php
// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to sanitize input data
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $phone = isset($_POST['phone']) ? sanitize_input($_POST['phone']) : '';
    $message = sanitize_input($_POST['message']);
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
        exit;
    }
    
    // Email recipient
    $to = "sandrastuatt014@gmail.com";
    
    // Email subject
    $subject = "New Contact Form Submission from $name";
    
    // Email headers
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    // Email body
    $email_body = "You have received a new message from your website contact form.\n\n";
    $email_body .= "Name: $name\n";
    $email_body .= "Email: $email\n";
    if ($phone) {
        $email_body .= "Phone: $phone\n";
    }
    $email_body .= "Message:\n$message\n";
    
    // Send email
    if (mail($to, $subject, $email_body, $headers)) {
        // Log successful submission
        $log_file = 'contact_log.txt';
        $log_message = date('Y-m-d H:i:s') . " - Message from $name ($email)\n";
        file_put_contents($log_file, $log_message, FILE_APPEND);
        
        // Send success response
        echo json_encode(['status' => 'success', 'message' => 'Thank you for your message. We will get back to you soon!']);
    } else {
        // Send error response
        echo json_encode(['status' => 'error', 'message' => 'Sorry, there was an error sending your message. Please try again later.']);
    }
} else {
    // If not a POST request, redirect to home page
    header('Location: index.html');
    exit;
}
?> 