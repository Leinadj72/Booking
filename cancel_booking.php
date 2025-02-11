<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);
$date = $data['date'];
$time = $data['time'];

$sql = "DELETE FROM bookings WHERE user_id = ? AND date = ? AND time = ?";
$stmt = $pdo->prepare($sql);
$success = $stmt->execute([$user_id, $date, $time]);

if ($success) {
    echo json_encode(['success' => true, 'message' => 'Booking canceled.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to cancel booking.']);
}
?>
