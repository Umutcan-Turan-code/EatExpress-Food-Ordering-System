<?php
session_start();
require_once "db.php";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = hash('sha256', $_POST['password']);

    $stmt = mysqli_prepare($conn, "SELECT UserID, FirstName, Password FROM users WHERE Email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        if ($password === $row['Password']) {
            $_SESSION['user_id'] = $row['UserID'];
            $_SESSION['first_name'] = $row['FirstName'];
            header("Location: dashboard.php");
            exit();
        }
    }
    $error = "Invalid email or password.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EatExpress</title>
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
            <a class="signup-btn" href="register.php">Sign Up</a>
        </div>
    </nav>

    <main class="auth-layout">
        <section class="auth-copy">
            <span class="auth-badge">Fast & Fresh Delivery</span>
            <h1>Welcome Back</h1>
            <p>Login to browse restaurants, choose your favorite food and place your order in seconds.</p>
            <img src="assets/delivery-hero.png" alt="Food delivery illustration">
        </section>

        <section class="auth-card">
            <h2>Customer Login</h2>
            <p class="auth-subtitle">Continue your EatExpress order.</p>
            <?php if (isset($_GET['registered'])): ?><p class="success">Registration successful. Please login.</p><?php endif; ?>
            <?php if (isset($_GET['session_expired'])): ?><p class="error">Session expired. Please login again.</p><?php endif; ?>
            <?php if ($error): ?><p class="error"><?php echo $error; ?></p><?php endif; ?>
            <form method="POST" onsubmit="return validateLoginForm();">
                <label>Email</label>
                <input type="email" name="email" id="email" placeholder="example@mail.com">
                <label>Password</label>
                <input type="password" name="password" id="password" placeholder="Enter password">
                <button type="submit">Login</button>
            </form>
            <p class="auth-link">No account? <a href="register.php">Register now</a></p>
        </section>
    </main>
</body>
</html>
