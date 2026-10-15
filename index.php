<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EatExpress | Online Food Ordering</title>
    <link rel="icon" type="image/svg+xml" href="assets/favicon.svg">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="home-page">
    <nav class="topbar">
        <a class="brand" href="index.php" aria-label="EatExpress home">
            <img src="assets/logo.svg" alt="EatExpress logo">
        </a>

        <div class="nav-center">
            <a href="index.php">Home</a>
            <a href="login.php">Restaurants</a>
            <a href="login.php">My Orders</a>
        </div>

        <div class="nav-actions">
            <a class="login-btn" href="login.php">Login</a>
            <a class="signup-btn" href="register.php">Sign Up</a>
        </div>
    </nav>

    <main class="homepage-wrap">
        <section class="hero-layout">
            <div class="hero-copy">
                <h1>Order Food Online Easily</h1>
                <p>Find your favorite restaurants and get your food delivered to your door.</p>

                <div class="hero-actions-main">
                    <a class="main-cta" href="login.php">Order Now</a>
                    <a class="secondary-cta" href="register.php">Create Account</a>
                </div>
            </div>

            <div class="hero-art">
                <img src="assets/delivery-hero.png" alt="Delivery rider on scooter">
            </div>
        </section>

        <section class="home-features" aria-label="EatExpress features">
            <article class="home-card">
                <div class="card-icon plate-icon"></div>
                <div>
                    <h3>Browse Restaurants</h3>
                    <p>Discover a variety of restaurants near you.</p>
                </div>
            </article>

            <article class="home-card">
                <div class="card-icon speed-icon">◔</div>
                <div>
                    <h3>Fast Delivery</h3>
                    <p>Get your food delivered fast and fresh.</p>
                </div>
            </article>

            <article class="home-card">
                <div class="card-icon support-icon">☎</div>
                <div>
                    <h3>Customer Support</h3>
                    <p>We're here to help 24/7 with any questions.</p>
                </div>
            </article>
        </section>
    </main>
</body>
</html>
