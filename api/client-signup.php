<?php
require_once '../config/database.php';
session_start();
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate reCaptcha
        $recaptcha_response = $data['recaptcha_token'] ?? '';
        $secret_key = '6LdjTTIrAAAAABqMWpLh2ZM2BPq1F9MJRnk0DARF';
        $verify_url = "https://www.google.com/recaptcha/api/siteverify?secret=$secret_key&response=$recaptcha_response";
        
        $recaptcha_result = file_get_contents($verify_url);
        $recaptcha_data = json_decode($recaptcha_result, true);
        
        if (empty($recaptcha_response) || intval($recaptcha_data["success"]) !== 1) {
            $response['message'] = 'reCaptcha verification failed. Please complete the reCAPTCHA and try again.';
            error_log("Signup failed - reCaptcha verification failed");
            echo json_encode($response);
            exit();
        }
        
        $username = trim($data['username'] ?? '');
        $first_name = trim($data['first_name'] ?? '');
        $last_name = trim($data['last_name'] ?? '');
        $email = trim($data['email'] ?? '');
        $contact_number = trim($data['contact_number'] ?? '');
        $password = $data['password'] ?? '';
        $confirm_password = $data['confirm_password'] ?? '';
        
        // Validation
        if (!$username || !$first_name || !$last_name || !$email || !$contact_number || !$password || !$confirm_password) {
            $response['message'] = "All fields are required.";
            echo json_encode($response);
            exit();
        }
        
        if ($password !== $confirm_password) {
            $response['message'] = "Passwords do not match.";
            echo json_encode($response);
            exit();
        }
        
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        // Check for unique username/email
        $query = "SELECT uid FROM users WHERE username = ? OR email = ? LIMIT 1";
        $stmt = $db->prepareAndExecute($query, "ss", [$username, $email]);
        
        if ($stmt) {
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $response['message'] = "Username or email already exists.";
                echo json_encode($response);
                exit();
            } else {
                // Hash password and insert
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $insert = "INSERT INTO users (username, first_name, last_name, email, contact_number, password) VALUES (?, ?, ?, ?, ?, ?)";
                $insert_stmt = $db->prepareAndExecute($insert, "ssssss", [$username, $first_name, $last_name, $email, $contact_number, $hashed_password]);
                
                if ($insert_stmt) {
                    $response['success'] = true;
                    $response['message'] = "Account created successfully! You can now log in.";
                } else {
                    $response['message'] = "Failed to create account. Please try again.";
                }
            }
            $stmt->close();
        } else {
            $response['message'] = "Database error. Please try again later.";
        }
    } catch (Exception $e) {
        $response['message'] = "An error occurred. Please try again later.";
        error_log("Signup Error: " . $e->getMessage());
    } finally {
        if (isset($db)) {
            $db->closeConnection();
        }
    }
} else {
    $response['message'] = 'Invalid request method';
}

echo json_encode($response);
?>
