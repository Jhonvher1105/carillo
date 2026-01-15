<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in as admin
if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

// Check if date parameter is present
if (!isset($_GET['date'])) {
    echo json_encode(['error' => 'Date parameter is required']);
    exit();
}

$date = $_GET['date'];

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // Get appointments for the specified date - only future dates
    $stmt = $conn->prepare("SELECT consultation_id, preferred_date, full_name 
                           FROM consultations 
                           WHERE DATE(preferred_date) = ? 
                           AND status = 'pending'
                           AND preferred_date >= CURDATE()");
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();

    $appointments = [];
    while ($row = $result->fetch_assoc()) {
        $datetime = new DateTime($row['preferred_date']);
        $appointments[] = [
            'id' => $row['consultation_id'],
            'name' => $row['full_name'],
            'time' => $datetime->format('H:i')
        ];
    }

    echo json_encode($appointments);
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?> 