<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['unit_name'])) {
    $mode = $_POST['mode'];
    $dept_id = $_POST['dept_id'];
    $class_id = $_POST['class_id'];

    // REQUIREMENT 1: Unit Name in Title Case
    $raw_name = trim($_POST['unit_name']);
    $unit_name = mb_convert_case($raw_name, MB_CASE_TITLE, "UTF-8");

    // REQUIREMENT 2: Unit Code in UPPERCASE
    $unit_code = strtoupper(trim($_POST['unit_code']));

    if (!empty($unit_name) && !empty($unit_code)) {
        try {
            // Check if unit code exists in this class
            $check = $pdo->prepare("SELECT id FROM units WHERE unit_code = ? AND class_id = ?");
            $check->execute([$unit_code, $class_id]);
            $existing = $check->fetch();

            if ($existing) {
                $unit_id = $existing['id'];
            } else {
                $sql = "INSERT INTO units (unit_name, unit_code, class_id) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$unit_name, $unit_code, $class_id]);
                $unit_id = $pdo->lastInsertId();
            }

            // JOURNEY FLOW: Proceed to Step 4 (Student Selection/Registration)
            header("Location: manage_students.php?mode=$mode&dept_id=$dept_id&class_id=$class_id&unit_id=$unit_id");
            exit();

        } catch (PDOException $e) {
            die("Database Error: " . $e->getMessage());
        }
    }
} else {
    header("Location: select_dept.php");
    exit();
}