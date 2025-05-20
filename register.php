<?php
include 'config/db.php';

$error = $success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    if (empty($username) || empty($email) || empty($password) || empty($role)) {
        $error = "All fields are required.";
    } else {
        $sql = "SELECT user_id FROM users WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        if ($stmt->rowCount() > 0) {
            $error = "Email already registered.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => $hashedPassword,
                'role' => $role
            ]);

            if ($result) {
                header("Location: login.php");
                exit();
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="assets/css/register.css">
</head>
<body>
    <div class="container">
        <h1>User Registration</h1>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="user">User</option>
                <!-- <option value="admin">Admin</option> -->
            </select>
            
            <button type="submit">Register</button>
        </form>
    </div>
    <script src="assets/js/script.js"></script>
</body>

</html>
