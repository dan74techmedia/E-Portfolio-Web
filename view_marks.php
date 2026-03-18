<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$user_id = $_SESSION['user_id'];
$teacher_name = $_SESSION['teacher_name'] ?? "Instructor";
$institution = $_SESSION['institution'] ?? "Institution Name";

$class_id = $_GET['class_id'] ?? null;
$unit_id = $_GET['unit_id'] ?? null;

if (!$class_id || !$unit_id) {
    header("Location: dashboard.php");
    exit();
}

try {
    // 1. Get Class & Department Info
    $info_stmt = $pdo->prepare("
        SELECT c.class_name, d.dept_name 
        FROM classes c 
        LEFT JOIN departments d ON c.dept_id = d.id 
        WHERE c.id = ?
    ");
    $info_stmt->execute([$class_id]);
    $class_details = $info_stmt->fetch(PDO::FETCH_ASSOC);

    // 2. Get Unit Name & Code
    $u_stmt = $pdo->prepare("SELECT unit_name, unit_code FROM units WHERE id = ?");
    $u_stmt->execute([$unit_id]);
    $unit_details = $u_stmt->fetch(PDO::FETCH_ASSOC);

    // 3. Get Students, Marks, and Registration Codes
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

$report_timestamp = date('l, F j, Y | h:i A'); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Class Report - <?= htmlspecialchars($institution) ?></title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f4f7f6; padding: 20px; color: #333; margin: 0; }
        .report-card { background: white; padding: 35px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 1200px; margin: auto; }
        
        /* Actions */
        .actions-bar { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .btn-print { background: #444; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; font-weight: bold; transition: 0.3s; cursor: pointer; border: none; }
        .btn-print:hover { background: #000; }
        .back-link { text-decoration: none; color: #17a2b8; font-weight: bold; display: flex; align-items: center; }

        /* Official Header */
        .report-header { text-align: center; border-bottom: 3px double #17a2b8; margin-bottom: 25px; padding-bottom: 15px; }
        .report-header h1 { margin: 0; color: #17a2b8; text-transform: uppercase; letter-spacing: 1px; }
        .report-header p { margin: 5px 0; font-weight: bold; font-size: 1.1em; }
        .timestamp { color: #666; font-size: 0.9em; }

        /* Analysis Metadata */
        .analysis-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; background: #f8f9fa; padding: 20px; border-radius: 6px; margin-bottom: 25px; border: 1px solid #e9ecef; }
        .meta-box b { color: #17a2b8; font-size: 0.85em; text-transform: uppercase; display: block; margin-bottom: 2px; }
        .meta-box span { font-weight: 600; color: #444; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 0.85em; }
        th { background: #17a2b8; color: white; padding: 12px 8px; border: 1px solid #dee2e6; }
        td { padding: 10px 8px; border: 1px solid #eee; text-align: center; vertical-align: middle; }
        
        .student-cell { text-align: left; display: flex; align-items: center; gap: 12px; }
        .student-photo { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #17a2b8; background: #eee; }
        .codes { font-size: 0.8em; color: #777; line-height: 1.4; margin-top: 3px; }

        @media print {
            .actions-bar { display: none; }
            body { background: white; padding: 0; }
            .report-card { box-shadow: none; border: none; max-width: 100%; padding: 0; }
            th { background: #17a2b8 !important; color: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>

<div class="report-card">
    <div class="actions-bar">
        <a href="manage_students.php?mode=view&class_id=<?= $class_id ?>&unit_id=<?= $unit_id ?>" class="back-link">← Return to Student List</a>
        <button onclick="window.print()" class="btn-print">🖨️ Print Analysis</button>
    </div>

    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'saved'): ?>
        <div style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 5px; text-align: center; border: 1px solid #c3e6cb;">
            ✅ Marks saved successfully!
        </div>
    <?php endif; ?>

    <div class="report-header">
        <h1><?= htmlspecialchars($institution) ?></h1>
        <p>Student Academic Performance & Assessment Report</p>
        <div class="timestamp">Generated on: <?= $report_timestamp ?></div>
    </div>

    <?php if($students && $class_details): ?>
    <div class="analysis-grid">
        <div class="meta-box"><b>Teacher:</b> <span><?= htmlspecialchars($teacher_name) ?></span></div>
        <div class="meta-box"><b>Department:</b> <span><?= htmlspecialchars($class_details['dept_name'] ?? 'Not Assigned') ?></span></div>
        <div class="meta-box"><b>Total Class Size:</b> <span><?= count($students) ?> Students</span></div>
        <div class="meta-box"><b>Class Name:</b> <span><?= htmlspecialchars($class_details['class_name']) ?></span></div>
        <div class="meta-box"><b>Unit Name:</b> <span><?= htmlspecialchars($unit_details['unit_name']) ?></span></div>
        <div class="meta-box"><b>Unit Code:</b> <span><?= htmlspecialchars($unit_details['unit_code']) ?></span></div>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 250px;">Student Information</th>
                <th colspan="3">Theory Assessment (CAT)</th>
                <th colspan="3">Practical Assessment (CHK)</th>
                <th rowspan="2">Comment & Remarks</th>
            </tr>
            <tr>
                <th>CAT 1</th><th>CAT 2</th><th>CAT 3</th>
                <th>CHK 1</th><th>CHK 2</th><th>CHK 3</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($students as $s): ?>
            <tr>
                <td>
                    <div class="student-cell">
                        <img src="<?= htmlspecialchars($s['photo_url']) ?>" class="student-photo" alt="Photo">
                        <div>
                            <div style="font-weight: bold; color: #111;"><?= htmlspecialchars($s['full_name']) ?></div>
                            <div class="codes">
                                ADM: <?= htmlspecialchars($s['admission_no'] ?? '-') ?><br>
                                REG: <?= htmlspecialchars($s['reg_code'] ?? 'N/A') ?>
                            </div>
                        </div>
                    </div>
                </td>
                <td><?= $s['cat1'] !== null ? htmlspecialchars($s['cat1']) : '-' ?></td>
                <td><?= $s['cat2'] !== null ? htmlspecialchars($s['cat2']) : '-' ?></td>
                <td><?= $s['cat3'] !== null ? htmlspecialchars($s['cat3']) : '-' ?></td>
                <td><?= $s['chk1'] !== null ? htmlspecialchars($s['chk1']) : '-' ?></td>
                <td><?= $s['chk2'] !== null ? htmlspecialchars($s['chk2']) : '-' ?></td>
                <td><?= $s['chk3'] !== null ? htmlspecialchars($s['chk3']) : '-' ?></td>
                <td style="text-align: left; max-width: 180px; font-size: 0.9em; color: #555;">
                    <?= htmlspecialchars($s['comment'] ?? 'No remarks.') ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p style="text-align: center; color: #666; padding: 40px;">No data available for this class and unit.</p>
    <?php endif; ?>
</div>

</body>
</html>