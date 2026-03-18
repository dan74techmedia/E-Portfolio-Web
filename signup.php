<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Sign Up | E-Portfolio</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f0f2f5; display: flex; flex-direction: column; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .header-area { text-align: center; margin-bottom: 20px; }
        .card { background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 90%; max-width: 400px; }
        h2 { text-align: center; color: #1c1e21; margin-bottom: 1.5rem; }
        input { width: 100%; padding: 12px; margin: 8px 0; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; font-size: 16px; }
        button { width: 100%; padding: 12px; background-color: #007bff; color: white; border: none; border-radius: 6px; font-size: 16px; font-weight: bold; cursor: pointer; margin-top: 10px; }
        button:hover { background-color: #0056b3; }
        .error-msg { color: #d9534f; background: #f2dede; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center; font-size: 14px; }
    </style>
</head>
<body>

<div class="header-area">
    <h1>SCHOOL E-PORTFOLIO</h1>
    <p>Empowering Trainers with Digital Tracking</p>
</div>

<div class="card">
    <h2>Trainer Sign Up</h2>

    <?php if(isset($_GET['error'])): ?>
        <div class="error-msg">
            <?php 
                if($_GET['error'] == 'email_exists') echo "This email is already registered.";
                elseif($_GET['error'] == 'pass_mismatch') echo "Passwords do not match.";
                else echo "An unexpected error occurred.";
            ?>
        </div>
    <?php endif; ?>

    <form action="signup_action.php" method="POST">
        <input type="text" name="institution" placeholder="Institution Name" required>
        <input type="text" name="teacher_name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="text" name="phone" placeholder="Phone Number">
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <button type="submit">Create Account</button>
    </form>
    <p style="text-align:center; font-size: 14px; margin-top: 15px;">
        Already have an account? <a href="login.php">Login here</a>
    </p>
</div>

</body>
</html>