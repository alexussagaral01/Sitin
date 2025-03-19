<?php
session_start();
require '../db.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (!isset($_POST['feedback_id'])) {
    echo json_encode(['success' => false, 'message' => 'No feedback ID provided']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM feedback WHERE FEEDBACK_ID = ?");
$stmt->bind_param("i", $_POST['feedback_id']);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

$stmt->close();
$conn->close();
