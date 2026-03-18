<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$student_id = $_GET['student_id'] ?? null;
$unit_id = $_GET['unit_id'] ?? null;

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

    if (!$data) { die("Data not found."); }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile | <?= htmlspecialchars($data['full_name']) ?></title>
    <!-- Include html2pdf Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; margin: 0; padding: 20px; display: flex; flex-direction: column; align-items: center; min-height: 100vh; }
        
        .action-buttons { margin-bottom: 20px; display: flex; gap: 10px; }
        .btn-action { padding: 10px 20px; border-radius: 20px; border: none; cursor: pointer; font-weight: bold; text-decoration: none; font-size: 0.9rem; }
        .btn-pdf { background: #e74c3c; color: white; }
        
        /* The Card Container */
        .card { width: 480px; background: white; border-radius: 15px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); overflow: hidden; border-top: 8px solid #17a2b8; }
        
        .card-header { padding: 30px 20px; text-align: center; background: #fafafa; border-bottom: 1px solid #eee; }
        .card-header img { width: 110px; height: 110px; border-radius: 50%; object-fit: cover; border: 3px solid #17a2b8; padding: 3px; margin-bottom: 10px; }
        .student-name { margin: 0; color: #333; font-size: 1.5rem; }
        .reg-code { color: #666; font-weight: bold; font-size: 0.9rem; letter-spacing: 1px; }
        
        .card-content { padding: 25px; }
        .info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f1f1f1; }
        .info-label { color: #888; font-size: 0.75rem; font-weight: bold; text-transform: uppercase; }
        .info-value { color: #333; font-weight: 600; text-align: right; }
        .unit-subtext { display: block; font-size: 0.75rem; color: #17a2b8; font-weight: bold; }
        
        .marks-summary { margin-top: 15px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .score-item { background: #f8f9fa; padding: 12px; border-radius: 8px; text-align: center; border: 1px solid #eee; }
        .score-item small { display: block; color: #17a2b8; font-size: 0.65rem; font-weight: bold; text-transform: uppercase; margin-bottom: 4px; }
        .score-item span { font-size: 1.2rem; font-weight: bold; color: #222; }

        .comment-area { font-style: italic; color: #555; background: #fffde7; padding: 15px; border-radius: 8px; margin-top: 20px; font-size: 0.9rem; border-left: 4px solid #ffd600; }
        .btn-back { display: block; text-align: center; padding: 18px; background: #17a2b8; color: white; text-decoration: none; font-weight: bold; width: 480px; margin-top: 20px; border-radius: 10px; }
    </style>
</head>
<body>

<div class="action-buttons">
    <button onclick="downloadPDF()" class="btn-action btn-pdf">📄 Download Profile PDF</button>
</div>

<!-- This div id="report-card" is what will be turned into PDF -->
<div class="card" id="report-card">
    <div class="card-header">
        <img src="<?= htmlspecialchars($data['photo_url']) ?>" alt="Student Photo">
        <h2 class="student-name"><?= htmlspecialchars($data['full_name']) ?></h2>
        <div class="reg-code"><?= htmlspecialchars($data['reg_code'] ?? 'NO-REG-CODE') ?></div>
    </div>

    <div class="card-content">
        <div class="info-section">
            <div class="info-row">
                <span class="info-label">Admission</span>
                <span class="info-value"><?= htmlspecialchars($data['admission_no']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Class</span>
                <span class="info-value"><?= htmlspecialchars($data['class_name']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Unit</span>
                <div class="info-value">
                    <?= htmlspecialchars($data['unit_name']) ?>
                    <span class="unit-subtext"><?= htmlspecialchars($data['unit_code']) ?></span>
                </div>
            </div>
        </div>

        <h4 style="margin: 25px 0 10px; font-size: 0.8rem; color: #999; text-align: center; letter-spacing: 2px;">ASSESSMENT LOG</h4>
        
        <div class="marks-summary">
            <div class="score-item"><small>CAT 1</small><span><?= $data['cat1'] ?? '-' ?></span></div>
            <div class="score-item"><small>CHK 1</small><span><?= $data['chk1'] ?? '-' ?></span></div>
            <div class="score-item"><small>CAT 2</small><span><?= $data['cat2'] ?? '-' ?></span></div>
            <div class="score-item"><small>CHK 2</small><span><?= $data['chk2'] ?? '-' ?></span></div>
            <div class="score-item"><small>End Term</small><span><?= $data['cat3'] ?? '-' ?></span></div>
            <div class="score-item"><small>CHK 3</small><span><?= $data['chk3'] ?? '-' ?></span></div>
        </div>

        <div class="comment-area">
            "<?= htmlspecialchars($data['comment'] ?? 'No teacher remarks recorded for this unit.') ?>"
        </div>
    </div>
</div>

<a href="manage_students.php?mode=view&class_id=<?= $data['class_id'] ?>&unit_id=<?= $unit_id ?>" class="btn-back">Return to Class List</a>

<script>
function downloadPDF() {
    const element = document.getElementById('report-card');
    const opt = {
        margin:       10,
        filename:     'Profile_<?= $data['admission_no'] ?>.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2 },
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };

    // New Promise-based usage:
    html2pdf().set(opt).from(element).save();
}
</script>

</body>
</html>