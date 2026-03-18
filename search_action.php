<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) { exit("Unauthorized"); }

$query = $_GET['q'] ?? '';
$teacher_id = $_SESSION['user_id'];

if (strlen($query) < 2) {
    echo "<div class='search-hint'>Type at least 2 characters to search...</div>";
    exit();
}

try {
    $search_term = "%$query%";

    // Searching Students by Name or Admission
    $sql_students = "SELECT s.id, s.full_name as title, s.admission_no as sub, c.id as class_id, 'student' as type 
                     FROM students s 
                     JOIN classes c ON s.class_id = c.id
                     WHERE (s.full_name ILIKE ? OR s.admission_no ILIKE ?) LIMIT 5";
    
    // Searching Classes by Name
    $sql_classes = "SELECT id, class_name as title, 'Class' as sub, id as class_id, 'class' as type 
                    FROM classes WHERE class_name ILIKE ? LIMIT 3";

    // Searching Units by Name or Code
    $sql_units = "SELECT id, unit_name as title, unit_code as sub, id as unit_id, 'unit' as type 
                  FROM units WHERE (unit_name ILIKE ? OR unit_code ILIKE ?) LIMIT 3";

    // Combine them or run separately for better UI categorization
    $stmt1 = $pdo->prepare($sql_students);
    $stmt1->execute([$search_term, $search_term]);
    $results = $stmt1->fetchAll(PDO::FETCH_ASSOC);

    $stmt2 = $pdo->prepare($sql_classes);
    $stmt2->execute([$search_term]);
    $results = array_merge($results, $stmt2->fetchAll(PDO::FETCH_ASSOC));

    if (empty($results)) {
        echo "<div class='no-results'>No matches found for '$query'</div>";
    } else {
        foreach ($results as $row) {
            $link = ($row['type'] == 'student') 
                    ? "view_student.php?student_id={$row['id']}" 
                    : "manage_students.php?class_id={$row['class_id']}&mode=view";
            
            echo "<a href='$link' class='search-result-item'>";
            echo "<strong>" . htmlspecialchars($row['title']) . "</strong>";
            echo "<span>" . htmlspecialchars($row['sub']) . " (" . ucfirst($row['type']) . ")</span>";
            echo "</a>";
        }
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}