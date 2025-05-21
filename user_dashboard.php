<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include 'config/db.php';

$userId = $_SESSION['user_id'];

$sql = "SELECT date, time, status, rejection_reason AS reason FROM bookings WHERE user_id = :user_id ORDER BY date DESC, time ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $userId]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="assets/css/user.css">
</head>
<body>
    <header>
        <h1>User Dashboard</h1>
        <a href="booking.php">Book a Slot</a> |
        <a href="logout.php">Logout</a>
    </header>

    <main>
        <section>
            <h2>Your Booking History</h2>
            <?php if (empty($bookings)): ?>
                <p>You haven't made any bookings yet.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Reason (if rejected)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($booking['date']); ?></td>
                                <td><?php echo htmlspecialchars($booking['time']); ?></td>
                                <td><?php echo ucfirst(htmlspecialchars($booking['status'])); ?></td>
                                <td>
                                    <?php 
                                        echo $booking['status'] === 'rejected' 
                                            ? htmlspecialchars($booking['reason']) 
                                            : '-'; 
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
