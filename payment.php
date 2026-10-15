<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = intval($_SESSION['user_id']);
$orderId = intval($_GET['order_id'] ?? 0);

$sql = "SELECT o.OrderID, o.TotalAmount, o.Status, r.RestaurantName, p.PaymentMethod, p.PaymentStatus, p.Amount
        FROM orders o
        JOIN restaurant r ON o.RestaurantID = r.RestaurantID
        JOIN payment p ON o.OrderID = p.OrderID
        WHERE o.OrderID = $orderId AND o.UserID = $userId AND p.PaymentMethod = 'Online Wallet'
        LIMIT 1";
$order = mysqli_fetch_assoc(mysqli_query($conn, $sql));

if (!$order) {
    header("Location: my_orders.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $walletProvider = trim($_POST['wallet_provider'] ?? '');
    $walletAccount = trim($_POST['wallet_account'] ?? '');
    $walletPin = trim($_POST['wallet_pin'] ?? '');

    if ($walletProvider !== '' && $walletAccount !== '' && strlen($walletPin) >= 4) {
        mysqli_query($conn, "UPDATE payment SET PaymentStatus='Completed', PaymentDate=NOW() WHERE OrderID=$orderId");
        mysqli_query($conn, "UPDATE orders SET Status='Confirmed' WHERE OrderID=$orderId");
        header("Location: payment_success.php?order_id=" . $orderId);
        exit();
    }

    $error = "Please fill all wallet payment fields. PIN must be at least 4 digits.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EatExpress | Online Wallet</title>
    <link rel="icon" type="image/svg+xml" href="assets/favicon.svg">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="app-dark-page payment-page">
<header class="topbar app-topbar">
    <a class="brand" href="dashboard.php"><img src="assets/logo.svg" alt="EatExpress logo"></a>
    <div class="nav-center">
        <a href="dashboard.php">Home</a>
        <a href="dashboard.php#restaurants">Restaurants</a>
        <a href="my_orders.php">My Orders</a>
    </div>
    <div class="nav-actions"><a class="login-btn" href="logout.php">Logout</a></div>
</header>

<main class="payment-wrap">
    <section class="wallet-card">
        <div class="wallet-card-left">
            <span class="eyebrow">Secure demo payment</span>
            <h1>Online Wallet</h1>
            <p>This is a simulated wallet payment screen for the project. Complete the form to mark your order as paid.</p>

            <div class="payment-summary-box">
                <div><span>Restaurant</span><strong><?php echo htmlspecialchars($order['RestaurantName']); ?></strong></div>
                <div><span>Order ID</span><strong>#<?php echo intval($order['OrderID']); ?></strong></div>
                <div><span>Total Amount</span><strong>₺<?php echo number_format((float)$order['Amount'], 2); ?></strong></div>
                <div><span>Status</span><strong><?php echo htmlspecialchars($order['PaymentStatus']); ?></strong></div>
            </div>
        </div>

        <form class="wallet-form" method="POST" onsubmit="return validateWalletPayment();">
            <h2>Pay with wallet</h2>
            <?php if (!empty($error)): ?><div class="error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

            <label for="wallet_provider">Wallet provider</label>
            <select name="wallet_provider" id="wallet_provider">
                <option value="">Choose wallet</option>
                <option value="Papara">Papara</option>
                <option value="Paycell">Paycell</option>
                <option value="Iyzico Wallet">Iyzico Wallet</option>
                <option value="EatExpress Wallet">EatExpress Wallet</option>
            </select>

            <label for="wallet_account">Wallet email or phone</label>
            <input type="text" name="wallet_account" id="wallet_account" placeholder="name@example.com or +90...">

            <label for="wallet_pin">Wallet PIN</label>
            <input type="password" name="wallet_pin" id="wallet_pin" placeholder="4 digit demo PIN" maxlength="6">

            <button class="pay-now-btn" type="submit">Pay ₺<?php echo number_format((float)$order['Amount'], 2); ?></button>
            <a class="cancel-payment" href="my_orders.php">Pay later</a>
        </form>
    </section>
</main>
<script src="js/validation.js"></script>
</body>
</html>
