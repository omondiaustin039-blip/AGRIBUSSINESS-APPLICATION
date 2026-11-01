<?php
// index.php - AgriMarket Responsive Web Application
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgriMarket - TVET CDACC Agribusiness Platform</title>
  <back page.jpg>
    <style>
        :root {
            --primary: #2e7d32;
            --primary-dark: #1b5e20;
            --accent: #4caf50;
            --bg-light: #f4f7f6;
            --text-main: #333;
            --text-muted: #666;
            --white: #ffffff;
            --shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: var(--bg-light); color: var(--text-main); line-height: 1.6; }

        /* Responsive Header & Navigation */
        header { background: var(--primary); color: var(--white); position: sticky; top: 0; z-index: 1000; box-shadow: 0 2px 6px rgba(0,0,0,0.15); }
        .nav-container { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; }
        .logo { font-size: 1.5rem; font-weight: bold; display: flex; align-items: center; gap: 0.5rem; cursor: pointer; }
        
        nav { display: flex; gap: 1.5rem; align-items: center; }
        nav a { color: var(--white); text-decoration: none; font-weight: 500; font-size: 0.95rem; transition: opacity 0.2s; cursor: pointer; }
        nav a:hover, nav a.active { text-decoration: underline; opacity: 0.85; }
        
        .menu-toggle { display: none; font-size: 1.5rem; cursor: pointer; background: none; border: none; color: var(--white); }

        /* Main Container */
        .main-container { max-width: 1200px; margin: 2rem auto; padding: 0 1.5rem; min-height: 70vh; }
        
        /* Sections */
        .section { display: none; }
        .section.active { display: block; animation: fadeIn 0.3s ease-in-out; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Hero Banner */
        .hero { background: linear-gradient(rgba(27,94,32,0.85), rgba(46,125,50,0.85)), url('https://images.unsplash.com/photo-1500937386664-56d1dfef3854?auto=format&fit=crop&w=1200&q=80') center/cover; color: var(--white); padding: 4rem 2rem; border-radius: 12px; text-align: center; margin-bottom: 2.5rem; box-shadow: var(--shadow); }
        .hero h1 { font-size: 2.5rem; margin-bottom: 1rem; }
        .hero p { font-size: 1.1rem; max-width: 700px; margin: 0 auto 1.5rem; opacity: 0.95; }
        .hero-btn { background: var(--accent); color: var(--white); padding: 0.75rem 2rem; border-radius: 6px; font-weight: bold; border: none; cursor: pointer; font-size: 1rem; transition: background 0.2s; }
        .hero-btn:hover { background: #388e3c; }

        /* Search & Filter Bar */
        .filter-bar { background: var(--white); padding: 1.25rem; border-radius: 8px; box-shadow: var(--shadow); display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 2rem; }
        .filter-bar input, .filter-bar select { flex: 1; min-width: 200px; padding: 0.75rem; border: 1px solid #ccc; border-radius: 6px; font-size: 0.95rem; }
        .filter-bar button { background: var(--primary); color: var(--white); border: none; padding: 0.75rem 1.5rem; border-radius: 6px; font-weight: bold; cursor: pointer; }
        .filter-bar button:hover { background: var(--primary-dark); }

        /* Catalog Grid */
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem; }
        .card { background: var(--white); border-radius: 8px; padding: 1.5rem; box-shadow: var(--shadow); display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid var(--accent); transition: transform 0.2s; }
        .card:hover { transform: translateY(-3px); }
        .card h3 { color: var(--primary-dark); margin-bottom: 0.5rem; font-size: 1.25rem; }
        .card p { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.4rem; }
        .price { font-size: 1.2rem; font-weight: bold; color: var(--primary); margin: 1rem 0; }
        .btn { background: var(--primary); color: var(--white); border: none; padding: 0.7rem; text-align: center; border-radius: 6px; cursor: pointer; text-decoration: none; display: block; font-weight: bold; font-size: 0.95rem; width: 100%; transition: background 0.2s; }
        .btn:hover { background: var(--primary-dark); }

        /* Dashboard & Stats */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-box { background: var(--white); padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow); text-align: center; }
        .stat-box h3 { font-size: 2rem; color: var(--primary); }
        .stat-box p { color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; font-weight: 600; }

        /* Form Layouts */
        .form-card { background: var(--white); padding: 2.5rem; border-radius: 8px; box-shadow: var(--shadow); max-width: 600px; margin: 0 auto; }
        .form-card h2 { color: var(--primary-dark); margin-bottom: 1.5rem; text-align: center; }
        .form-group { margin-bottom: 1.25rem; }
        .form-group label { display: block; margin-bottom: 0.4rem; font-weight: bold; font-size: 0.9rem; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 6px; font-size: 1rem; }

        /* Modal Popup */
        .modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 2000; justify-content: center; align-items: center; padding: 1rem; }
        .modal-content { background: var(--white); padding: 2rem; border-radius: 8px; max-width: 450px; width: 100%; box-shadow: var(--shadow); position: relative; }
        .close-modal { position: absolute; top: 1rem; right: 1rem; font-size: 1.2rem; cursor: pointer; border: none; background: none; font-weight: bold; }

        /* Footer */
        footer { text-align: center; padding: 2rem; margin-top: 4rem; background: #e0e0e0; color: #555; font-size: 0.9rem; }

        /* Responsive Breakpoints */
        @media (max-width: 768px) {
            nav { display: none; flex-direction: column; position: absolute; top: 100%; left: 0; right: 0; background: var(--primary); padding: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
            nav.show { display: flex; }
            .menu-toggle { display: block; }
            .hero h1 { font-size: 2rem; }
        }
    </style>
</head>
<body>

    <!-- Header & Navigation -->
    <header>
        <div class="nav-container">
            <div class="logo" onclick="switchSection('home')">🌾 AgriMarket</div>
            <button class="menu-toggle" onclick="toggleMenu()">☰</button>
            <nav id="navMenu">
                <a onclick="switchSection('home')" class="active" id="nav-home">Home</a>
                <a onclick="switchSection('catalog')" id="nav-catalog">Produce Catalog</a>
                <a onclick="switchSection('dashboard')" id="nav-dashboard">Dashboard</a>
                <a onclick="switchSection('contact')" id="nav-contact">Contact</a>
                <a onclick="switchSection('login')" id="nav-login" style="background: rgba(255,255,255,0.2); padding: 0.4rem 1rem; border-radius: 4px;">Sign In</a>
            </nav>
        </div>
    </header>

    <div class="main-container">

        <!-- 1. HOME SECTION -->
        <section id="home" class="section active">
            <div class="hero">
                <h1>Empowering Smallholder Farmers & Buyers</h1>
                <p>Direct marketplace connecting agricultural producers with regional institutional buyers, retailers, and exporters across Kenya.</p>
                <button class="hero-btn" onclick="switchSection('catalog')">Explore Produce Catalog</button>
            </div>

            <h2 style="color: var(--primary-dark); margin-bottom: 1.5rem; text-align: center;">Featured Agricultural Produce</h2>
            <div class="grid" id="featuredGrid">
                <!-- Dynamically populated via JS -->
            </div>
        </section>

        <!-- 2. CATALOG SECTION -->
        <section id="catalog" class="section">
            <h2 style="color: var(--primary-dark); margin-bottom: 0.5rem;">Produce Catalog</h2>
            <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Browse fresh, verified farm produce ready for direct order inquiry.</p>
            
            <div class="filter-bar">
                <input type="text" id="searchInput" placeholder="Search crop name or location..." onkeyup="filterCatalog()">
                <select id="categoryFilter" onchange="filterCatalog()">
                    <option value="">All Categories</option>
                    <option value="Grains">Grains & Cereals</option>
                    <option value="Vegetables">Vegetables</option>
                    <option value="Fruits">Fruits</option>
                    <option value="Tubers">Tubers & Roots</option>
                </select>
            </div>

            <div class="grid" id="catalogGrid">
                <!-- Dynamically populated via JS -->
            </div>
        </section>

        <!-- 3. DASHBOARD SECTION -->
        <section id="dashboard" class="section">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h2 style="color: var(--primary-dark);">User Portal Dashboard</h2>
                    <p style="color: var(--text-muted);">Manage your listings, orders, and account status.</p>
                </div>
                <div>
                    <select id="roleSwitcher" onchange="switchRole(this.value)" style="padding: 0.5rem; border-radius: 4px; font-weight: bold;">
                        <option value="farmer">Role: Farmer (Producer)</option>
                        <option value="buyer">Role: Buyer (Customer)</option>
                        <option value="admin">Role: Administrator</option>
                    </select>
                </div>
            </div>

            <!-- Farmer Dashboard View -->
            <div id="farmerDashboard" class="role-view">
                <div class="stats-grid">
                    <div class="stat-box"><h3 id="statListings">4</h3><p>Active Listings</p></div>
                    <div class="stat-box"><h3>KSh 84,500</h3><p>Estimated Revenue</p></div>
                    <div class="stat-box"><h3 style="color: var(--accent);">Approved</h3><p>Account Status</p></div>
                </div>

                <div class="form-card" style="max-width: 100%; margin-bottom: 2rem;">
                    <h3 style="color: var(--primary-dark); margin-bottom: 1rem;">+ Add New Produce Listing</h3>
                    <form onsubmit="addProduce(event)">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                            <div class="form-group"><label>Produce Name</label><input type="text" id="newProdName" required placeholder="e.g. Fresh Red Tomatoes"></div>
                            <div class="form-group"><label>Category</label><select id="newProdCat"><option value="Vegetables">Vegetables</option><option value="Grains">Grains</option><option value="Fruits">Fruits</option><option value="Tubers">Tubers</option></select></div>
                            <div class="form-group"><label>Price per Unit (KSh)</label><input type="number" id="newProdPrice" required placeholder="e.g. 120"></div>
                            <div class="form-group"><label>Quantity & Unit</label><input type="text" id="newProdQty" required placeholder="e.g. 500 Kg"></div>
                            <div class="form-group"><label>Location / Farm Town</label><input type="text" id="newProdLoc" required placeholder="e.g. Nakuru"></div>
                        </div>
                        <button type="submit" class="hero-btn" style="margin-top: 1rem; width: 100%;">Publish Listing</button>
                    </form>
                </div>
            </div>

            <!-- Buyer Dashboard View -->
            <div id="buyerDashboard" class="role-view" style="display: none;">
                <div class="stats-grid">
                    <div class="stat-box"><h3>2</h3><p>Active Orders</p></div>
                    <div class="stat-box"><h3>KSh 12,300</h3><p>Total Spent</p></div>
                </div>
                <div class="form-card" style="max-width: 100%;">
                    <h3 style="color: var(--primary-dark); margin-bottom: 1rem;">Your Recent Inquiries & Orders</h3>
                    <p style="color: var(--text-muted);">You have pending inquiries for Maize (Eldoret) and Cabbage (Nakuru). Farmers will contact you via phone.</p>
                </div>
            </div>

            <!-- Admin Dashboard View -->
            <div id="adminDashboard" class="role-view" style="display: none;">
                <div class="stats-grid">
                    <div class="stat-box"><h3>18</h3><p>Registered Farmers</p></div>
                    <div class="stat-box"><h3>42</h3><p>Active Buyers</p></div>
                    <div class="stat-box"><h3 style="color: #d32f2f;">3</h3><p>Pending Approvals</p></div>
                </div>
            </div>
        </section>

        <!-- 4. CONTACT SECTION -->
        <section id="contact" class="section">
            <div class="form-card">
                <h2>Contact Support</h2>
                <form onsubmit="handleContact(event)">
                    <div class="form-group"><label>Your Name</label><input type="text" required placeholder="John Doe"></div>
                    <div class="form-group"><label>Email Address</label><input type="email" required placeholder="john@example.com"></div>
                    <div class="form-group"><label>Message</label><textarea rows="4" required placeholder="How can our support team help you?"></textarea></div>
                    <button type="submit" class="btn">Send Message</button>
                </form>
            </div>
        </section>

        <!-- 5. LOGIN SECTION -->
        <section id="login" class="section">
            <div class="form-card">
                <h2>Sign In to AgriMarket</h2>
                <form onsubmit="handleLogin(event)">
                    <div class="form-group"><label>Email Address</label><input type="email" required placeholder="farmer@example.com"></div>
                    <div class="form-group"><label>Password</label><input type="password" required placeholder="••••••••"></div>
                    <button type="submit" class="btn" style="margin-top: 1rem;">Log In</button>
                </form>
            </div>
        </section>

    </div>

    <!-- ORDER MODAL -->
    <div class="modal" id="orderModal">
        <div class="modal-content">
            <button class="close-modal" onclick="closeModal()">×</button>
            <h3 id="modalTitle" style="color: var(--primary-dark); margin-bottom: 1rem;">Place Order Inquiry</h3>
            <p id="modalInfo" style="color: var(--text-muted); margin-bottom: 1rem; font-size: 0.95rem;"></p>
            <div class="form-group">
                <label>Quantity Required</label>
                <input type="number" id="orderQty" value="1" min="1" oninput="calculateTotal()">
            </div>
            <p style="font-weight: bold; margin-bottom: 1.5rem;" id="modalTotal">Total: KSh 0</p>
            <button class="btn" onclick="submitOrder()">Confirm Order Inquiry</button>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 AgriMarket System (TVET CDACC Project). All rights reserved.</p>
    </footer>

    <script>
        // Sample Produce Data
        let produceList = [
            { id: 1, name: 'Grade 1 Dry Maize', category: 'Grains', price: 3500, quantity: '90 Kg Bag', location: 'Eldoret', farmer: 'Kiprono Farms' },
            { id: 2, name: 'Fresh Red Tomatoes', category: 'Vegetables', price: 120, quantity: '1 Kg', location: 'Nakuru', farmer: 'Green Valley Growers' },
            { id: 3, name: 'Hass Avocados', category: 'Fruits', price: 30, quantity: 'Piece', location: 'Murang\'a', farmer: 'Central Highland Produce' },
            { id: 4, name: 'Sweet Irish Potatoes', category: 'Tubers', price: 2800, quantity: '110 Kg Bag', location: 'Nyandarua', farmer: 'Aberdare Farmers Coop' }
        ];

        let currentSelectedItem = null;

        // Navigation Switching
        function switchSection(sectionId) {
            document.querySelectorAll('.section').forEach(sec => sec.classList.remove('active'));
            document.querySelectorAll('nav a').forEach(a => a.classList.remove('active'));
            
            document.getElementById(sectionId).classList.add('active');
            const navLink = document.getElementById('nav-' + sectionId);
            if (navLink) navLink.classList.add('active');
            
            window.scrollTo({ top: 0, behavior: 'smooth' });
            if (document.getElementById('navMenu').classList.contains('show')) {
                toggleMenu();
            }
        }

        function toggleMenu() {
            document.getElementById('navMenu').classList.toggle('show');
        }

        // Render Catalog & Featured Grid
        function renderCatalog(items = produceList) {
            const catalogGrid = document.getElementById('catalogGrid');
            const featuredGrid = document.getElementById('featuredGrid');
            
            let html = '';
            items.forEach(item => {
                html += `
                    <div class="card">
                        <div>
                            <h3>${item.name}</h3>
                            <p><strong>Category:</strong> ${item.category}</p>
                            <p><strong>Grower:</strong> ${item.farmer}</p>
                            <p><strong>Location:</strong>  ${item.location}</p>
                            <p><strong>Stock:</strong> Available (${item.quantity})</p>
                            <div class="price">KSh ${item.price.toLocaleString()} <span style="font-size:0.8rem; color:#666;">/ ${item.quantity}</span></div>
                        </div>
                        <button class="btn" onclick="openOrderModal(${item.id})">Order / Inquire</button>
                    </div>
                `;
            });

            if (catalogGrid) catalogGrid.innerHTML = html;
            if (featuredGrid) featuredGrid.innerHTML = html; // Show all in featured for demo
        }

        // Search & Filter
        function filterCatalog() {
            const query = document.getElementById('searchInput').value.toLowerCase();
            const cat = document.getElementById('categoryFilter').value;
            
            const filtered = produceList.filter(item => {
                const matchQuery = item.name.toLowerCase().includes(query) || item.location.toLowerCase().includes(query);
                const matchCat = cat === '' || item.category === cat;
                return matchQuery && matchCat;
            });
            renderCatalog(filtered);
        }

        // Role Switcher in Dashboard
        function switchRole(role) {
            document.querySelectorAll('.role-view').forEach(view => view.style.display = 'none');
            document.getElementById(role + 'Dashboard').style.display = 'block';
        }

        // Add Produce Form Handler
        function addProduce(event) {
            event.preventDefault();
            const newProd = {
                id: produceList.length + 1,
                name: document.getElementById('newProdName').value,
                category: document.getElementById('newProdCat').value,
                price: parseFloat(document.getElementById('newProdPrice').value),
                quantity: document.getElementById('newProdQty').value,
                location: document.getElementById('newProdLoc').value,
                farmer: 'You (Registered Farmer)'
            };
            produceList.unshift(newProd);
            renderCatalog();
            alert('Produce successfully listed and published to the catalog!');
            event.target.reset();
            document.getElementById('statListings').innerText = produceList.filter(p => p.farmer.includes('You')).length + 3;
        }

        // Modal Functions
        function openOrderModal(id) {
            currentSelectedItem = produceList.find(p => p.id === id);
            document.getElementById('modalTitle').innerText = 'Inquire: ' + currentSelectedItem.name;
            document.getElementById('modalInfo').innerHTML = `Grower: <strong>${currentSelectedItem.farmer}</strong> (${currentSelectedItem.location})<br>Price: KSh ${currentSelectedItem.price.toLocaleString()} per ${currentSelectedItem.quantity}`;
            document.getElementById('orderQty').value = 1;
            calculateTotal();
            document.getElementById('orderModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('orderModal').style.display = 'none';
        }

        function calculateTotal() {
            const qty = parseInt(document.getElementById('orderQty').value) || 1;
            const total = qty * currentSelectedItem.price;
            document.getElementById('modalTotal').innerText = 'Total: KSh ' + total.toLocaleString();
        }

        function submitOrder() {
            alert(`Order inquiry successfully submitted for ${document.getElementById('orderQty').value} units of ${currentSelectedItem.name}! The farmer has been notified.`);
            closeModal();
        }

        function handleContact(e) {
            e.preventDefault();
            alert('Thank you for contacting AgriMarket support! We will respond shortly.');
            e.target.reset();
        }

        function handleLogin(e) {
            e.preventDefault();
            alert('Login successful! Redirecting to your dashboard.');
            switchSection('dashboard');
        }

        // Initialize on Load
        window.onload = function() {
            renderCatalog();
        };
    </script>
</body>
</html>