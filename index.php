<?php
include 'includes/config.php';
include 'includes/auth.php';

// Redirect users to their appropriate dashboard
if (isset($_SESSION['user_id'])) {
    switch ($_SESSION['user_role']) {
        case 'admin':
            header('Location: admin/index.php');
            exit;
        case 'sales':
            header('Location: sales/orders.php');
            exit;
        case 'manager':
            header('Location: manager/dashboard.php');
            exit;
        case 'customer':
            header('Location: customer/menu.php');
            exit;
    }
}
?>

<?php include 'includes/header.php'; ?>

<h1>Welcome to Aunt Joy's Restaurant</h1>

<div style="text-align: center; padding: 3rem;">
    <h2>Online Food Ordering System</h2>
    <p>Order your favorite meals from Aunt Joy's Restaurant</p>
    <div style="margin-top: 2rem;">
        <a href="login.php" class="btn" style="margin-right: 1rem;">Login</a>
        <a href="register.php" class="btn">Register</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>