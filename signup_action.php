<?php
require 'db.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inst  = $_POST['institution'];
    $name  = $_POST['teacher_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $pass  = $_POST['password'];
    $conf  = $_POST['confirm_password'];

    // 1. Password Match Check
    if ($pass !== $conf) {
        header("Location: signup.php?error=pass_mismatch");
        exit();
    }

    // 2. Hash Password
    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

    try {
        // 3. Insert into Database
        $sql = "INSERT INTO users (institution_name, teacher_name, email, phone, password) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$inst, $name, $email, $phone, $hashed_pass]);

        // 4. Success Redirect
        header("Location: login.php?msg=account_created");
        exit();

    } catch (PDOException $e) {
        // Handle Duplicate Email (PostgreSQL Error Code 23505)
        if ($e->getCode() == 23505) {
            header("Location: signup.php?error=email_exists");
            exit();
        } else {
            die("Critical Database Error: " . $e->getMessage());
        }
    }
} else {
    // Redirect back if page is accessed directly
    header("Location: signup.php");
    exit();
}
?>