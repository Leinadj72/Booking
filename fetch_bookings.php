<?php
include 'config/db.php';

$sql = "SELECT date, time FROM bookings WHERE status IN ('pending', 'approved')";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$bookedSlots = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($bookedSlots);
?>
