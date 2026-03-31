<?php
// Set headers to accept JSON from our fetch request
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Get the raw POST data
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

// Check if data exists
if (isset($data['name']) && isset($data['email'])) {
    
    // Sanitize the inputs
    $name = htmlspecialchars(strip_tags(trim($data['name'])));
    $email = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);

    // Validate the email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format.']);
        exit;
    }

    // --- SAVE DATA ---
    // Here we will save it to a simple text file. 
    // In a real production environment, you would insert this into a MySQL database or send it to an API like Mailchimp.
    
    $file = 'subscribers.txt';
    $timestamp = date("Y-m-d H:i:s");
    $record = "{$timestamp} | Name: {$name} | Email: {$email}\n";

    // Append the new subscriber to the file
    if (file_put_contents($file, $record, FILE_APPEND | LOCK_EX)) {
        echo json_encode(['status' => 'success', 'message' => 'Successfully subscribed!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to save subscription.']);
    }

} else {
    // If the required fields are missing
    echo json_encode(['status' => 'error', 'message' => 'Please fill in all fields.']);
}
?>