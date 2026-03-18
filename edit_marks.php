<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

// Catch the parameters passed from manage_students.php
$class_id = $_GET['class_id'] ?? null;
$unit_id = $_GET['unit_id'] ?? null;

if (!$class_id || !$unit_id) {
    header("Location: dashboard.php");
    exit();
}

try {
    // 1. Fetch Context Info for the Header
    $info_stmt = $pdo->prepare("
        SELECT c.class_name, u.unit_name, u.unit_code 
        FROM classes c 
        JOIN units u ON u.class_id = c.id 
        WHERE c.id = ? AND u.id = ?
    ");
    $info_stmt->execute([$class_id, $unit_id]);
    $info = $info_stmt->fetch(PDO::FETCH_ASSOC);

    // 2. Fetch Students and their Marks (if any exist)
    $sql = "SELECT s.*, m.cat1, m.cat2, m.cat3, m.chk1, m.chk2, m.chk3, m.comment
            FROM students s
            LEFT JOIN marks m ON s.id = m.student_id AND m.unit_id = ?
            WHERE s.class_id = ? ORDER BY s.full_name ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$unit_id, $class_id]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Marks | E-Portfolio</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #fdfdfd; padding: 20px; }
        .edit-container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-top: 5px solid #ffc107; }
        .header-info { background: #fffdf5; padding: 15px; border: 1px solid #ffeeba; border-radius: 6px; margin-bottom: 20px; }
        .header-info h3 { margin: 0 0 5px 0; color: #856404; }
        .header-info p { margin: 0; color: #666; font-size: 0.95rem; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #343a40; color: white; padding: 12px; font-size: 0.9rem; }
        td { padding: 10px; border: 1px solid #ddd; vertical-align: middle; }
        
        .student-name { font-weight: bold; color: #333; display: block; }
        .student-adm { font-size: 0.8rem; color: #666; }
        
        input[type="number"] { width: 60px; padding: 6px; text-align: center; border-radius: 4px; border: 1px solid #ccc; font-size: 1rem; }
        input[type="text"] { width: 90%; padding: 6px; border-radius: 4px; border: 1px solid #ccc; font-size: 0.9rem; }
        
        .save-btn { background: #28a745; color: white; padding: 15px 30px; border: none; border-radius: 5px; font-weight: bold; font-size: 1.1rem; cursor: pointer; display: block; margin: 30px auto 0; width: 100%; max-width: 400px; transition: 0.2s; }
        .save-btn:hover { background: #218838; }
        
        .back-link { text-decoration: none; color: #007bff; margin-bottom: 20px; display: inline-block; font-weight: bold; }
    </style>
</head>
<body>
    <div class="edit-container">
        <!-- Allows teacher to go back to the previous screen easily -->
        <a href="manage_students.php?mode=edit&class_id=<?= $class_id ?>&unit_id=<?= $unit_id ?>" class="back-link">← Back to Students</a>
        
        <h2>✍️ Bulk Edit Assessment Marks</h2>
        
        <div class="header-info">
            <h3><?= htmlspecialchars($info['class_name']) ?></h3>
            <p>Unit: <strong><?= htmlspecialchars($info['unit_name']) ?> (<?= htmlspecialchars($info['unit_code']) ?>)</strong></p>
        </div>

        <?php if($students): ?>
        <form action="marks_action.php" method="POST">
            <input type="hidden" name="unit_id" value="<?= htmlspecialchars($unit_id) ?>">
            <input type="hidden" name="class_id" value="<?= htmlspecialchars($class_id) ?>">
            
            <table>
                <tr>
                    <th>Student Details</th>
                    <th>CAT 1</th><th>CAT 2</th><th>CAT 3</th>
                    <th>CHK 1</th><th>CHK 2</th><th>CHK 3</th>
                    <th>Remarks / Comment</th>
                </tr>
                <?php foreach($students as $s): ?>
                <tr>
                    <td>
                        <span class="student-name"><?= htmlspecialchars($s['full_name']) ?></span>
                        <span class="student-adm">Adm: <?= htmlspecialchars($s['admission_no']) ?></span>
                        <input type="hidden" name="student_ids[]" value="<?= $s['id'] ?>">
                    </td>
                    <td><input type="number" name="cat1[]" value="<?= $s['cat1'] ?? 0 ?>" min="0" max="100"></td>
                    <td><input type="number" name="cat2[]" value="<?= $s['cat2'] ?? 0 ?>" min="0" max="100"></td>
                    <td><input type="number" name="cat3[]" value="<?= $s['cat3'] ?? 0 ?>" min="0" max="100"></td>
                    <td><input type="number" name="chk1[]" value="<?= $s['chk1'] ?? 0 ?>" min="0" max="100"></td>
                    <td><input type="number" name="chk2[]" value="<?= $s['chk2'] ?? 0 ?>" min="0" max="100"></td>
                    <td><input type="number" name="chk3[]" value="<?= $s['chk3'] ?? 0 ?>" min="0" max="100"></td>
                    <td><input type="text" name="comment[]" value="<?= htmlspecialchars($s['comment'] ?? '') ?>" placeholder="Optional remark..."></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <button type="submit" class="save-btn">💾 Save All Marks Permanently</button>
        </form>
        <?php else: ?>
            <p style="text-align: center; color: #666; padding: 30px;">No students found in this class. Please add students first.</p>
        <?php endif; ?>
    </div>
</body>
</html>