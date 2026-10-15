<?php
session_start();
require_once "db.php";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $password = $_POST['password'];

    if ($firstName == "" || $lastName == "" || $email == "" || $phone == "" || $address == "" || $password == "") {
        $error = "All fields are required.";
    } else {
       $hashedPassword = hash('sha256', $password);

/* Email daha önce kayıtlı mı kontrol et */
$check = mysqli_prepare($conn, "SELECT UserID FROM users WHERE Email = ?");
mysqli_stmt_bind_param($check, "s", $email);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);

if (mysqli_stmt_num_rows($check) > 0) {
    $error = "This email is already registered. Please login or use another email.";
} else {
    $stmt = mysqli_prepare($conn, "INSERT INTO users (FirstName, LastName, Email, Phone, Address, Password) VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssssss", $firstName, $lastName, $email, $phone, $address, $hashedPassword);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: login.php?registered=1");
        exit();
    } else {
        $error = "Registration failed. Please try again.";
    }
}
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - EatExpress</title>
    <link rel="icon" type="image/svg+xml" href="assets/favicon.svg">
    <link rel="stylesheet" href="css/style.css">
    <script src="js/validation.js" defer></script>
</head>
<body class="auth-page">
    <nav class="topbar auth-topbar">
        <a class="brand" href="index.php"><img src="assets/logo.svg" alt="EatExpress logo"></a>
        <div class="nav-center">
            <a href="index.php">Home</a>
            <a href="login.php">Restaurants</a>
            <a href="login.php">My Orders</a>
        </div>
        <div class="nav-actions">
            <a class="login-btn" href="login.php">Login</a>
        </div>
    </nav>

    <main class="auth-layout register-layout">
        <section class="auth-copy">
            <span class="auth-badge">Join EatExpress</span>
            <h1>Create Account</h1>
            <p>Register as a customer and start ordering delicious meals from nearby restaurants.</p>
            <img src="assets/delivery-hero.png" alt="Food delivery illustration">
        </section>

        <section class="auth-card register-card">
            <h2>Create Customer Account</h2>
            <p class="auth-subtitle">Fill in your details to get started.</p>
            <?php if ($error): ?><p class="error"><?php echo $error; ?></p><?php endif; ?>
            <form method="POST" onsubmit="return validateRegisterForm();">
                <div class="form-row">
                    <div><label>First Name</label><input type="text" name="first_name" id="first_name" placeholder="First name"></div>
                    <div><label>Last Name</label><input type="text" name="last_name" id="last_name" placeholder="Last name"></div>
                </div>
                <label>Email</label>
                <input type="email" name="email" id="email" placeholder="example@mail.com">
                <label>Phone</label>
                <input type="text" name="phone" id="phone" placeholder="Phone number">
                <label>Delivery Address</label>
                <textarea name="address" id="address" placeholder="Delivery address"></textarea>
                <label>Password</label>
                <input type="password" name="password" id="password" placeholder="Create password">
                <button type="submit">Register</button>
            </form>
            <p class="auth-link">Already have an account? <a href="login.php">Login</a></p>
        </section>
    </main>
</body>
</html>
