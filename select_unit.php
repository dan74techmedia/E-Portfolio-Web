<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Catch parameters from Step 2
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'view';
$dept_id = isset($_GET['dept_id']) ? $_GET['dept_id'] : null;
$class_id = isset($_GET['class_id']) ? $_GET['class_id'] : null;

if (!$class_id) {
    header("Location: select_class.php?mode=$mode&dept_id=$dept_id");
    exit();
}

try {
    // 1. Fetch Class and Dept details for the header
    $info_stmt = $pdo->prepare("
        SELECT c.class_name, d.dept_name 
        FROM classes c 
        JOIN departments d ON c.dept_id = d.id 
        WHERE c.id = ?
    ");
    $info_stmt->execute([$class_id]);
    $info = $info_stmt->fetch();

    // 2. Fetch units for THIS class
    $unit_stmt = $pdo->prepare("SELECT * FROM units WHERE class_id = ? ORDER BY unit_name ASC");
    $unit_stmt->execute([$class_id]);
    $units = $unit_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching units: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Unit | E-Portfolio</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .container { max-width: 750px; margin: 40px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { border-bottom: 2px solid #eee; margin-bottom: 25px; padding-bottom: 10px; }
        .breadcrumb { font-size: 0.85rem; color: #666; margin-bottom: 5px; }
        
        .selection-list { display: grid; grid-template-columns: 1fr; gap: 10px; margin-bottom: 30px; }
        .unit-card { 
            display: flex; justify-content: space-between; align-items: center;
            padding: 15px 20px; background: #f8f9fa; border: 1px solid #ddd; border-radius: 8px;
            text-decoration: none; color: #333; transition: 0.2s;
        }
        .unit-card:hover { border-color: #6f42c1; background: #f3effb; color: #6f42c1; }
        .unit-code-badge { background: #eee; padding: 2px 8px; border-radius: 4px; font-family: monospace; font-size: 0.9rem; }

        .add-section { background: #fbf9ff; padding: 20px; border-radius: 8px; border: 1px dashed #6f42c1; }
        .form-row { display: flex; gap: 10px; margin-bottom: 10px; }
        input[type="text"] { flex: 1; padding: 12px; border: 1px solid #ccc; border-radius: 6px; }
        button { width: 100%; padding: 12px; background: #6f42c1; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; }
        button:hover { background: #5a32a3; }
        
        .back-link { text-decoration: none; color: #6f42c1; font-size: 0.9rem; display: inline-block; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="container">
    <a href="select_class.php?mode=<?php echo $mode; ?>&dept_id=<?php echo $dept_id; ?>" class="back-link">← Change Class</a>
    
    <div class="header">
        <div class="breadcrumb"><?php echo htmlspecialchars($info['dept_name']); ?> > <strong><?php echo htmlspecialchars($info['class_name']); ?></strong></div>
        <h2>3. Select Unit</h2>
    </div>

    <?php if (!empty($units)): ?>
        <div class="selection-list">
            <?php foreach ($units as $u): ?>
                <a href="manage_students.php?mode=<?php echo $mode; ?>&dept_id=<?php echo $dept_id; ?>&class_id=<?php echo $class_id; ?>&unit_id=<?php echo $u['id']; ?>" class="unit-card">
                    <div>
                        <span class="unit-code-badge"><?php echo htmlspecialchars($u['unit_code']); ?></span>
                        <strong style="margin-left:10px;"><?php echo htmlspecialchars($u['unit_name']); ?></strong>
                    </div>
                    <span style="font-size: 1.2rem;">→</span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p style="text-align: center; color: #666;">No units assigned to this class yet.</p>
    <?php endif; ?>

    <div class="add-section">
        <h3 style="margin-top:0; font-size:1.1rem; color: #6f42c1;">Add New Unit to this Class</h3>
        <form action="unit_action.php" method="POST">
            <input type="hidden" name="mode" value="<?php echo $mode; ?>">
            <input type="hidden" name="dept_id" value="<?php echo $dept_id; ?>">
            <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
            
            <div class="form-row">
                <input type="text" name="unit_name" placeholder="Unit Name (e.g. Communication Skills)" required>
                <input type="text" name="unit_code" placeholder="Code (e.g. COM_101)" required>
            </div>
            <button type="submit">Add & Proceed to Students</button>
        </form>
    </div>
</div>

</body>
</html>