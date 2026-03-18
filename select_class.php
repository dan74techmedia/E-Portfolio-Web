<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Catch the parameters from the previous step
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'view';
$dept_id = isset($_GET['dept_id']) ? $_GET['dept_id'] : null;

if (!$dept_id) {
    header("Location: select_dept.php?mode=$mode");
    exit();
}

try {
    // 1. Fetch Department Details for the header
    $dept_stmt = $pdo->prepare("SELECT dept_name FROM departments WHERE id = ?");
    $dept_stmt->execute([$dept_id]);
    $dept = $dept_stmt->fetch();

    // 2. Fetch classes for THIS department and THIS teacher
    $class_stmt = $pdo->prepare("SELECT * FROM classes WHERE dept_id = ? AND teacher_id = ? ORDER BY class_name ASC");
    $class_stmt->execute([$dept_id, $_SESSION['user_id']]);
    $classes = $class_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching classes: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Class | E-Portfolio</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .container { max-width: 700px; margin: 40px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { border-bottom: 2px solid #eee; margin-bottom: 25px; padding-bottom: 10px; }
        .header h2 { margin: 0; color: #1c1e21; }
        .breadcrumb { font-size: 0.9rem; color: #666; margin-bottom: 10px; }
        
        .selection-list { display: grid; grid-template-columns: 1fr; gap: 10px; margin-bottom: 30px; }
        .class-card { 
            display: flex; justify-content: space-between; align-items: center;
            padding: 15px 20px; background: #f8f9fa; border: 1px solid #ddd; border-radius: 8px;
            text-decoration: none; color: #333; font-weight: 600; transition: 0.2s;
        }
        .class-card:hover { border-color: #007bff; background: #e7f3ff; color: #007bff; }

        .add-section { background: #fffdf5; padding: 20px; border-radius: 8px; border: 1px dashed #ffc107; }
        .form-group { display: flex; flex-direction: column; gap: 10px; }
        input[type="text"] { padding: 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 1rem; }
        button { padding: 12px; background: #007bff; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; }
        button:hover { background: #0056b3; }
        
        .back-link { text-decoration: none; color: #007bff; font-size: 0.9rem; display: inline-block; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="container">
    <a href="select_dept.php?mode=<?php echo $mode; ?>" class="back-link">← Change Department</a>
    
    <div class="header">
        <div class="breadcrumb">Department: <strong><?php echo htmlspecialchars($dept['dept_name']); ?></strong></div>
        <h2>2. Select Class</h2>
    </div>

    <!-- Selection Stage -->
    <?php if (!empty($classes)): ?>
        <div class="selection-list">
            <?php foreach ($classes as $class): ?>
                <a href="select_unit.php?mode=<?php echo $mode; ?>&dept_id=<?php echo $dept_id; ?>&class_id=<?php echo $class['id']; ?>" class="class-card">
                    <span>🏫 <?php echo htmlspecialchars($class['class_name']); ?></span>
                    <span style="font-size: 1.2rem;">→</span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p style="text-align: center; color: #666;">No classes found in this department. Add one below!</p>
    <?php endif; ?>

    <!-- Add Stage -->
    <div class="add-section">
        <h3 style="margin-top:0; font-size:1.1rem;">Add New Class to this Department</h3>
        <form action="class_action.php" method="POST" class="form-group">
            <input type="hidden" name="mode" value="<?php echo $mode; ?>">
            <input type="hidden" name="dept_id" value="<?php echo $dept_id; ?>">
            <input type="text" name="class_name" placeholder="e.g. Ict Level 6 - 2024 Intake" required>
            <button type="submit">Add & Proceed to Units</button>
        </form>
    </div>
</div>

</body>
</html>