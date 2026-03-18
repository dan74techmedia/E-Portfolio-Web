<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['class_name'])) {
    $mode = $_POST['mode'];
    $dept_id = $_POST['dept_id'];
    $teacher_id = $_SESSION['user_id'];

    // REQUIREMENT: Save Class Name as Title Case
    // We trim first, then convert to Title Case
    $raw_name = trim($_POST['class_name']);
    $class_name = mb_convert_case($raw_name, MB_CASE_TITLE, "UTF-8");

    if (!empty($class_name) && !empty($dept_id)) {
        try {
            // Check if class already exists for this teacher/dept to prevent duplicates
            $check = $pdo->prepare("SELECT id FROM classes WHERE class_name = ? AND dept_id = ? AND teacher_id = ?");
            $check->execute([$class_name, $dept_id, $teacher_id]);
            $existing = $check->fetch();

            if ($existing) {
                $class_id = $existing['id'];
            } else {
                // Insert into Neon
                $sql = "INSERT INTO classes (class_name, dept_id, teacher_id) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$class_name, $dept_id, $teacher_id]);
                $class_id = $pdo->lastInsertId();
            }

            // JOURNEY FLOW: Automatically proceed to Step 3 (Unit Selection)
            header("Location: select_unit.php?mode=$mode&dept_id=$dept_id&class_id=$class_id");
            exit();

        } catch (PDOException $e) {
            die("Database Error: " . $e->getMessage());
        }
    }
} else {
    header("Location: select_dept.php");
    exit();
}