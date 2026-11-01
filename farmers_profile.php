<?php
// farmer_profile.php - Public View for Registered Farmer Details and Listings
session_start();
require_once 'db.php'; // Includes your PDO MySQL connection ($pdo)

// Retrieve and validate farmer ID from URL parameter
$farmerId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$farmerId) {
    header("Location: produce catalogue.php");
    exit;
}

try {
    // 1. Fetch farmer details
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'farmer' AND approved = 1");
    $stmt->execute([$farmerId]);
    $farmer = $stmt->fetch();

    if (!$farmer) {
        $error = "Farmer not found or account is not active.";
    } else {
        // 2. Fetch all active and approved produce listings by this farmer
        $prodStmt = $pdo->prepare("
            SELECT p.*, c.name AS category_name 
            FROM produce p 
            JOIN categories c ON p.category_id = c.id 
            WHERE p.farmer_id = ? AND p.approved = 1 AND p.removed = 0 
            ORDER BY p.created_at DESC
        ");
        $prodStmt->execute([$farmerId]);
        $products = $prodStmt->fetchAll();
    }
} catch (PDOException $e) {
    $error = "Database error occurred.";
    $farmer = null;
    $products = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $farmer ? htmlspecialchars($farmer['name']) . ' - Farmer Profile' : 'Farmer Not Found' ?> - AgriMarket</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f4f7f6; color: #333; line-height: 1.6; }

        /* Navigation Bar */
        header { background: #2e7d32; color: white; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        header h1 { font-size: 1.5rem; }
        nav a { color: white; text-decoration: none; margin-left: 1.5rem; font-weight: 500; }
        nav a:hover { text-decoration: underline; }

        /* Container Layout */
        .container { max-width: 1100px; margin: 2rem auto; padding: 0 1rem; }

        /* Farmer Profile Header Card */
        .profile-card { background: white; padding: 2.5rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 2rem; border-left: 6px solid #2e7d32; }
        .profile-card h2 { color: #1b5e20; margin-bottom: 0.5rem; font-size: 2rem; }
        .profile-meta { display: flex; gap: 2rem; margin-top: 1rem; flex-wrap: wrap; color: #555; font-size: 0.95rem; }
        .profile-meta div { background: #e8f5e9; padding: 0.5rem 1rem; border-radius: 4px; }
        .bio-section { margin-top: 1.5rem; color: #444; border-top: 1px solid #eee; padding-top: 1rem; }

        /* Produce Grid */
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1.5rem; margin-top: 1rem; }
        .card { background: white; border-radius: 8px; padding: 1.5rem; box-shadow: 0 2px 5px rgba(0,0,0,0.1); display: flex; flex-direction: column; justify-content: space-between; }
        .card h3 { color: #2e7d32; margin-bottom: 0.5rem; }
        .card p { color: #555; font-size: 0.9rem; margin-bottom: 0.3rem; }
        .card .price { font-size: 1.2rem; font-weight: bold; color: #388e3c; margin: 0.75rem 0; }

        /* Buttons */
        .btn { background: #2e7d32; color: white; border: none; padding: 0.6rem; text-align: center; border-radius: 4px; cursor: pointer; text-decoration: none; display: block; font-weight: bold; }
        .btn:hover { background: #1b5e20; }
        .back-link { display: inline-block; margin-bottom: 1rem; color: #2e7d32; text-decoration: none; font-weight: bold; }
        .back-link:hover { text-decoration: underline; }

        /* Footer */
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
            <?php if (isset($_SESSION['user'])): ?>
                <a href="dashboard.php">Dashboard</a>
                <a href="logout.php">Logout (<?= htmlspecialchars($_SESSION['user']['name']) ?>)</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </nav>
    </header>

    <div class="container">
        <a href="produce catalogue.php" class="back-link">&larr; Back to Produce Catalogue</a>

        <?php if ($farmer): ?>
            <!-- Farmer Details Card -->
            <div class="profile-card">
                <h2>👨‍🌾 <?= htmlspecialchars($farmer['name']) ?></h2>
                <div class="profile-meta">
                    <div>📍 <strong>Location:</strong> <?= htmlspecialchars($farmer['location'] ?? 'Not specified') ?></div>
                    <div>📞 <strong>Phone:</strong> <?= htmlspecialchars($farmer['phone'] ?? 'Contact via platform') ?></div>
                    <div>✉️ <strong>Email:</strong> <?= htmlspecialchars($farmer['email']) ?></div>
                </div>
                <?php if (!empty($farmer['bio'])): ?>
                    <div class="bio-section">
                        <p><strong>About the Farmer:</strong></p>
                        <p style="margin-top: 0.3rem;"><?= htmlspecialchars($farmer['bio']) ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Active Produce Listings Section -->
            <h3 style="color: #1b5e20; margin-bottom: 0.5rem;">Produce Listings by <?= htmlspecialchars($farmer['name']) ?></h3>
            <p style="color: #666; margin-bottom: 1.5rem;">Explore available fresh produce listed directly from this grower's farm.</p>

            <div class="grid">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="card">
                            <div>
                                <h3><?= htmlspecialchars($product['name']) ?></h3>
                                <p><strong>Category:</strong> <?= htmlspecialchars($product['category_name']) ?></p>
                                <p><strong>Location:</strong> <?= htmlspecialchars($product['location']) ?></p>
                                <p><strong>Available Stock:</strong> <?= htmlspecialchars($product['quantity']) ?> <?= htmlspecialchars($product['unit']) ?></p>
                                <?php if (!empty($product['description'])): ?>
                                    <p style="margin-top: 0.5rem; font-style: italic; color: #777;"><?= htmlspecialchars($product['description']) ?></p>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div class="price">KSh <?= number_format($product['price'], 2) ?> / <?= htmlspecialchars($product['unit']) ?></div>
                                <a href="order.php?produce_id=<?= $product['id'] ?>" class="btn">Place Order</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="grid-column: 1 / -1; background: white; padding: 2.5rem; text-align: center; border-radius: 8px;">
                        <p style="color: #666;">This farmer currently has no active produce listings available.</p>
                    </div>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <!-- Error State if Farmer is Missing -->
            <div style="background: white; padding: 3rem; text-align: center; border-radius: 8px;">
                <h3 style="color: #d32f2f; margin-bottom: 0.5rem;">Farmer Profile Not Found</h3>
                <p style="color: #666; margin-bottom: 1.5rem;"><?= htmlspecialchars($error ?? 'The requested farmer does not exist or has been removed.') ?></p>
                <a href="produce catalogue.php" class="btn" style="display: inline-block; padding: 0.75rem 1.5rem;">Return to Produce Catalog</a>
            </div>
        <?php endif; ?>

    </div>

    <footer>
        <p>&copy; <?= date('Y') ?> AgriMarket System. All rights reserved.</p>
    </footer>

</body>
</html>