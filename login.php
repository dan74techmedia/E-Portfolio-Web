<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Login | E-Portfolio</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f0f2f5; display: flex; flex-direction: column; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .header-area { text-align: center; margin-bottom: 20px; }
        .card { background: white; padding: 2.5rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 90%; max-width: 380px; }
        h2 { text-align: center; color: #1c1e21; margin-bottom: 1.5rem; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; font-size: 16px; }
        button { width: 100%; padding: 12px; background-color: #28a745; color: white; border: none; border-radius: 6px; font-size: 16px; font-weight: bold; cursor: pointer; margin-top: 10px; }
        button:hover { background-color: #218838; }
        .alert { padding: 12px; border-radius: 6px; margin-bottom: 15px; text-align: center; font-size: 14px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .links { text-align: center; margin-top: 20px; font-size: 14px; color: #606770; }
        a { color: #007bff; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="header-area">
    <h1>SCHOOL E-PORTFOLIO</h1>
</div>

<div class="card">
    <h2>Trainer Login</h2>

    <!-- Feedback Messages -->
    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'account_created'): ?>
        <div class="alert alert-success">Account created! Please log in.</div>
    <?php endif; ?>

    <?php if(isset($_GET['error']) && $_GET['error'] == 'invalid'): ?>
        <div class="alert alert-error">Invalid email or password.</div>
    <?php endif; ?>

    <form action="login_action.php" method="POST">
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login to Dashboard</button>
    </form>

    <div class="links">
        Don't have an account? <a href="signup.php">Sign Up</a>
    </div>
</div>

</body>
</html>