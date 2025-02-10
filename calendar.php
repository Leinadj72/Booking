<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include 'config/db.php';

$sql = "SELECT date, time FROM bookings WHERE status = 'approved'";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$bookedSlots = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($bookedSlots);
exit;
?>
