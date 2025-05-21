<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

include 'config/db.php';

ini_set('display_errors', 0);
ini_set('log_errors', 1); 

try {
    $sql = "SELECT bookings.booking_id, users.username, bookings.date, bookings.time
            FROM bookings
            JOIN users ON bookings.user_id = users.user_id
            WHERE bookings.status = 'pending'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $pendingBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('SQL Error: ' . $e->getMessage());
    $pendingBookings = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingId = $_POST['booking_id'];
    $action = $_POST['action'];
    $reason = $_POST['reason'] ?? null;

    if ($action === 'approve') {
        $sql = "UPDATE bookings SET status = 'approved' WHERE booking_id = :id";
    } elseif ($action === 'reject') {
        $sql = "UPDATE bookings SET status = 'rejected', rejection_reason = :reason WHERE booking_id = :id";
    }

    $stmt = $pdo->prepare($sql);
    $params = ['id' => $bookingId];
    if ($action === 'reject') {
        $params['reason'] = $reason;
    }
    $stmt->execute($params);

    header('Location: admin_dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
        <a href="logout.php">Logout</a>
    </header>

    <main>
        <section>
            <h2>Pending Booking Requests</h2>
            <?php if (empty($pendingBookings)): ?>
                <p>No pending bookings at the moment.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingBookings as $booking): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($booking['username']); ?></td>
                                <td><?php echo htmlspecialchars($booking['date']); ?></td>
                                <td><?php echo htmlspecialchars($booking['time']); ?></td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                        <button type="submit" name="action" value="approve">Approve</button>
                                        <button type="submit" name="action" value="reject">Reject</button>
                                        <textarea name="reason" placeholder="Reason for rejection (optional)"></textarea>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const forms = document.querySelectorALL('form');

            forms.forEach(form => {
                form.addEventListener('submit', (e) => {
                    const action = e.submitter?.value;
                    const reason = form.querySelector('textarea[name="reason]');

                    if (action === 'reject' && reason && reason.value.trim() === '') {
                        e.preventDefault();
                        alert('Please provide a reason for rejecting the booking.');
                        reason.focus();
                    }
                })
            })
        })
    </script>
</body>
</html>
