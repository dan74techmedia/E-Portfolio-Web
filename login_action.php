<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $pass = $_POST['password'];

    try {
        // Find the user by email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify password
        if ($user && password_verify($pass, $user['password'])) {
            // SUCCESS: Initialize the "Session Memory"
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['teacher_name'] = $user['teacher_name'];
            $_SESSION['institution'] = $user['institution_name'];

            // Direct to the Action Center
            header("Location: dashboard.php");
            exit();
        } else {
            // FAIL: Back to login with error code
            header("Location: login.php?error=invalid");
            exit();
        }

    } catch (PDOException $e) {
        // Crash protection
        die("System Connection Error: " . $e->getMessage());
    }
} else {
    header("Location: login.php");
    exit();
}
?>