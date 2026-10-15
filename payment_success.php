<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = intval($_SESSION['user_id']);
$orderId = intval($_GET['order_id'] ?? 0);
$order = mysqli_fetch_assoc(mysqli_query($conn, "SELECT o.OrderID, o.TotalAmount, r.RestaurantName FROM orders o JOIN restaurant r ON o.RestaurantID = r.RestaurantID WHERE o.OrderID=$orderId AND o.UserID=$userId LIMIT 1"));
if (!$order) {
    header("Location: my_orders.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EatExpress | Payment Successful</title>
    <link rel="icon" type="image/svg+xml" href="assets/favicon.svg">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="app-dark-page payment-page">
<main class="payment-success-wrap">
    <section class="success-card-big">
        <div class="success-icon">✓</div>
        <h1>Payment Successful</h1>
        <p>Your online wallet payment for <strong><?php echo htmlspecialchars($order['RestaurantName']); ?></strong> has been completed.</p>
        <div class="success-total">₺<?php echo number_format((float)$order['TotalAmount'], 2); ?></div>
        <a class="main-cta" href="my_orders.php">View My Orders</a>
    </section>
</main>
</body>
</html>
