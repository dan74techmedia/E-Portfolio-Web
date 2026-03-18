<?php
session_start();
require 'db.php';

// Cloudinary Credentials
$cloud_name = "dvxf1l9ee";
$api_key = "987778174599826"; 
$api_secret = "VJzYYf51Ot2OrRSLsnP8b0u6dpA";
$upload_preset = "portfolio_preset"; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Journey parameters
    $mode = $_POST['mode'];
    $dept_id = $_POST['dept_id'];
    $class_id = $_POST['class_id'];
    $unit_id = $_POST['unit_id'];

    // Input Casing Logic
    $raw_name = trim($_POST['full_name']);
    $name = mb_convert_case($raw_name, MB_CASE_TITLE, "UTF-8"); // Title Case
    
    $adm = strtoupper(trim($_POST['admission_no'])); // UPPERCASE
    $reg = strtoupper(trim($_POST['reg_code'])); // UPPERCASE
    
    // Check if file exists
    if (!isset($_FILES['student_photo']) || $_FILES['student_photo']['error'] !== UPLOAD_ERR_OK) {
        die("Please select a valid photo. <a href='javascript:history.back()'>Go back</a>");
    }

    $file_tmp = $_FILES['student_photo']['tmp_name'];

    // Cloudinary Upload Logic
    $url = "https://api.cloudinary.com/v1_1/$cloud_name/image/upload";
    
    $data = [
        'file' => new CURLFile($file_tmp),
        'upload_preset' => $upload_preset, 
        'api_key' => $api_key
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

    $response = curl_exec($ch);
    $result = json_decode($response, true);
    curl_close($ch);

    // Process Cloudinary Result
    if (isset($result['secure_url'])) {
        $photo_url = $result['secure_url']; 

        try {
            // Insert into Neon
            $sql = "INSERT INTO students (full_name, admission_no, reg_code, photo_url, class_id) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $adm, $reg, $photo_url, $class_id]);
            
            // Success: Return to the student selection page with the journey intact
            header("Location: manage_students.php?mode=$mode&dept_id=$dept_id&class_id=$class_id&unit_id=$unit_id&msg=success");
            exit();

        } catch (PDOException $e) {
            die("Database Error: " . $e->getMessage());
        }
    } else {
        echo "<h3>Upload Failed</h3>";
        echo "Error: " . ($result['error']['message'] ?? 'Unknown connection error');
        echo "<br><a href='javascript:history.back()'>Go Back</a>";
    }
} else {
    header("Location: dashboard.php");
    exit();
}
?>