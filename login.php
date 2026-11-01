<?php
// login.php - User Authentication Page (AgriMarket)
session_start();
require_once 'db.php'; // Includes your PDO MySQL connection ($pdo)

// Redirect user to dashboard if already logged in
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
$email = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error = "Please enter both your email address and password.";
    } else {
        try {
            // Retrieve user record by email
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Check if farmer account is still pending approval
                if ($user['role'] === 'farmer' && isset($user['approved']) && $user['approved'] == 0) {
                    $error = "Your farmer account is currently pending administrator approval.";
                } else {
                    // Set session variables (excluding sensitive password hash)
                    $_SESSION['user'] = [
                        'id'       => $user['id'],
                        'name'     => $user['name'],
                        'email'    => $user['email'],
                        'role'     => $user['role'],
                        'location' => $user['location'] ?? ''
                    ];

                    header("Location: dashboard.php");
                    exit;
                }
            } else {
                $error = "Invalid email address or password.";
            }
        } catch (PDOException $e) {
            $error = "A database error occurred. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgriMarket - Login</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f4f7f6; color: #333; line-height: 1.6; }

        /* Navigation Header */
        header { background: #2e7d32; color: white; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        header h1 { font-size: 1.5rem; }
        nav a { color: white; text-decoration: none; margin-left: 1.5rem; font-weight: 500; }
        nav a:hover { text-decoration: underline; }

        /* Form Container */
        .container { max-width: 450px; margin: 3rem auto; padding: 0 1rem; }
        .card { background: white; padding: 2.5rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .card h2 { color: #1b5e20; margin-bottom: 0.5rem; text-align: center; font-size: 1.8rem; }
        .card p.subtitle { text-align: center; color: #666; font-size: 0.95rem; margin-bottom: 1.5rem; }

        /* Form Controls */
        .form-group { margin-bottom: 1.25rem; }
        .form-group label { display: block; margin-bottom: 0.4rem; font-weight: bold; color: #444; font-size: 0.9rem; }
        .form-group input { width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 4px; font-size: 1rem; }
        .form-group input:focus { outline: none; border-color: #2e7d32; box-shadow: 0 0 4px rgba(46,125,50,0.3); }

        /* Alerts and Buttons */
        .alert-error { background: #ffebee; color: #c62828; padding: 0.8rem; border-radius: 4px; border: 1px solid #ef5350; margin-bottom: 1.25rem; font-size: 0.9rem; text-align: center; }
        .btn { width: 100%; background: #2e7d32; color: white; border: none; padding: 0.8rem; border-radius: 4px; font-size: 1rem; font-weight: bold; cursor: pointer; transition: background 0.2s; }
        .btn:hover { background: #1b5e20; }

        /* Links */
        .auth-footer { text-align: center; margin-top: 1.5rem; font-size: 0.9rem; color: #666; }
        .auth-footer a { color: #2e7d32; text-decoration: none; font-weight: bold; }
        .auth-footer a:hover { text-decoration: underline; }

        /* Page Footer */
        footer { text-align: center; padding: 2rem; margin-top: 3rem; background: #e0e0e0; color: #555; font-size: 0.9rem; }
    </style>
</head>
<body>

    <!-- Header Navigation -->
    <header>
        <h1>🌾 AgriMarket</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="produce catalogue.php">Produce Catalogue</a>
            <a href="about.php">About Us</a>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        </nav>
    </header>

    <div class="container">
        <div class="card">
            <h2>Sign In</h2>
            <p class="subtitle">Access your Farmer or Buyer dashboard</p>

            <?php if (!empty($error)): ?>
                <div class="alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="e.g. buyer@example.com" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>

                <button type="submit" class="btn">Log In</button>
            </form>

            <div class="auth-footer">
                Don't have an account yet? <a href="register.php">Register here</a>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; <?= date('Y') ?> AgriMarket System. All rights reserved.</p>
    </footer>

</body>
</html>