<?php
// dashboard.php - User Portal Dashboard
session_start();
require_once 'db.php'; // Includes your PDO MySQL connection ($pdo)

// Protect page: user must be logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$role = $user['role'];

// Fetch role-specific statistics
$stats = [];
$recentItems = [];

try {
    if ($role === 'farmer') {
        // Count active produce listed by this farmer
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM produce WHERE farmer_id = ? AND removed = 0");
        $stmt->execute([$user['id']]);
        $stats['produce_count'] = $stmt->fetchColumn();

        // Recent listings
        $prodStmt = $pdo->prepare("SELECT * FROM produce WHERE farmer_id = ? AND removed = 0 ORDER BY created_at DESC LIMIT 5");
        $prodStmt->execute([$user['id']]);
        $recentItems = $prodStmt->fetchAll();

    } elseif ($role === 'admin') {
        // Admin stats
        $stats['farmers'] = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'farmer'")->fetchColumn();
        $stats['buyers'] = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'buyer'")->fetchColumn();
        $stats['produce'] = $pdo->query("SELECT COUNT(*) FROM produce WHERE removed = 0")->fetchColumn();
        $stats['pending_farmers'] = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'farmer' AND approved = 0")->fetchColumn();
    }
} catch (PDOException $e) {
    // Silently handle if tables are empty or being initialized
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgriMarket - Dashboard</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f4f7f6; color: #333; line-height: 1.6; }

        header { background: #2e7d32; color: white; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        header h1 { font-size: 1.5rem; }
        nav a { color: white; text-decoration: none; margin-left: 1.5rem; font-weight: 500; }
        nav a:hover { text-decoration: underline; }

        .container { max-width: 1000px; margin: 2rem auto; padding: 0 1rem; }
        .welcome-card { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 2rem; border-left: 6px solid #2e7d32; }
        .welcome-card h2 { color: #1b5e20; margin-bottom: 0.3rem; }
        .welcome-card p { color: #666; }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-box { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.08); text-align: center; }
        .stat-box h3 { font-size: 2rem; color: #2e7d32; }
        .stat-box p { color: #666; font-size: 0.9rem; text-transform: uppercase; }

        .card { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.08); margin-bottom: 2rem; }
        .card h3 { color: #1b5e20; margin-bottom: 1rem; border-bottom: 1px solid #eee; padding-bottom: 0.5rem; }

        .btn { background: #2e7d32; color: white; border: none; padding: 0.6rem 1.2rem; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; font-weight: bold; }
        .btn:hover { background: #1b5e20; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        table th, table td { text-align: left; padding: 0.75rem; border-bottom: 1px solid #eee; }
        table th { background-color: #f8f9fa; color: #555; }

        footer { text-align: center; padding: 2rem; margin-top: 3rem; background: #e0e0e0; color: #555; font-size: 0.9rem; }
    </style>
</head>
<body>

    <header>
        <h1>🌾 AgriMarket Dashboard</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="produce catalogue.php">Produce Catalogue</a>
            <a href="manage_profile.php">Profile</a>
            <a href="logout.php">Logout (<?= htmlspecialchars($user['name']) ?>)</a>
        </nav>
    </header>

    <div class="container">
        <!-- Welcome Banner -->
        <div class="welcome-card">
            <h2>Welcome, <?= htmlspecialchars($user['name']) ?>!</h2>
            <p>Logged in as: <strong><?= ucfirst(htmlspecialchars($role)) ?></strong> | Location: <?= htmlspecialchars($user['location'] ?: 'Not specified') ?></p>
        </div>

        <!-- Role-based Dashboard Views -->
        <?php if ($role === 'farmer'): ?>

            <div class="stats-grid">
                <div class="stat-box">
                    <h3><?= number_format($stats['produce_count'] ?? 0) ?></h3>
                    <p>Your Listed Produce</p>
                </div>
            </div>

            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3>Your Produce Listings</h3>
                    <a href="produce catalogue.php" class="btn">+ Add New Produce</a>
                </div>

                <?php if (!empty($recentItems)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentItems as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['name']) ?></td>
                                    <td>KSh <?= number_format($item['price'], 2) ?></td>
                                    <td><?= htmlspecialchars($item['quantity']) ?> <?= htmlspecialchars($item['unit']) ?></td>
                                    <td>
                                        <?php if ($item['approved']): ?>
                                            <span style="color: green; font-weight: bold;">Approved</span>
                                        <?php else: ?>
                                            <span style="color: orange; font-weight: bold;">Pending Approval</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="color: #666;">You haven't listed any produce yet.</p>
                <?php endif; ?>
            </div>

        <?php elseif ($role === 'buyer'): ?>

            <div class="card">
                <h3>Buyer Options</h3>
                <p style="margin-bottom: 1.5rem; color: #555;">Browse available fresh produce from registered farmers across the region.</p>
                <a href="produce catalogue.php" class="btn">Explore Produce Catalogue</a>
                <a href="manage_profile.php" class="btn" style="background: #555; margin-left: 0.5rem;">Update Account Details</a>
            </div>

        <?php elseif ($role === 'admin'): ?>

            <div class="stats-grid">
                <div class="stat-box">
                    <h3><?= number_format($stats['farmers'] ?? 0) ?></h3>
                    <p>Total Farmers</p>
                </div>
                <div class="stat-box">
                    <h3><?= number_format($stats['buyers'] ?? 0) ?></h3>
                    <p>Total Buyers</p>
                </div>
                <div class="stat-box">
                    <h3><?= number_format($stats['produce'] ?? 0) ?></h3>
                    <p>Total Produce</p>
                </div>
                <div class="stat-box">
                    <h3 style="color: #d32f2f;"><?= number_format($stats['pending_farmers'] ?? 0) ?></h3>
                    <p>Pending Approvals</p>
                </div>
            </div>

        <?php endif; ?>

    </div>

    <footer>
        <p>&copy; <?= date('Y') ?> AgriMarket System. All rights reserved.</p>
    </footer>

</body>
</html>