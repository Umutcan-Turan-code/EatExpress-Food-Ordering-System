<?php
session_start();
require_once "db.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = intval($_SESSION['user_id']);
$userCheck = mysqli_query($conn, "SELECT UserID FROM users WHERE UserID = $userId LIMIT 1");
if (!$userCheck || mysqli_num_rows($userCheck) === 0) {
    session_destroy();
    header("Location: login.php?session_expired=1");
    exit();
}
$userId = intval($_SESSION['user_id']);
$sql = "SELECT o.*, r.RestaurantName,
               p.PaymentMethod, p.PaymentStatus, p.PaymentDate,
               d.DeliveryStatus, d.EstimatedDeliveryTime,
               dr.FirstName AS RiderFirstName, dr.LastName AS RiderLastName,
               dr.VehicleType,
               z.ZoneName
        FROM orders o
        JOIN restaurant r ON o.RestaurantID = r.RestaurantID
        JOIN payment p ON o.OrderID = p.OrderID
        LEFT JOIN delivery d ON o.OrderID = d.OrderID
        LEFT JOIN deliveryrider dr ON d.RiderID = dr.RiderID
        LEFT JOIN deliveryzone z ON d.ZoneID = z.ZoneID
        WHERE o.UserID = $userId
        ORDER BY o.OrderDate DESC";
$orders = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EatExpress | My Orders</title>
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
    <section class="section-head orders-head-modern">
        <h1>My Orders</h1>
        <p>Track your orders, payments and delivery rider information.</p>
        <?php if (isset($_GET['success'])): ?><div class="success order-success">Order created successfully.</div><?php endif; ?>
    </section>

    <section class="orders-grid-modern">
        <?php if (mysqli_num_rows($orders) > 0): ?>
            <?php while ($o = mysqli_fetch_assoc($orders)): ?>
                <article class="order-card-modern">
                    <div class="order-card-header">
                        <div>
                            <span class="order-id">Order #<?php echo intval($o['OrderID']); ?></span>
                            <h3><?php echo htmlspecialchars($o['RestaurantName']); ?></h3>
                        </div>
                        <span class="status-pill"><?php echo htmlspecialchars($o['Status']); ?></span>
                    </div>

                    <div class="order-info-grid">
                        <div><span>Date</span><strong><?php echo htmlspecialchars($o['OrderDate']); ?></strong></div>
                        <div><span>Total</span><strong>₺<?php echo number_format((float)$o['TotalAmount'], 2); ?></strong></div>
                        <div><span>Payment</span><strong><?php echo htmlspecialchars($o['PaymentMethod']); ?> / <?php echo htmlspecialchars($o['PaymentStatus']); ?></strong></div>
                        <div><span>Delivery</span><strong><?php echo htmlspecialchars($o['DeliveryStatus'] ?? 'Not assigned'); ?></strong></div>
                    </div>

                    <div class="delivery-box-modern">
                        <h4>Delivery Details</h4>
                        <p><strong>Rider:</strong> <?php echo htmlspecialchars(trim(($o['RiderFirstName'] ?? '') . ' ' . ($o['RiderLastName'] ?? '')) ?: 'Not assigned'); ?></p>
                        <p><strong>Vehicle:</strong> <?php echo htmlspecialchars($o['VehicleType'] ?? '-'); ?></p>
                        <p><strong>Zone:</strong> <?php echo htmlspecialchars($o['ZoneName'] ?? '-'); ?></p>
                        <p><strong>Estimated Time:</strong> <?php echo htmlspecialchars($o['EstimatedDeliveryTime'] ?? '-'); ?></p>
                    </div>

                    <div class="order-card-actions">
                        <a class="danger" href="delete_order.php?id=<?php echo intval($o['OrderID']); ?>" onclick="return confirm('Delete this order?')">Delete Pending Order</a>
                    </div>
                </article>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state-modern">
                <h2>No orders yet</h2>
                <p>Choose a restaurant and place your first order.</p>
                <a class="main-cta" href="dashboard.php#restaurants">Browse Restaurants</a>
            </div>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
