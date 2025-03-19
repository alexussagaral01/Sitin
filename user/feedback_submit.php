<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

// Get user's IDNO from users table
$stmt = $conn->prepare("SELECT IDNO FROM users WHERE STUD_NUM = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

// Insert feedback
$stmt = $conn->prepare("INSERT INTO feedback (IDNO, LABORATORY, DATE, FEEDBACK) VALUES (?, ?, CURRENT_DATE(), ?)");
$stmt->bind_param("iss", $user['IDNO'], $_POST['laboratory'], $_POST['feedback']);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

$stmt->close();
$conn->close();
