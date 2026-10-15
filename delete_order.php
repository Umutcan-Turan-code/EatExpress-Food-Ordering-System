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
$orderId = intval($_GET['id'] ?? 0);
$userId = intval($_SESSION['user_id']);
mysqli_query($conn, "DELETE FROM orders WHERE OrderID=$orderId AND UserID=$userId AND Status='Pending'");
header("Location: my_orders.php");
exit();
?>
