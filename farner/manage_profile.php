<?php
// manage_profile.php - Profile Management
session_start();
require_once 'db.php'; // Includes your PDO MySQL connection ($pdo)

// Protect page: user must be logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user']['id'];
$error = '';
$success = '';

// Fetch current user details from DB
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    die("Error fetching user data.");
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name'] ?? '');
    $phone       = trim($_POST['phone'] ?? '');
    $location    = trim($_POST['location'] ?? '');
    $bio         = trim($_POST['bio'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';

    if (empty($name)) {
        $error = "Full Name cannot be left empty.";
    } else {
        try {
            if (!empty($newPassword)) {
                $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                $updateStmt = $pdo->prepare("
                    UPDATE users 
                    SET name = ?, phone = ?, location = ?, bio = ?, password = ? 
                    WHERE id = ?
                ");
                $updateStmt->execute([$name, $phone, $location, $bio, $hashedPassword, $userId]);
            } else {
                $updateStmt = $pdo->prepare("
                    UPDATE users 
                    SET name = ?, phone = ?, location = ?, bio = ? 
                    WHERE id = ?
                ");
                $updateStmt->execute([$name, $phone, $location, $bio, $userId]);
            }

            // Update session data
            $_SESSION['user']['name'] = $name;
            $_SESSION['user']['location'] = $location;

            $success = "Profile updated successfully!";
            
            // Refresh user variable
            $stmt->execute([$userId]);
            $user = $stmt->fetch();

        } catch (PDOException $e) {
            $error = "Failed to update profile: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgriMarket - Manage Profile</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f4f7f6; color: #333; line-height: 1.6; }

        header { background: #2e7d32; color: white; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        header h1 { font-size: 1.5rem; }
        nav a { color: white; text-decoration: none; margin-left: 1.5rem; font-weight: 500; }
        nav a:hover { text-decoration: underline; }

        .container { max-width: 600px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: white; padding: 2.5rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .card h2 { color: #1b5e20; margin-bottom: 0.5rem; font-size: 1.8rem; }
        .card p.subtitle { color: #666; font-size: 0.95rem; margin-bottom: 1.5rem; }

        .form-group { margin-bottom: 1.25rem; }
        .form-group label { display: block; margin-bottom: 0.4rem; font-weight: bold; color: #444; font-size: 0.9rem; }
        .form-group input, .form-group textarea { width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 4px; font-size: 1rem; }
        .form-group input[disabled] { background-color: #e9ecef; cursor: not-allowed; }

        .alert-error { background: #ffebee; color: #c62828; padding: 0.8rem; border-radius: 4px; border: 1px solid #ef5350; margin-bottom: 1.25rem; font-size: 0.9rem; text-align: center; }
        .alert-success { background: #e8f5e9; color: #2e7d32; padding: 0.8rem; border-radius: 4px; border: 1px solid #4caf50; margin-bottom: 1.25rem; font-size: 0.9rem; text-align: center; }

        .btn { background: #2e7d32; color: white; border: none; padding: 0.8rem 1.5rem; border-radius: 4px; font-size: 1rem; font-weight: bold; cursor: pointer; }
        .btn:hover { background: #1b5e20; }
        .back-link { display: inline-block; margin-bottom: 1rem; color: #2e7d32; text-decoration: none; font-weight: bold; }
        footer { text-align: center; padding: 2rem; margin-top: 3rem; background: #e0e0e0; color: #555; font-size: 0.9rem; }
    </style>
</head>
<body>

    <header>
        <h1>🌾 AgriMarket</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="produce catalogue.php">Produce Catalogue</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="manage_profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="container">
        <a href="dashboard.php" class="back-link">&larr; Back to Dashboard</a>
        
        <div class="card">
            <h2>Manage Profile</h2>
            <p class="subtitle">Update your personal account details and preferences</p>

            <?php if (!empty($error)): ?>
                <div class="alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="POST" action="manage_profile.php">
                <div class="form-group">
                    <label>Email Address (Cannot be changed)</label>
                    <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                </div>

                <div class="form-group">
                    <label>Account Role</label>
                    <input type="text" value="<?= ucfirst(htmlspecialchars($user['role'])) ?>" disabled>
                </div>

                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="location">Location / Region</label>
                    <input type="text" id="location" name="location" value="<?= htmlspecialchars($user['location'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="bio">Bio / Farm Description</label>
                    <textarea id="bio" name="bio" rows="4"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                </div>

                <hr style="margin: 1.5rem 0; border: 0; border-top: 1px solid #eee;">

                <div class="form-group">
                    <label for="new_password">New Password (Leave blank to keep existing password)</label>
                    <input type="password" id="new_password" name="new_password" placeholder="Enter new password">
                </div>

                <button type="submit" class="btn">Save Changes</button>
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; <?= date('Y') ?> AgriMarket System. All rights reserved.</p>
    </footer>

</body>
</html>