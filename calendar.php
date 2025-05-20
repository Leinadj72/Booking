<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['bookedSlots' => [], 'currentUserId' => null]);
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT booking_id, user_id, date, time, status FROM bookings WHERE status IN ('pending', 'approved')";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$bookedSlots = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "bookedSlots" => $bookedSlots,
    "currentUserId" => $user_id
]);
?>
