<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get the mode (view or edit) from the URL
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'view';

// Fetch all existing departments
try {
    $stmt = $pdo->query("SELECT * FROM departments ORDER BY dept_name ASC");
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching departments: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Department | E-Portfolio</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .container { max-width: 700px; margin: 40px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { border-bottom: 2px solid #eee; margin-bottom: 25px; padding-bottom: 10px; display: flex; justify-content: space-between; align-items: center; }
        h2 { margin: 0; color: #1c1e21; }
        .mode-badge { background: #e7f3ff; color: #007bff; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; text-transform: uppercase; }
        
        /* Selection List */
        .selection-list { display: grid; grid-template-columns: 1fr; gap: 10px; margin-bottom: 30px; }
        .dept-card { 
            display: flex; justify-content: space-between; align-items: center;
            padding: 15px 20px; background: #f8f9fa; border: 1px solid #ddd; border-radius: 8px;
            text-decoration: none; color: #333; font-weight: 600; transition: 0.2s;
        }
        .dept-card:hover { border-color: #007bff; background: #e7f3ff; color: #007bff; }

        /* Add New Form */
        .add-section { background: #f1f3f4; padding: 20px; border-radius: 8px; border: 1px dashed #999; }
        .add-section h3 { margin-top: 0; font-size: 1.1rem; }
        .form-group { display: flex; gap: 10px; }
        input[type="text"] { flex: 1; padding: 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 1rem; }
        button { padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; }
        button:hover { background: #218838; }
        
        .back-link { text-decoration: none; color: #666; font-size: 0.9rem; margin-bottom: 20px; display: inline-block; }
    </style>
</head>
<body>

<div class="container">
    <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
    
    <div class="header">
        <h2>1. Select Department</h2>
        <span class="mode-badge"><?php echo $mode; ?> mode</span>
    </div>

    <!-- Selection Stage -->
    <?php if (!empty($departments)): ?>
        <div class="selection-list">
            <?php foreach ($departments as $dept): ?>
                <a href="select_class.php?mode=<?php echo $mode; ?>&dept_id=<?php echo $dept['id']; ?>" class="dept-card">
                    <span>🏛️ <?php echo htmlspecialchars($dept['dept_name']); ?></span>
                    <span style="font-size: 1.2rem;">→</span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p style="text-align: center; color: #666;">No departments found. Please add one below to start.</p>
    <?php endif; ?>

    <!-- Add Stage (Always available to prevent dead ends) -->
    <div class="add-section">
        <h3>Add New Department</h3>
        <form action="dept_action.php" method="POST" class="form-group">
            <input type="hidden" name="mode" value="<?php echo $mode; ?>">
            <input type="text" name="dept_name" placeholder="e.g. INFORMATION TECHNOLOGY" required>
            <button type="submit">Add & Proceed</button>
        </form>
    </div>
</div>

</body>
</html>