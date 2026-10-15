document.addEventListener("DOMContentLoaded", function () {
    const box = document.getElementById("foodItems");
    const restaurantId = box.dataset.restaurantId;

    fetch("ajax/get_food_items.php?restaurant_id=" + restaurantId)
        .then(response => response.text())
        .then(data => box.innerHTML = data)
        .catch(() => box.innerHTML = '<div class="menu-loading">Menu could not be loaded.</div>');
});

function validateOrderForm() {
    const checked = document.querySelectorAll('input[name="items[]"]:checked');
    const address = document.getElementById("address").value.trim();
    const payment = document.getElementById("payment_method").value;

    if (checked.length === 0) {
        alert("Please select at least one food item.");
        return false;
    }
    if (!address) {
        alert("Please enter delivery address.");
        return false;
    }
    if (!payment) {
        alert("Please select payment method.");
        return false;
    }
    return true;
}
