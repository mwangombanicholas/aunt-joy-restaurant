<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aunt Joy's Restaurant</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h1 class="nav-logo">Aunt Joy's Restaurant</h1>
            <ul class="nav-menu">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="index.php">Home</a></li>
                    <?php if ($_SESSION['user_role'] === 'customer'): ?>
                        <li><a href="customer/menu.php">Menu</a></li>
                        <li><a href="customer/cart.php">Cart</a></li>
                        <li><a href="customer/orders.php">My Orders</a></li>
                    <?php elseif ($_SESSION['user_role'] === 'admin'): ?>
                        <li><a href="admin/index.php">Admin Dashboard</a></li>
                    <?php elseif ($_SESSION['user_role'] === 'sales'): ?>
                        <li><a href="sales/orders.php">Orders</a></li>
                    <?php elseif ($_SESSION['user_role'] === 'manager'): ?>
                        <li><a href="manager/dashboard.php">Reports Dashboard</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php">Logout (<?php echo $_SESSION['username']; ?>)</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    <div class="container">