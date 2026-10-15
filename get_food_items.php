<?php
require_once "../db.php";
$restaurantId = intval($_GET['restaurant_id'] ?? 0);
$result = mysqli_query($conn, "SELECT f.*, c.CategoryName FROM fooditem f JOIN category c ON f.CategoryID = c.CategoryID WHERE f.RestaurantID = $restaurantId ORDER BY f.ItemName");

while ($item = mysqli_fetch_assoc($result)) {
    $id = intval($item['ItemID']);
    $name = htmlspecialchars($item['ItemName']);
    $category = htmlspecialchars($item['CategoryName']);
    $price = number_format((float)$item['Price'], 2);
    echo '<label class="food-card-option">';
    echo '<input type="checkbox" name="items[]" value="' . $id . '">';
    echo '<span class="food-card-content">';
    echo '<span class="food-card-top"><strong>' . $name . '</strong><span class="price-pill">₺' . $price . '</span></span>';
    echo '<span class="category-chip">' . $category . '</span>';
    echo '<span class="qty-line">Quantity <input type="number" name="quantity[' . $id . ']" value="1" min="1"></span>';
    echo '</span>';
    echo '</label>';
}
?>
