<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['dept_name'])) {
    $mode = $_POST['mode'];
    
    // REQUIREMENT: Save Department as UPPERCASE
    $dept_name = strtoupper(trim($_POST['dept_name']));

    if (!empty($dept_name)) {
        try {
            // Check if department exists (case-insensitive check)
            $check = $pdo->prepare("SELECT id FROM departments WHERE UPPER(dept_name) = ?");
            $check->execute([$dept_name]);
            $existing = $check->fetch();

            if ($existing) {
                // If it exists, just proceed with the existing ID
                $new_id = $existing['id'];
            } else {
                // Insert new department
                $sql = "INSERT INTO departments (dept_name) VALUES (?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$dept_name]);
                $new_id = $pdo->lastInsertId();
            }

            // JOURNEY FLOW: Automatically proceed to Step 2 (Class Selection)
            header("Location: select_class.php?mode=$mode&dept_id=$new_id");
            exit();

        } catch (PDOException $e) {
            die("Database Error: " . $e->getMessage());
        }
    }
} else {
    header("Location: select_dept.php");
    exit();
}