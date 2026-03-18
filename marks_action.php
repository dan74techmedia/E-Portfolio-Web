<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $unit_id = $_POST['unit_id'];
    $class_id = $_POST['class_id'];
    $student_ids = $_POST['student_ids'];

    try {
        $pdo->beginTransaction(); 

        foreach ($student_ids as $index => $s_id) {
            // Mapping the incoming array data to specific variables
            $c1 = $_POST['cat1'][$index] ?? 0;
            $c2 = $_POST['cat2'][$index] ?? 0;
            $c3 = $_POST['cat3'][$index] ?? 0; // This is the "End Term"
            
            $h1 = $_POST['chk1'][$index] ?? 0;
            $h2 = $_POST['chk2'][$index] ?? 0;
            $h3 = $_POST['chk3'][$index] ?? 0;

            // Normalize comments to Title Case for a professional report look
            $raw_com = trim($_POST['comment'][$index] ?? '');
            $com = mb_convert_case($raw_com, MB_CASE_TITLE, "UTF-8");

            // PostgreSQL 'Upsert' logic: 
            // It tries to insert, but if the (student_id + unit_id) combo exists, 
            // it updates the existing row instead.
            $sql = "INSERT INTO marks (student_id, unit_id, cat1, cat2, cat3, chk1, chk2, chk3, comment) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ON CONFLICT (student_id, unit_id) 
                    DO UPDATE SET 
                        cat1 = EXCLUDED.cat1, 
                        cat2 = EXCLUDED.cat2, 
                        cat3 = EXCLUDED.cat3, 
                        chk1 = EXCLUDED.chk1, 
                        chk2 = EXCLUDED.chk2, 
                        chk3 = EXCLUDED.chk3, 
                        comment = EXCLUDED.comment";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$s_id, $unit_id, $c1, $c2, $c3, $h1, $h2, $h3, $com]);
        }
        
        $pdo->commit();
        
        // Redirect to the class analysis view with a success message
        header("Location: view_marks.php?class_id=$class_id&unit_id=$unit_id&msg=saved");
        exit();

    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Critical Error: Could not save assessment data. " . $e->getMessage());
    }
} else {
    header("Location: dashboard.php");
    exit();
}