<?php
session_start();
require_once "db.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$restaurants = mysqli_query($conn, "SELECT * FROM restaurant ORDER BY Rating DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EatExpress | Restaurants</title>
    <link rel="icon" type="image/svg+xml" href="assets/favicon.svg">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="app-dark-page">
<header class="topbar app-topbar">
    <a class="brand" href="dashboard.php"><img src="assets/logo.svg" alt="EatExpress logo"></a>
    <div class="nav-center">
        <a href="dashboard.php">Home</a>
        <a href="dashboard.php#restaurants">Restaurants</a>
        <a href="my_orders.php">My Orders</a>
    </div>
    <div class="nav-actions">
        <a class="login-btn" href="logout.php">Logout</a>
    </div>
</header>

<main class="homepage-wrap dashboard-wrap">
    <section class="hero-layout dashboard-hero">
        <div class="hero-copy">
            <p class="hello-text">Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?></p>
            <h1>Order Food Online Easily</h1>
            <p>Find your favorite restaurants and get your food delivered to your door.</p>
            <div class="hero-actions-main">
                <a class="main-cta" href="#restaurants">Browse Restaurants</a>
                <a class="secondary-cta" href="my_orders.php">My Orders</a>
            </div>
        </div>
        <div class="hero-art">
            <img src="assets/delivery-hero.png" alt="Food delivery scooter illustration">
        </div>
    </section>

    <section class="home-features">
        <div class="home-card"><div class="card-icon plate-icon"></div><div><h3>Browse Restaurants</h3><p>Discover a variety of restaurants near you.</p></div></div>
        <div class="home-card"><div class="card-icon speed-icon">•</div><div><h3>Fast Delivery</h3><p>Get your food delivered fast and fresh.</p></div></div>
        <div class="home-card"><div class="card-icon support-icon">☏</div><div><h3>Customer Support</h3><p>We are here to help with your orders.</p></div></div>
    </section>

    <section id="restaurants" class="restaurant-section">
        <div class="section-head">
            <h2>Restaurants</h2>
            <p>Select a restaurant to order food</p>
        </div>
        <div class="restaurant-grid">
            <?php
            $restaurantMeta = [
                'Kebab House' => ['Turkish Grill', '🥙', '25-35 min'],
                'Burger House' => ['Fast Food', '🍔', '15-25 min'],
                'Pizza Palace' => ['Pizza', '🍕', '25-35 min'],
                'Sushi House' => ['Asian', '🍣', '30-40 min'],
                'Pasta Point' => ['Italian', '🍝', '20-30 min'],
                'Dessert Spot' => ['Dessert', '🍰', '15-25 min'],
                'Doner Express' => ['Turkish Doner', '🌯', '15-25 min'],
                'Taco Fiesta' => ['Mexican', '🌮', '25-35 min'],
                'Healthy Bowl' => ['Healthy Food', '🥗', '20-30 min'],
                'Coffee Corner' => ['Cafe & Drinks', '☕', '10-20 min']
            ];
            while ($r = mysqli_fetch_assoc($restaurants)):
                $name = $r['RestaurantName'];
                $meta = $restaurantMeta[$name] ?? ['Restaurant', '🍽️', '20-30 min'];
            ?>
                <article class="restaurant-card restaurant-card-modern">
                    <div class="restaurant-icon"><?php echo $meta[1]; ?></div>
                    <div class="restaurant-card-top">
                        <span class="cuisine-badge"><?php echo htmlspecialchars($meta[0]); ?></span>
                        <span class="rating-badge">★ <?php echo htmlspecialchars($r['Rating']); ?></span>
                    </div>
                    <h3><?php echo htmlspecialchars($name); ?></h3>
                    <p class="restaurant-location">📍 <?php echo htmlspecialchars($r['Location']); ?></p>
                    <div class="restaurant-info-row">
                        <span>⏱ <?php echo $meta[2]; ?></span>
                        <span>Free delivery</span>
                    </div>
                    <a class="btn order-btn" href="order.php?restaurant_id=<?php echo $r['RestaurantID']; ?>">Order Now</a>
                </article>
            <?php endwhile; ?>
        </div>
    </section>
</main>
</body>
</html>
