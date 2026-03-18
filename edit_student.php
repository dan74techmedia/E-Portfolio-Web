<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$student_id = $_GET['student_id'] ?? null;
$unit_id = $_GET['unit_id'] ?? null;

if (!$student_id || !$unit_id) { die("Invalid Access."); }

try {
    $sql = "SELECT s.*, c.class_name, u.unit_name, u.unit_code,
                   m.cat1, m.cat2, m.cat3, m.chk1, m.chk2, m.chk3, m.comment
            FROM students s
            JOIN classes c ON s.class_id = c.id
            JOIN units u ON u.id = ?
            LEFT JOIN marks m ON s.id = m.student_id AND m.unit_id = u.id
            WHERE s.id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$unit_id, $student_id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) { die($e->getMessage()); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Marks | <?= htmlspecialchars($data['full_name']) ?></title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; padding: 20px; }
        .profile-container { max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; }
        .profile-header { background: #6f42c1; color: white; padding: 25px; text-align: center; }
        .profile-header img { width: 100px; height: 100px; border-radius: 50%; border: 3px solid rgba(255,255,255,0.3); object-fit: cover; }
        
        .form-body { padding: 30px; }
        .marks-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .input-group label { display: block; font-weight: bold; margin-bottom: 5px; color: #555; font-size: 0.85rem; }
        input[type="number"], textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; font-size: 1rem; }
        
        .end-term-box { grid-column: span 1; background: #fff3e0; border: 1px solid #ffe0b2; padding: 10px; border-radius: 6px; }
        .btn-save { background: #28a745; color: white; width: 100%; padding: 15px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 1rem; }
    </style>
</head>
<body>

<div class="profile-container">
    <div class="profile-header">
        <img src="<?= htmlspecialchars($data['photo_url']) ?>">
        <h2 style="margin:10px 0 0;"><?= htmlspecialchars($data['full_name']) ?></h2>
        <small><?= htmlspecialchars($data['unit_name']) ?></small>
    </div>

    <form action="marks_action.php" method="POST" class="form-body">
        <input type="hidden" name="unit_id" value="<?= $unit_id ?>">
        <input type="hidden" name="class_id" value="<?= $data['class_id'] ?>">
        <input type="hidden" name="student_ids[]" value="<?= $student_id ?>">

        <div class="marks-grid">
            <div class="input-group"><label>CAT 1</label><input type="number" name="cat1[]" value="<?= $data['cat1'] ?? 0 ?>"></div>
            <div class="input-group"><label>CHK 1</label><input type="number" name="chk1[]" value="<?= $data['chk1'] ?? 0 ?>"></div>
            
            <div class="input-group"><label>CAT 2</label><input type="number" name="cat2[]" value="<?= $data['cat2'] ?? 0 ?>"></div>
            <div class="input-group"><label>CHK 2</label><input type="number" name="chk2[]" value="<?= $data['chk2'] ?? 0 ?>"></div>
            
            <div class="input-group end-term-box"><label style="color: #e65100;">END TERM (CAT 3)</label><input type="number" name="cat3[]" value="<?= $data['cat3'] ?? 0 ?>"></div>
            <div class="input-group"><label>CHK 3</label><input type="number" name="chk3[]" value="<?= $data['chk3'] ?? 0 ?>"></div>
        </div>

        <div class="input-group" style="margin-bottom: 20px;">
            <label>Teacher's Remarks</label>
            <textarea name="comment[]" rows="3"><?= htmlspecialchars($data['comment'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn-save">Save Assessment Records</button>
        <a href="manage_students.php?mode=edit&class_id=<?= $data['class_id'] ?>&unit_id=<?= $unit_id ?>" style="display:block; text-align:center; margin-top:15px; color:#666; text-decoration:none;">Go Back</a>
    </form>
</div>

</body>
</html>