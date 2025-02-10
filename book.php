<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to book a slot.']);
    exit;
}

$user_id = $_SESSION['user_id'];

$data = json_decode(file_get_contents("php://input"), true);
$date = $data['date'];
$time = $data['time'];

$sql = "SELECT * FROM bookings WHERE date = ? AND time = ? AND status IN ('pending', 'approved')";
$stmt = $pdo->prepare($sql);
$stmt->execute([$date, $time]);
$existingBooking = $stmt->fetch();

if ($existingBooking) {
    echo json_encode(['success' => false, 'message' => 'This time slot is already booked.']);
    exit;
}

$sql = "SELECT * FROM bookings WHERE user_id = ? AND date = ? AND time = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id, $date, $time]);
$userBooking = $stmt->fetch();

if ($userBooking) {
    echo json_encode(['success' => false, 'message' => 'You have already booked this time slot.']);
    exit;
}

$sql = "INSERT INTO bookings (user_id, date, time, status) VALUES (?, ?, ?, 'pending')";
$stmt = $pdo->prepare($sql);
if ($stmt->execute([$user_id, $date, $time])) {
    echo json_encode(['success' => true, 'message' => 'Booking request submitted successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to book the slot.']);
}
?>
