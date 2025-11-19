<?php
include '../includes/config.php';
include '../includes/auth.php';
checkAuth('customer');

$order_id = $_GET['order_id'] ?? 0;

if (!$order_id) {
    header('Location: orders.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Get order details
$query = "SELECT o.*, u.full_name 
          FROM orders o 
          JOIN users u ON o.user_id = u.id 
          WHERE o.id = :order_id AND o.user_id = :user_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':order_id', $order_id);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: orders.php');
    exit();
}
?>

<?php include '../includes/header.php'; ?>

<div style="text-align: center; padding: 3rem;">
    <div style="background: #e8f5e8; padding: 2rem; border-radius: 8px; max-width: 600px; margin: 0 auto;">
        <h1 style="color: #2e7d32;">🎉 Order Placed Successfully!</h1>
        <p style="font-size: 1.2rem; margin: 1rem 0;">Thank you for your order, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</p>
        
        <div style="background: white; padding: 1.5rem; border-radius: 8px; margin: 2rem 0; text-align: left;">
            <h3>Order Details</h3>
            <p><strong>Order ID:</strong> #<?php echo $order['id']; ?></p>
            <p><strong>Total Amount:</strong> MK <?php echo number_format($order['total_amount'], 2); ?></p>
            <p><strong>Delivery Address:</strong> <?php echo htmlspecialchars($order['delivery_address']); ?></p>
            <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($order['contact_number']); ?></p>
            <p><strong>Status:</strong> <span style="color: #ff9800; font-weight: bold;"><?php echo ucfirst($order['status']); ?></span></p>
        </div>
        
        <p>We'll prepare your order and deliver it to you soon. You can track your order status in your orders page.</p>
        
        <div style="margin-top: 2rem;">
            <a href="orders.php" class="btn">View My Orders</a>
            <a href="menu.php" class="btn">Continue Shopping</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>