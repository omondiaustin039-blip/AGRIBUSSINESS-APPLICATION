<?php
// about.php - About Us Page (AgriMarket)
session_start();
require_once 'db.php'; // Includes your PDO MySQL connection ($pdo)

// Retrieve live platform statistics from MySQL database
try {
    $farmerCount = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'farmer' AND approved = 1")->fetchColumn();
    $produceCount = $pdo->query("SELECT COUNT(*) FROM produce WHERE approved = 1 AND removed = 0")->fetchColumn();
    $buyerCount = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'buyer'")->fetchColumn();
} catch (PDOException $e) {
    $farmerCount = 0;
    $produceCount = 0;
    $buyerCount = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgriMarket - About Us</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f4f7f6; color: #333; line-height: 1.6; }

        /* Navigation Header */
        header { background: #2e7d32; color: white; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        header h1 { font-size: 1.5rem; }
        nav a { color: white; text-decoration: none; margin-left: 1.5rem; font-weight: 500; }
        nav a:hover { text-decoration: underline; }

        /* Main Container */
        .container { max-width: 900px; margin: 2rem auto; padding: 0 1rem; }

        /* Card Section */
        .card { background: white; padding: 2.5rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 2rem; }
        .card h2 { color: #2e7d32; margin-bottom: 1rem; font-size: 1.8rem; border-bottom: 2px solid #e8f5e9; padding-bottom: 0.5rem; }
        .card p { margin-bottom: 1rem; color: #444; font-size: 1.05rem; }

        /* Key Objectives Grid */
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-top: 1.5rem; }
        .objective-card { background: #e8f5e9; padding: 1.5rem; border-radius: 6px; border-left: 4px solid #2e7d32; }
        .objective-card h3 { color: #1b5e20; margin-bottom: 0.5rem; font-size: 1.1rem; }
        .objective-card p { font-size: 0.95rem; color: #555; margin-bottom: 0; }

        /* Live Platform Stats */
        .stats-container { display: flex; justify-content: space-around; text-align: center; background: #2e7d32; color: white; padding: 1.5rem; border-radius: 8px; margin-top: 1rem; flex-wrap: wrap; gap: 1rem; }
        .stat-item h3 { font-size: 2rem; margin-bottom: 0.2rem; }
        .stat-item p { font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; }

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

        <!-- About Project Description -->
        <div class="card">
            <h2>About AgriMarket</h2>
            <p><strong>AgriMarket</strong> is an innovative agribusiness web platform designed to connect smallholder farmers directly with diverse market buyers—including local households, hotels, schools, institutions, and produce exporters[cite: 2].</p>
            <p>Historically, smallholder farmers face challenges accessing transparent market prices and reaching larger buyer networks without middleman markups. AgriMarket solves this by providing a reliable digital marketplace where farmers can showcase fresh produce, manage inventory, and receive direct buyer inquiries[cite: 2].</p>
            
            <!-- Live Database Stats -->
            <div class="stats-container">
                <div class="stat-item">
                    <h3><?= number_format($farmerCount) ?></h3>
                    <p>Registered Farmers</p>
                </div>
                <div class="stat-item">
                    <h3><?= number_format($produceCount) ?></h3>
                    <p>Active Produce Listings</p>
                </div>
                <div class="stat-item">
                    <h3><?= number_format($buyerCount) ?></h3>
                    <p>Market Buyers</p>
                </div>
            </div>
        </div>

        <!-- Project Objectives -->
        <div class="card">
            <h2>Our Core Goals</h2>
            <div class="grid">
                <div class="objective-card">
                    <h3>🌾 Farmer Empowerment</h3>
                    <p>Enables registered smallholder farmers to create listings, display transparent prices, and manage active crops directly[cite: 2].</p>
                </div>
                <div class="objective-card">
                    <h3> Direct Buyer Access</h3>
                    <p>Allows buyers to browse, search, and filter fresh agricultural produce by location and category with ease[cite: 2].</p>
                </div>
                <div class="objective-card">
                    <h3> Verified Approvals</h3>
                    <p>Built-in administrative workflow ensuring listed produce and registered farmers are approved and safe[cite: 2].</p>
                </div>
            </div>
        </div>

    </div>

    <footer>
        <p>&copy; <?= date('Y') ?> AgriMarket Web Application. Built for Smallholder Agribusiness Development[cite: 2].</p>
    </footer>

</body>
</html>