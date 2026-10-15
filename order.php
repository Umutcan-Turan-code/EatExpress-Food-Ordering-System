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

$restaurantId = intval($_GET['restaurant_id'] ?? 0);
$restaurant = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM restaurant WHERE RestaurantID = $restaurantId"));
if (!$restaurant) die("Restaurant not found.");

$menuItems = mysqli_query($conn, "SELECT f.*, c.CategoryName FROM fooditem f JOIN category c ON f.CategoryID = c.CategoryID WHERE f.RestaurantID = $restaurantId ORDER BY f.ItemName");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $items = $_POST['items'] ?? [];
    $quantities = $_POST['quantity'] ?? [];
    $address = trim($_POST['address']);
    $paymentMethod = $_POST['payment_method'];
    $total = 0;
    $selected = [];

    foreach ($items as $itemId) {
        $itemId = intval($itemId);
        $qty = max(1, intval($quantities[$itemId] ?? 1));
        $result = mysqli_query($conn, "SELECT Price FROM fooditem WHERE ItemID=$itemId AND RestaurantID=$restaurantId");
        if ($food = mysqli_fetch_assoc($result)) {
            $line = $food['Price'] * $qty;
            $total += $line;
            $selected[] = ['id' => $itemId, 'qty' => $qty, 'price' => $food['Price']];
        }
    }

    if ($total > 0 && $address != '') {
        $stmt = mysqli_prepare($conn, "INSERT INTO orders (UserID, RestaurantID, TotalAmount, Status, Address) VALUES (?, ?, ?, 'Pending', ?)");
        mysqli_stmt_bind_param($stmt, "iids", $userId, $restaurantId, $total, $address);
        mysqli_stmt_execute($stmt);
        $orderId = mysqli_insert_id($conn);

        foreach ($selected as $s) {
            $stmt2 = mysqli_prepare($conn, "INSERT INTO orderdetails (OrderID, ItemID, Quantity, Price) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt2, "iiid", $orderId, $s['id'], $s['qty'], $s['price']);
            mysqli_stmt_execute($stmt2);
        }

        $paymentStatus = ($paymentMethod === 'Online Wallet') ? 'Pending' : 'Completed';
        $stmt3 = mysqli_prepare($conn, "INSERT INTO payment (OrderID, PaymentMethod, PaymentStatus, Amount) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt3, "issd", $orderId, $paymentMethod, $paymentStatus, $total);
        mysqli_stmt_execute($stmt3);

        // Assign a delivery rider from the restaurant's delivery zone and create delivery record
        $zoneResult = mysqli_query($conn, "SELECT ZoneID FROM restaurant WHERE RestaurantID=$restaurantId");
        $zoneRow = mysqli_fetch_assoc($zoneResult);
        $zoneId = intval($zoneRow['ZoneID'] ?? 1);

        $riderResult = mysqli_query($conn, "SELECT rz.RiderID FROM riderzone rz WHERE rz.ZoneID=$zoneId ORDER BY RAND() LIMIT 1");
        $riderRow = mysqli_fetch_assoc($riderResult);
        $riderId = intval($riderRow['RiderID'] ?? 1);

        $stmt4 = mysqli_prepare($conn, "INSERT INTO delivery (OrderID, RiderID, ZoneID, EstimatedDeliveryTime, DeliveryStatus) VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL 35 MINUTE), 'Assigned')");
        mysqli_stmt_bind_param($stmt4, "iii", $orderId, $riderId, $zoneId);
        mysqli_stmt_execute($stmt4);

        if ($paymentMethod === 'Online Wallet') {
            header("Location: payment.php?order_id=" . $orderId);
            exit();
        }

        header("Location: my_orders.php?success=1");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>EatExpress | Order</title>
    <link rel="icon" type="image/svg+xml" href="assets/favicon.svg">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<nav>
    <h2>Eat<span>Express</span></h2>
    <div><a href="dashboard.php">Restaurants</a><a href="my_orders.php">My Orders</a><a href="logout.php">Logout</a></div>
</nav>
<main class="order-page-wrap">
    <section class="order-hero-panel">
        <div>
            <span class="eyebrow">EatExpress Menu</span>
            <h1><?php echo htmlspecialchars($restaurant['RestaurantName']); ?></h1>
            <p>Select your favorite meals, enter your delivery details, and place your order.</p>
        </div>
        <div class="restaurant-mini-card">
            <span>⭐ <?php echo htmlspecialchars($restaurant['Rating']); ?></span>
            <strong><?php echo htmlspecialchars($restaurant['Location']); ?></strong>
        </div>
    </section>

    <form class="order-form-modern" method="POST" onsubmit="return validateOrderForm();">
        <section class="menu-panel">
            <div class="panel-title-row">
                <div>
                    <h2>Choose your food</h2>
                    <p>Select dishes from the menu and customize the quantity.</p>
                </div>
            </div>
            <div id="foodItems" class="food-grid-modern" data-restaurant-id="<?php echo $restaurantId; ?>">
                <?php if (mysqli_num_rows($menuItems) > 0): ?>
                    <?php while ($item = mysqli_fetch_assoc($menuItems)): ?>
                        <label class="food-card-option">
                            <input type="checkbox" name="items[]" value="<?php echo intval($item['ItemID']); ?>">
                            <span class="food-card-content">
                                <span class="food-card-top">
                                    <strong><?php echo htmlspecialchars($item['ItemName']); ?></strong>
                                    <span class="price-pill">₺<?php echo number_format((float)$item['Price'], 2); ?></span>
                                </span>
                                <span class="category-chip"><?php echo htmlspecialchars($item['CategoryName']); ?></span>
                                <span class="prep-time">⏱ <?php echo intval($item['PreparationTime']); ?> min preparation</span>
                                <span class="qty-line">Quantity <input type="number" name="quantity[<?php echo intval($item['ItemID']); ?>]" value="1" min="1"></span>
                            </span>
                        </label>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="menu-loading">No food items found for this restaurant.</div>
                <?php endif; ?>
            </div>
        </section>

        <aside class="checkout-panel">
            <h2>Delivery details</h2>
            <label for="address">Address</label>
            <textarea name="address" id="address" placeholder="Write your full delivery address"></textarea>

            <label for="payment_method">Payment method</label>
            <select name="payment_method" id="payment_method">
                <option value="">Select payment method</option>
                <option value="Cash">Cash</option>
                <option value="Card">Card</option>
                <option value="Online Wallet">Online Wallet</option>
            </select>

            <button class="place-order-btn" type="submit">Place Order</button>
        </aside>
    </form>
</main>
<script src="js/order.js"></script>
</body>
</html>
