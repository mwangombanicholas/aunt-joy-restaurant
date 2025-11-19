<?php
include '../includes/config.php';
include '../includes/auth.php';
checkAuth('admin');

$database = new Database();
$db = $database->getConnection();

// Get statistics
$users_count = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$meals_count = $db->query("SELECT COUNT(*) FROM meals")->fetchColumn();
$orders_count = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$revenue = $db->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status = 'delivered'")->fetchColumn();
?>

<?php include 'header.php'; ?>

<!-- WELCOME MESSAGE SECTION -->
<?php if (isset($_SESSION['welcome_message'])): ?>
    <div style="background: #e8f5e8; padding: 1rem; border-radius: 4px; margin-bottom: 1rem; border-left: 4px solid #27ae60;">
        <strong>👋 Welcome, <?php echo $_SESSION['full_name']; ?>!</strong><br>
        <?php echo $_SESSION['welcome_message']; ?>
    </div>
    <?php unset($_SESSION['welcome_message']); ?>
<?php endif; ?>

<h1>Admin Dashboard</h1>

<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin: 2rem 0;">
    <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;">
        <h3 style="color: #3498db; margin-bottom: 1rem;">👥 Total Users</h3>
        <div style="font-size: 2.5rem; font-weight: bold; color: #2c3e50;"><?php echo $users_count; ?></div>
    </div>
    
    <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;">
        <h3 style="color: #e74c3c; margin-bottom: 1rem;">🍽️ Total Meals</h3>
        <div style="font-size: 2.5rem; font-weight: bold; color: #2c3e50;"><?php echo $meals_count; ?></div>
    </div>
    
    <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;">
        <h3 style="color: #f39c12; margin-bottom: 1rem;">📦 Total Orders</h3>
        <div style="font-size: 2.5rem; font-weight: bold; color: #2c3e50;"><?php echo $orders_count; ?></div>
    </div>
    
    <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;">
        <h3 style="color: #27ae60; margin-bottom: 1rem;">💰 Total Revenue</h3>
        <div style="font-size: 2.5rem; font-weight: bold; color: #2c3e50;">MK <?php echo number_format($revenue, 2); ?></div>
    </div>
</div>

<div class="admin-links" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-top: 3rem;">
    <a href="manage_meals.php" style="display: block; background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-decoration: none; color: inherit; text-align: center; transition: transform 0.3s;">
        <h3>🍽️ Manage Meals</h3>
        <p>Add, edit, or remove meals from the menu</p>
    </a>
    
    <a href="manage_users.php" style="display: block; background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-decoration: none; color: inherit; text-align: center; transition: transform 0.3s;">
        <h3>👥 Manage Users</h3>
        <p>View and manage user accounts</p>
    </a>
    
    <a href="../sales/orders.php" style="display: block; background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-decoration: none; color: inherit; text-align: center; transition: transform 0.3s;">
        <h3>📦 View Orders</h3>
        <p>Monitor and manage customer orders</p>
    </a>
    
    <a href="../manager/dashboard.php" style="display: block; background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-decoration: none; color: inherit; text-align: center; transition: transform 0.3s;">
        <h3>📊 Reports</h3>
        <p>View sales reports and analytics</p>
    </a>
</div>

<style>
.admin-links a:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}
</style>

<?php include '../includes/footer.php'; ?>