<?php

// Include database connection file
include_once 'db.php';

class Student {
    public $id;
    public $name;
    public $email;
}

// Create Student
function createStudent($name, $email) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO students (name, email) VALUES (?, ?)");
    return $stmt->execute([$name, $email]);
}

// Read Student
function readStudent($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetchObject('Student');
}

// Update Student
function updateStudent($id, $name, $email) {
    global $conn;
    $stmt = $conn->prepare("UPDATE students SET name = ?, email = ? WHERE id = ?");
    return $stmt->execute([$name, $email, $id]);
}

// Delete Student
function deleteStudent($id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    return $stmt->execute([$id]);
}

// Sample API Endpoints
if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    // Create
    if (isset($_POST['name']) && isset($_POST['email'])) {
        createStudent($_POST['name'], $_POST['email']);
        echo json_encode(['status' => 'Student created.']);
    }
} elseif ($_SERVER["REQUEST_METHOD"] === 'GET') {
    // Read
    if (isset($_GET['id'])) {
        $student = readStudent($_GET['id']);
        echo json_encode($student);
    }
} elseif ($_SERVER["REQUEST_METHOD"] === 'PUT') {
    // Update
    parse_str(file_get_contents("php://input"), $_PUT);
    if (isset($_PUT['id']) && isset($_PUT['name']) && isset($_PUT['email'])) {
        updateStudent($_PUT['id'], $_PUT['name'], $_PUT['email']);
        echo json_encode(['status' => 'Student updated.']);
    }
} elseif ($_SERVER["REQUEST_METHOD"] === 'DELETE') {
    // Delete
    if (isset($_GET['id'])) {
        deleteStudent($_GET['id']);
        echo json_encode(['status' => 'Student deleted.']);
    }
} else {
    echo json_encode(['status' => 'Invalid request.']);
}