<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>School Supplies Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar">
    <div class="nav-brand">School Shop</div>
    <button class="nav-toggle" onclick="document.getElementById('links').classList.toggle('show')">&#9776;</button>
    <div class="nav-links" id="links">
        <a href="index.php">Home</a>
        <a href="cart.php">Cart (<?php echo array_sum($_SESSION['cart'] ?? []); ?>)</a>
        <?php if(isset($_SESSION['user'])): ?>
            <a href="orders.php">My Orders</a>
            <?php if($_SESSION['user']['role'] === 'admin'): ?>
                <a href="products.php">Manage Products</a>
            <?php endif; ?>
            <a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['user']['username']); ?>)</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </div>
</nav>
<script>
    document.querySelectorAll('.nav-links a').forEach(a => {
        a.addEventListener('click', () => document.getElementById('links').classList.remove('show'));
    });
</script>
