<?php
include '../includes/config.php';
include '../includes/auth.php';
checkAuth('customer');

$database = new Database();
$db = $database->getConnection();

// Get user's orders
$query = "SELECT o.*, 
                 COUNT(oi.id) as item_count,
                 SUM(oi.quantity) as total_items
          FROM orders o 
          LEFT JOIN order_items oi ON o.id = oi.order_id 
          WHERE o.user_id = :user_id 
          GROUP BY o.id 
          ORDER BY o.created_at DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/header.php'; ?>

<h1>My Orders</h1>

<?php if (empty($orders)): ?>
    <div style="text-align: center; padding: 3rem;">
        <h2>No orders yet</h2>
        <p>Start ordering from our delicious menu!</p>
        <p><a href="menu.php" class="btn">Browse Menu</a></p>
    </div>
<?php else: ?>
    <div class="orders-list">
        <?php foreach ($orders as $order): ?>
            <div class="order-card" style="background: white; padding: 1.5rem; margin-bottom: 1.5rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 1rem;">
                    <div>
                        <h3>Order #<?php echo $order['id']; ?></h3>
                        <p style="color: #666; margin: 0.5rem 0;">
                            Placed on: <?php echo date('F j, Y g:i A', strtotime($order['created_at'])); ?>
                        </p>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 1.2rem; font-weight: bold; color: #2c3e50;">
                            MK <?php echo number_format($order['total_amount'], 2); ?>
                        </div>
                        <div style="padding: 0.5rem 1rem; background: 
                            <?php 
                                switch($order['status']) {
                                    case 'pending': echo '#fff3cd'; break;
                                    case 'preparing': echo '#d1ecf1'; break;
                                    case 'out_for_delivery': echo '#d4edda'; break;
                                    case 'delivered': echo '#e8f5e8'; break;
                                    default: echo '#f8f9fa';
                                }
                            ?>; 
                            color: 
                            <?php 
                                switch($order['status']) {
                                    case 'pending': echo '#856404'; break;
                                    case 'preparing': echo '#0c5460'; break;
                                    case 'out_for_delivery': echo '#155724'; break;
                                    case 'delivered': echo '#2e7d32'; break;
                                    default: echo '#6c757d';
                                }
                            ?>; 
                            border-radius: 20px; font-size: 0.9rem; margin-top: 0.5rem;">
                            <?php echo ucfirst(str_replace('_', ' ', $order['status'])); ?>
                        </div>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; font-size: 0.9rem;">
                    <div>
                        <strong>Delivery Address:</strong><br>
                        <?php echo htmlspecialchars($order['delivery_address']); ?>
                    </div>
                    <div>
                        <strong>Contact:</strong> <?php echo htmlspecialchars($order['contact_number']); ?><br>
                        <strong>Items:</strong> <?php echo $order['total_items']; ?> items
                    </div>
                </div>
                
                <?php if ($order['customer_notes']): ?>
                    <div style="margin-top: 1rem; padding: 1rem; background: #f8f9fa; border-radius: 4px;">
                        <strong>Your Notes:</strong> <?php echo htmlspecialchars($order['customer_notes']); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>