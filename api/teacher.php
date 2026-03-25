<?php

// Include database connection
include('db.php');

// Teacher signup endpoint
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $sql = "INSERT INTO teachers (username, password) VALUES (?, ?);";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username, $password]);
    echo json_encode(['status' => 'success', 'message' => 'Teacher signed up successfully.']);
}

// Teacher login endpoint
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $sql = "SELECT password FROM teachers WHERE username = ?;";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (password_verify($password, $row['password'])) {
            echo json_encode(['status' => 'success', 'message' => 'Login successful.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid credentials.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User does not exist.']);
    }
}

// Get all teachers endpoint
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['get_all'])) {
    $sql = "SELECT * FROM teachers;";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($teachers);
}

// Update teacher endpoint
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['update'])) {
    parse_str(file_get_contents("php://input"), $put_vars);
    $id = $put_vars['id'];
    $username = $put_vars['username'];
    $sql = "UPDATE teachers SET username = ? WHERE id = ?;";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username, $id]);
    echo json_encode(['status' => 'success', 'message' => 'Teacher updated successfully.']);
}

// Delete teacher endpoint
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['delete'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM teachers WHERE id = ?;";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    echo json_encode(['status' => 'success', 'message' => 'Teacher deleted successfully.']);
}