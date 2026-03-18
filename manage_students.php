<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Catch the accumulated journey variables
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'view';
$dept_id = isset($_GET['dept_id']) ? $_GET['dept_id'] : null;
$class_id = isset($_GET['class_id']) ? $_GET['class_id'] : null;
$unit_id = isset($_GET['unit_id']) ? $_GET['unit_id'] : null;

// Ensure they didn't skip steps
if (!$class_id || !$unit_id) {
    header("Location: dashboard.php");
    exit();
}

try {
    // 1. Fetch Context Info (Class & Unit)
    $info_stmt = $pdo->prepare("
        SELECT c.class_name, u.unit_name, u.unit_code 
        FROM classes c 
        JOIN units u ON u.class_id = c.id 
        WHERE c.id = ? AND u.id = ?
    ");
    $info_stmt->execute([$class_id, $unit_id]);
    $info = $info_stmt->fetch(PDO::FETCH_ASSOC);

    // 2. Fetch Students for this specific class
    $student_stmt = $pdo->prepare("SELECT * FROM students WHERE class_id = ? ORDER BY full_name ASC");
    $student_stmt->execute([$class_id]);
    $students = $student_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
}

// Determine where the "Bulk Action" button should go based on mode
$bulk_action_url = ($mode === 'edit') ? "edit_marks.php?class_id=$class_id&unit_id=$unit_id" : "view_marks.php?class_id=$class_id&unit_id=$unit_id";
$bulk_action_text = ($mode === 'edit') ? "Edit Entire Class (Bulk)" : "View Class Report (Print)";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Students | E-Portfolio</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: 20px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { border-bottom: 2px solid #eee; margin-bottom: 20px; padding-bottom: 10px; display: flex; justify-content: space-between; align-items: center; }
        .breadcrumb { font-size: 0.9rem; color: #666; margin-bottom: 5px; }
        .mode-badge { background: #e7f3ff; color: #007bff; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; text-transform: uppercase; }

        /* Actions Section */
        .actions-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; background: #f8f9fa; padding: 15px; border-radius: 8px; border: 1px solid #ddd; }
        .bulk-btn { background: #28a745; color: white; padding: 12px 20px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 1.1rem; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .bulk-btn:hover { background: #218838; }

        /* Student Grid */
        .student-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; margin-bottom: 30px; }
        .student-card { background: #fff; border: 1px solid #ddd; padding: 15px; border-radius: 8px; text-align: center; text-decoration: none; color: inherit; transition: 0.2s; display: block; }
        .student-card:hover { border-color: #007bff; box-shadow: 0 4px 10px rgba(0,123,255,0.15); transform: translateY(-3px); }
        .student-card img { width: 90px; height: 90px; border-radius: 50%; object-fit: cover; margin-bottom: 10px; border: 3px solid #f0f2f5; }
        .student-name { font-weight: bold; margin-bottom: 5px; color: #333; }
        .student-details { font-size: 0.8em; color: #666; }

        /* Add Form */
        .add-section { background: #fdfdfd; padding: 20px; border-radius: 8px; border: 1px dashed #17a2b8; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px; }
        input[type="text"], input[type="file"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #17a2b8; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; }
        button:hover { background: #138496; }
        
        .back-link { text-decoration: none; color: #17a2b8; font-size: 0.9rem; margin-bottom: 10px; display: inline-block; }
    </style>
</head>
<body>

<div class="container">
    <a href="select_unit.php?mode=<?php echo $mode; ?>&dept_id=<?php echo $dept_id; ?>&class_id=<?php echo $class_id; ?>" class="back-link">← Change Unit</a>
    
    <div class="header">
        <div>
            <div class="breadcrumb"><?php echo htmlspecialchars($info['class_name']); ?> > <strong><?php echo htmlspecialchars($info['unit_name']); ?> (<?php echo htmlspecialchars($info['unit_code']); ?>)</strong></div>
            <h2 style="margin:0;">4. Manage Students</h2>
        </div>
        <span class="mode-badge"><?php echo $mode; ?> mode</span>
    </div>

    <!-- Bulk Action Bar -->
    <?php if (!empty($students)): ?>
        <div class="actions-bar">
            <div>
                <strong style="color: #333; font-size: 1.1rem;">Ready to proceed?</strong><br>
                <small style="color: #666;">You have <?php echo count($students); ?> students registered.</small>
            </div>
            <a href="<?php echo $bulk_action_url; ?>" class="bulk-btn"><?php echo $bulk_action_text; ?> →</a>
        </div>
    <?php endif; ?>

    <!-- Student Selection Grid -->
    <div class="student-list">
        <?php if (empty($students)): ?>
            <p style="grid-column: 1 / -1; text-align: center; color: #666; padding: 20px;">No students enrolled in this class yet. Please add them below.</p>
        <?php else: ?>
            <?php foreach ($students as $s): ?>
                <?php 
                    // Set individual link based on mode
                    $individual_url = ($mode === 'edit') 
                        ? "edit_student.php?student_id={$s['id']}&unit_id=$unit_id" 
                        : "view_student.php?student_id={$s['id']}&unit_id=$unit_id"; 
                ?>
                <a href="<?php echo $individual_url; ?>" class="student-card">
                    <img src="<?php echo htmlspecialchars($s['photo_url']); ?>" alt="Photo">
                    <div class="student-name"><?php echo htmlspecialchars($s['full_name']); ?></div>
                    <div class="student-details">Adm: <?php echo htmlspecialchars($s['admission_no']); ?></div>
                    <div class="student-details">Reg: <?php echo htmlspecialchars($s['reg_code']); ?></div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Add New Student Section -->
    <div class="add-section">
        <h3 style="margin-top:0; color: #17a2b8;">Enroll New Student</h3>
        
        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
            <div style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 5px; text-align: center;">Student successfully enrolled and photo uploaded!</div>
        <?php endif; ?>

        <form action="student_action.php" method="POST" enctype="multipart/form-data">
            <!-- Hidden inputs to maintain the journey state -->
            <input type="hidden" name="mode" value="<?php echo $mode; ?>">
            <input type="hidden" name="dept_id" value="<?php echo $dept_id; ?>">
            <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
            <input type="hidden" name="unit_id" value="<?php echo $unit_id; ?>">

            <div class="form-grid">
                <input type="text" name="full_name" placeholder="Full Name (e.g. John Doe)" required>
                <input type="text" name="admission_no" placeholder="Admission Number (e.g. ADM/001)" required>
                <input type="text" name="reg_code" placeholder="Registration Code (e.g. REG-24)" required>
                <input type="file" name="student_photo" accept="image/*" required>
            </div>
            
            <button type="submit">Upload Photo & Register Student</button>
        </form>
    </div>
</div>

</body>
</html>