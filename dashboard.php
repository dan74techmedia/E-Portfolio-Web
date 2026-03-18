<?php
session_start();
// Security Check: If not logged in, boot them to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$teacher_name = $_SESSION['teacher_name'];
$institution = $_SESSION['institution'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | E-Portfolio System</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; margin: 0; color: #333; }
        
        /* Navigation Header */
        .nav { 
            background: #007bff; 
            color: white; 
            padding: 1rem 2rem; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
        }
        .nav-info h2 { margin: 0; font-size: 1.4rem; }
        .nav-info span { font-size: 0.9rem; opacity: 0.8; }
        .logout-btn { 
            color: white; 
            text-decoration: none; 
            background: rgba(255,255,255,0.2); 
            padding: 8px 15px; 
            border-radius: 5px; 
            font-weight: bold; 
            transition: 0.3s;
        }
        .logout-btn:hover { background: rgba(255,255,255,0.3); }

        .container { max-width: 800px; margin: 50px auto; padding: 0 20px; text-align: center; }
        .welcome-msg { margin-bottom: 40px; }
        .welcome-msg h1 { font-size: 2rem; color: #1c1e21; }

        /* Primary Action Cards */
        .action-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); 
            gap: 25px; 
        }
        .action-card { 
            background: white; 
            padding: 40px; 
            border-radius: 15px; 
            text-decoration: none; 
            color: inherit; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
            transition: all 0.3s ease; 
            border-bottom: 6px solid #ddd;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .action-card:hover { 
            transform: translateY(-10px); 
            box-shadow: 0 8px 25px rgba(0,0,0,0.1); 
        }
        .action-card.view { border-color: #17a2b8; }
        .action-card.edit { border-color: #ffc107; }

        .icon { font-size: 3rem; margin-bottom: 15px; }
        .action-card h3 { margin: 10px 0; font-size: 1.5rem; }
        .action-card p { color: #666; font-size: 0.95rem; }

        /* Developer Footer */
        .dev-footer { 
            margin-top: 60px; 
            background: #fff; 
            padding: 25px; 
            border-radius: 12px; 
            border: 1px dashed #25d366; 
        }
        .whatsapp-btn { 
            display: inline-block; 
            background: #25d366; 
            color: white; 
            padding: 12px 25px; 
            border-radius: 50px; 
            text-decoration: none; 
            font-weight: bold; 
            margin-top: 15px; 
            transition: 0.3s;
        }
        .whatsapp-btn:hover { background: #1ebe57; transform: scale(1.05); }
    </style>
</head>
<body>

<div class="nav">
    <div class="nav-info">
        <h2><?php echo htmlspecialchars($teacher_name); ?></h2>
        <span><?php echo htmlspecialchars($institution); ?></span>
    </div>
    <a href="logout.php" class="logout-btn">Logout</a>
</div>

<div class="container">
    <div class="welcome-msg">
        <h1>Assessment Action Center</h1>
        <p>Select an action below to begin the selection journey.</p>
    </div>

    <div class="action-grid">
        <!-- Path A: Viewing Mode -->
        <a href="select_dept.php?mode=view" class="action-card view">
            <div class="icon">📊</div>
            <h3>View Marks</h3>
            <p>Generate reports, analyze student performance, and print records.</p>
        </a>

        <!-- Path B: Editing Mode -->
        <a href="select_dept.php?mode=edit" class="action-card edit">
            <div class="icon">✍️</div>
            <h3>Edit Marks</h3>
            <p>Enter new assessment data or update existing student marks.</p>
        </a>
    </div>
    <style>
    .search-container { position: relative; margin: 20px 0; }
    #universal-search { 
        width: 100%; padding: 15px 45px; border-radius: 30px; 
        border: 1px solid #ddd; font-size: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    .search-icon { position: absolute; left: 15px; top: 15px; color: #888; }
    #search-results { 
        position: absolute; width: 100%; background: white; 
        z-index: 100; border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        max-height: 300px; overflow-y: auto; display: none; margin-top: 5px;
    }
    .search-result-item { 
        display: block; padding: 12px 20px; text-decoration: none; color: #333; 
        border-bottom: 1px solid #eee; transition: 0.2s;
    }
    .search-result-item:hover { background: #f0f7ff; color: #007bff; }
    .search-result-item span { font-size: 0.8rem; color: #777; display: block; }
</style>

<div class="search-container">
    <span class="search-icon">🔍</span>
    <input type="text" id="universal-search" placeholder="Search students, admissions, classes, or units..." autocomplete="off">
    <div id="search-results"></div>
</div>

<script>
const searchInput = document.getElementById('universal-search');
const resultsBox = document.getElementById('search-results');

searchInput.addEventListener('input', function() {
    const q = this.value;
    if (q.length < 2) {
        resultsBox.style.display = 'none';
        return;
    }

    fetch(`search_action.php?q=${encodeURIComponent(q)}`)
        .then(response => response.text())
        .then(data => {
            resultsBox.innerHTML = data;
            resultsBox.style.display = 'block';
        });
});

// Close results when clicking outside
document.addEventListener('click', (e) => {
    if (!searchInput.contains(e.target)) resultsBox.style.display = 'none';
});
</script>

    <div class="dev-footer">
        <p style="margin:0; color:#555;">System working perfectly on Neon PostgreSQL.</p>
        <p style="font-weight:bold; margin:5px 0;">Developer Support:</p>
        <p style="margin:0; font-size: 1.1em; color: #333;">Dantech74</p>
        <a href="https://wa.me/254790435584?text=Hello%20Dantech74,%20I%20need%20assistance%20with%20the%20E-Portfolio%20system." class="whatsapp-btn" target="_blank">
            💬 Chat on WhatsApp
        </a>
    </div>
</div>

</body>
</html>