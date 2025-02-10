<?php
include 'config/db.php';

$sql = "SELECT b.date, b.time, u.username, b.status FROM bookings b 
        JOIN users u ON b.user_id = u.user_id 
        WHERE b.status IN ('pending', 'approved') 
        ORDER BY b.date, b.time";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$bookedSlots = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booked Slots</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
                <li><a href="booking.php">Book Now</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h1>Booked Slots</h1>
        <div id="calendar">
            <?php if ($bookedSlots): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Booked By</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookedSlots as $slot): ?>
                            <tr>
                                <td><?= htmlspecialchars($slot['date']) ?></td>
                                <td><?= htmlspecialchars($slot['time']) ?></td>
                                <td><?= htmlspecialchars($slot['username']) ?></td>
                                <td class="<?= ($slot['status'] == 'approved') ? 'unavailable' : 'pending' ?>">
                                    <?= htmlspecialchars($slot['status']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No booked slots yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Booking Website. All rights reserved.</p>
    </footer>
</body>

</html>
