<?php
// contact.php - Contact Us & Support Page (AgriMarket)
session_start();
require_once 'db.php'; // Includes PDO database connection ($pdo)

$success = '';
$error = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        $error = "Please fill in all required fields (Name, Email, and Message).";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please provide a valid email address.";
    } else {
        try {
            // Save message to database using prepared statements (prevents SQL injection)
            $stmt = $pdo->prepare("
                INSERT INTO contact_messages (name, email, subject, message, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$name, $email, $subject, $message]);

            $success = "Thank you for reaching out, " . htmlspecialchars($name) . "! Your message has been received successfully. Our support team will get back to you shortly.";
        } catch (PDOException $e) {
            $error = "A database error occurred while sending your message. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgriMarket - Contact Us</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f4f7f6; color: #333; line-height: 1.6; }

        /* Navigation Header */
        header { background: #2e7d32; color: white; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
        header h1 { font-size: 1.5rem; }
        nav { display: flex; gap: 1.5rem; align-items: center; flex-wrap: wrap; }
        nav a { color: white; text-decoration: none; font-weight: 500; font-size: 0.95rem; }
        nav a:hover { text-decoration: underline; }

        /* Container Layout */
        .container { max-width: 1000px; margin: 2.5rem auto; padding: 0 1rem; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; }

        @media (max-width: 768px) {
            .grid { grid-template-columns: 1fr; }
        }

        /* Card Styles */
        .card { background: white; padding: 2.5rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .card h2 { color: #1b5e20; margin-bottom: 0.5rem; font-size: 1.8rem; border-bottom: 2px solid #e8f5e9; padding-bottom: 0.5rem; }
        .card p { color: #555; margin-bottom: 1.5rem; font-size: 0.95rem; }

        /* Form Controls */
        .form-group { margin-bottom: 1.25rem; }
        .form-group label { display: block; margin-bottom: 0.4rem; font-weight: bold; color: #444; font-size: 0.9rem; }
        .form-group input, .form-group textarea { width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 4px; font-size: 1rem; }
        .form-group input:focus, .form-group textarea:focus { outline: none; border-color: #2e7d32; box-shadow: 0 0 4px rgba(46,125,50,0.3); }

        /* Alerts & Buttons */
        .alert-error { background: #ffebee; color: #c62828; padding: 0.8rem; border-radius: 4px; border: 1px solid #ef5350; margin-bottom: 1.25rem; font-size: 0.9rem; text-align: center; }
        .alert-success { background: #e8f5e9; color: #2e7d32; padding: 0.8rem; border-radius: 4px; border: 1px solid #4caf50; margin-bottom: 1.25rem; font-size: 0.9rem; text-align: center; }

        .btn { width: 100%; background: #2e7d32; color: white; border: none; padding: 0.8rem; border-radius: 4px; font-size: 1rem; font-weight: bold; cursor: pointer; transition: background 0.2s; }
        .btn:hover { background: #1b5e20; }

        /* Contact Details */
        .contact-info { display: flex; flex-direction: column; gap: 1.2rem; margin-top: 1.5rem; }
        .info-item { display: flex; align-items: flex-start; gap: 0.75rem; }
        .info-item div h4 { color: #1b5e20; font-size: 1rem; }
        .info-item div p { margin-bottom: 0; color: #666; font-size: 0.9rem; }

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
            <a href="produce catalogue.php">Produce Catalog</a>
            <a href="contact.php" style="text-decoration: underline;">Contact</a>
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
        <div class="grid">
            
            <!-- Contact Form -->
            <div class="card">
                <h2>Send Us a Message</h2>
                <p>Have questions regarding crop listings, orders, or account approval? Reach out to our team.</p>

                <?php if (!empty($error)): ?>
                    <div class="alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <form method="POST" action="contact.php">
                    <div class="form-group">
                        <label for="name">Your Name *</label>
                        <input type="text" id="name" name="name" required placeholder="e.g. John Doe">
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" required placeholder="e.g. john@example.com">
                    </div>

                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" placeholder="e.g. Farmer Approval / Bulk Order Inquiry">
                    </div>

                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" rows="5" required placeholder="Type your message or inquiry here..."></textarea>
                    </div>

                    <button type="submit" class="btn">Send Message</button>
                </form>
            </div>

            <!-- Direct Contact Details -->
            <div class="card">
                <h2>Support & Headquarters</h2>
                <p>We are available to assist agricultural producers and registered buyers across the region.</p>

                <div class="contact-info">
                    <div class="info-item">
                        <span style="font-size: 1.5rem;">📍</span>
                        <div>
                            <h4>Office Location</h4>
                            <p>AgriMarket Support Hub, Nairobi, Kenya</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <span style="font-size: 1.5rem;">📞</span>
                        <div>
                            <h4>Phone Support</h4>
                            <p>+254 700 000 000 / +254 711 000 000</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <span style="font-size: 1.5rem;">✉️</span>
                        <div>
                            <h4>Official Email</h4>
                            <p>support@agrimarket.co.ke</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <span style="font-size: 1.5rem;">⏰</span>
                        <div>
                            <h4>Working Hours</h4>
                            <p>Monday – Friday: 8:00 AM – 5:00 PM<br>Saturday: 8:00 AM – 1:00 PM</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <footer>
        <p>&copy; <?= date('Y') ?> AgriMarket System. All rights reserved.</p>
    </footer>

</body>
</html>