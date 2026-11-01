<?php
// produce catalogue.php - Produce Catalogue with Search & Filter
session_start();
require_once 'db.php'; // Includes your PDO MySQL connection ($pdo)

// Retrieve search and category filter inputs
$search = trim($_GET['search'] ?? '');
$category_id = trim($_GET['category'] ?? '');

try {
    // 1. Fetch categories for the filter dropdown menu
    $catStmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
    $categories = $catStmt->fetchAll();

    // 2. Build dynamic SQL query for approved & active produce listings
    $query = "
        SELECT 
            p.*, 
            c.name AS category_name, 
            u.name AS farmer_name 
        FROM produce p 
        JOIN categories c ON p.category_id = c.id 
        JOIN users u ON p.farmer_id = u.id 
        WHERE p.approved = 1 AND p.removed = 0
    ";
    $params = [];

    // Filter by produce name or farm location
    if (!empty($search)) {
        $query .= " AND (p.name LIKE ? OR p.location LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    // Filter by specific category
    if (!empty($category_id)) {
        $query .= " AND p.category_id = ?";
        $params[] = $category_id;
    }

    $query .= " ORDER BY p.created_at DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $products = $stmt->fetchAll();

} catch (PDOException $e) {
    $products = [];
    $categories = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgriMarket - Produce Catalogue</title>
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
        .page-title { color: #1b5e20; margin-bottom: 0.5rem; }
        .subtitle { color: #666; margin-bottom: 1.5rem; }

        /* Search & Filter Form */
        .filter-card { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.08); margin-bottom: 2rem; }
        .filter-form { display: flex; gap: 1rem; flex-wrap: wrap; }
        .filter-form input, .filter-form select, .filter-form button { padding: 0.75rem; font-size: 1rem; border: 1px solid #ccc; border-radius: 4px; }
        .filter-form input { flex: 2; min-width: 220px; }
        .filter-form select { flex: 1; min-width: 180px; }
        .filter-form button { background: #2e7d32; color: white; border: none; cursor: pointer; font-weight: bold; min-width: 100px; }
        .filter-form button:hover { background: #1b5e20; }
        .reset-link { align-self: center; color: #d32f2f; text-decoration: none; font-size: 0.9rem; font-weight: bold; }

        /* Grid & Cards */
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1.5rem; }
        .card { background: white; border-radius: 8px; padding: 1.5rem; box-shadow: 0 2px 5px rgba(0,0,0,0.1); display: flex; flex-direction: column; justify-content: space-between; }
        .card h3 { color: #2e7d32; margin-bottom: 0.5rem; }
        .card p { color: #555; font-size: 0.9rem; margin-bottom: 0.3rem; }
        .card .price { font-size: 1.2rem; font-weight: bold; color: #388e3c; margin: 0.75rem 0; }

        /* Buttons */
        .btn { background: #2e7d32; color: white; border: none; padding: 0.6rem; text-align: center; border-radius: 4px; cursor: pointer; text-decoration: none; display: block; font-weight: bold; }
        .btn:hover { background: #1b5e20; }

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
        <h2 class="page-title">Produce Catalogue</h2>
        <p class="subtitle">Browse fresh agricultural produce directly from registered smallholder farmers[cite: 2].</p>

        <!-- Search and Category Filter Section -->
        <div class="filter-card">
            <form method="GET" action="produce catalogue.php" class="filter-form">
                <input type="text" name="search" placeholder="Search by produce name or location..." value="<?= htmlspecialchars($search) ?>">
                <select name="category">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($category_id == $cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Filter</button>
                <?php if (!empty($search) || !empty($category_id)): ?>
                    <a href="produce catalogue.php" class="reset-link">Clear Filters</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Catalogue Grid Display -->
        <div class="grid">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="card">
                        <div>
                            <h3><?= htmlspecialchars($product['name']) ?></h3>
                            <p><strong>Category:</strong> <?= htmlspecialchars($product['category_name']) ?></p>
                            <p><strong>Farmer:</strong> <a href="farmer_profile.php?id=<?= $product['farmer_id'] ?>" style="color: #2e7d32; font-weight: bold; text-decoration: none;"><?= htmlspecialchars($product['farmer_name']) ?></a></p>
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
                <div style="grid-column: 1 / -1; background: white; padding: 3rem; text-align: center; border-radius: 8px;">
                    <p style="color: #666; font-size: 1.1rem;">No produce found matching your search criteria.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; <?= date('Y') ?> AgriMarket System. All rights reserved.</p>
    </footer>

</body>
</html>