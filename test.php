<?php
// test.php - Test MySQL Database Connection and Queries

// 1. Include your connection configuration
require_once 'db.php'; 

try {
    // 2. Output successful connection status
    echo "<div style='font-family: Arial, sans-serif; padding: 20px; background-color: #e8f5e9; border: 1px solid #4caf50; border-radius: 5px; color: #2e7d32;'>";
    echo "<h2>✅ Database Connection Successful!</h2>";
    echo "<p>Connected to database: <strong>agribusiness_db</strong> on <strong>localhost</strong></p>";

    // 3. Query existing tables inside agribusiness_db
    $query = $pdo->query("SHOW TABLES");
    $tables = $query->fetchAll(PDO::FETCH_COLUMN);

    if (count($tables) > 0) {
        echo "<h3>Existing Tables in `agribusiness_db`:</h3><ul>";
        foreach ($tables as $table) {
            echo "<li>" . htmlspecialchars($table) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color: #d32f2f;'>⚠️ Connected successfully, but no tables were found in <strong>agribusiness_db</strong>. Please import your schema in phpMyAdmin.</p>";
    }

    echo "</div>";

} catch (PDOException $e) {
    // Catch any execution errors
    echo "<div style='font-family: Arial, sans-serif; padding: 20px; background-color: #ffebee; border: 1px solid #ef5350; border-radius: 5px; color: #c62828;'>";
    echo "<h2>❌ Test Query Failed</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}
?>