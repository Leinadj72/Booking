<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT id, user_id, date, time FROM bookings WHERE status IN ('pending', 'approved')";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["bookedSlots" => $bookings, "currentUserId" => $user_id]);
?>
