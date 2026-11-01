<?php
// register.php - User Registration Page
session_start();
require_once 'db.php'; // Includes your PDO MySQL connection ($pdo)

// Redirect if already logged in
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? 'buyer';
    $phone    = trim($_POST['phone'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $bio      = trim($_POST['bio'] ?? '');

    if (empty($name) || empty($email) || empty($password)) {
        $error = "Name, Email, and Password are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = "Email address is already registered.";
            } else {
                // Securely hash the password
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                // Farmers default to pending approval (0), Buyers are active (1)
                $approved = ($role === 'farmer') ? 0 : 1;

                $insertStmt = $pdo->prepare("
                    INSERT INTO users (name, email, password, role, phone, location, bio, approved, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ");
                $insertStmt->execute([$name, $email, $hashedPassword, $role, $phone, $location, $bio, $approved]);

                if ($role === 'farmer') {
                    $success = "Registration successful! Your farmer account is pending administrator approval before you can log in.";
                } else {
                    $success = "Registration successful! You can now log in to your account.";
                }
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgriMarket - Register</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f4f7f6; color: #333; line-height: 1.6; }

        header { background: #2e7d32; color: white; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        header h1 { font-size: 1.5rem; }
        nav a { color: white; text-decoration: none; margin-left: 1.5rem; font-weight: 500; }
        nav a:hover { text-decoration: underline; }

        .container { max-width: 550px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: white; padding: 2.5rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .card h2 { color: #1b5e20; margin-bottom: 0.5rem; text-align: center; font-size: 1.8rem; }
        .card p.subtitle { text-align: center; color: #666; font-size: 0.95rem; margin-bottom: 1.5rem; }

        .form-group { margin-bottom: 1.25rem; }
        .form-group label { display: block; margin-bottom: 0.4rem; font-weight: bold; color: #444; font-size: 0.9rem; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 4px; font-size: 1rem; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #2e7d32; box-shadow: 0 0 4px rgba(46,125,50,0.3); }

        .alert-error { background: #ffebee; color: #c62828; padding: 0.8rem; border-radius: 4px; border: 1px solid #ef5350; margin-bottom: 1.25rem; font-size: 0.9rem; text-align: center; }
        .alert-success { background: #e8f5e9; color: #2e7d32; padding: 0.8rem; border-radius: 4px; border: 1px solid #4caf50; margin-bottom: 1.25rem; font-size: 0.9rem; text-align: center; }

        .btn { width: 100%; background: #2e7d32; color: white; border: none; padding: 0.8rem; border-radius: 4px; font-size: 1rem; font-weight: bold; cursor: pointer; transition: background 0.2s; }
        .btn:hover { background: #1b5e20; }

        .auth-footer { text-align: center; margin-top: 1.5rem; font-size: 0.9rem; color: #666; }
        .auth-footer a { color: #2e7d32; text-decoration: none; font-weight: bold; }
        footer { text-align: center; padding: 2rem; margin-top: 3rem; background: #e0e0e0; color: #555; font-size: 0.9rem; }
    </style>
</head>
<body>

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
            <h2>Create Account</h2>
            <p class="subtitle">Join as a Farmer or Buyer on AgriMarket</p>

            <?php if (!empty($error)): ?>
                <div class="alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="POST" action="register.php">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" required placeholder="e.g. John Doe">
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="e.g. john@example.com">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Create a secure password">
                </div>

                <div class="form-group">
                    <label for="role">Register As</label>
                    <select id="role" name="role" required>
                        <option value="buyer">Buyer (Institutions, Exporters, Retailers)</option>
                        <option value="farmer">Farmer (Producer / Grower)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" placeholder="e.g. +254 700 000000">
                </div>

                <div class="form-group">
                    <label for="location">Location / Region</label>
                    <input type="text" id="location" name="location" placeholder="e.g. Nakuru, Eldoret, Nairobi">
                </div>

                <div class="form-group">
                    <label for="bio">Short Description / Bio (Optional)</label>
                    <textarea id="bio" name="bio" rows="3" placeholder="Tell buyers about your farm or organization..."></textarea>
                </div>

                <button type="submit" class="btn">Register Account</button>
            </form>

            <div class="auth-footer">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; <?= date('Y') ?> AgriMarket System. All rights reserved.</p>
    </footer>

</body>
</html>