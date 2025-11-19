<?php
include '../includes/config.php';
include '../includes/auth.php';
checkAuth('sales');

$database = new Database();
$db = $database->getConnection();

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    
    $query = "UPDATE orders SET status = :status WHERE id = :order_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':status', $new_status);
    $stmt->bindParam(':order_id', $order_id);
    
    if ($stmt->execute()) {
        $success = "Order status updated successfully!";
    } else {
        $error = "Failed to update order status.";
    }
}

// Get all orders with user details
$query = "SELECT o.*, u.full_name, u.phone, 
                 COUNT(oi.id) as item_count,
                 SUM(oi.quantity) as total_items
          FROM orders o 
          JOIN users u ON o.user_id = u.id 
          LEFT JOIN order_items oi ON o.id = oi.order_id 
          GROUP BY o.id 
          ORDER BY o.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; ?>

<!-- WELCOME MESSAGE SECTION -->
<?php if (isset($_SESSION['welcome_message'])): ?>
    <div style="background: #fff3cd; padding: 1rem; border-radius: 4px; margin-bottom: 1rem; border-left: 4px solid #ffc107;">
        <strong>👋 Welcome, <?php echo $_SESSION['full_name']; ?>!</strong><br>
        <?php echo $_SESSION['welcome_message']; ?>
    </div>
    <?php unset($_SESSION['welcome_message']); ?>
<?php endif; ?>

<h1>Order Management</h1>

<?php if (isset($success)): ?>
    <div style="background: #e8f5e8; color: #2e7d32; padding: 1rem; margin-bottom: 1rem; border-radius: 4px;">
        <?php echo $success; ?>
    </div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div style="background: #ffebee; color: #c62828; padding: 1rem; margin-bottom: 1rem; border-radius: 4px;">
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<div class="orders-list">
    <?php foreach ($orders as $order): ?>
        <div class="order-card" style="background: white; padding: 1.5rem; margin-bottom: 1.5rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                <div>
                    <h3>Order #<?php echo $order['id']; ?></h3>
                    <p style="color: #666; margin: 0.5rem 0;">
                        Customer: <strong><?php echo htmlspecialchars($order['full_name']); ?></strong> 
                        (<?php echo htmlspecialchars($order['phone']); ?>)
                    </p>
                    <p style="color: #666; margin: 0.5rem 0;">
                        Placed: <?php echo date('F j, Y g:i A', strtotime($order['created_at'])); ?>
                    </p>
                </div>
                
                <div style="text-align: right;">
                    <div style="font-size: 1.2rem; font-weight: bold; color: #2c3e50;">
                        MK <?php echo number_format($order['total_amount'], 2); ?>
                    </div>
                    
                    <form method="POST" action="" style="margin-top: 0.5rem;">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <select name="status" onchange="this.form.submit()" style="padding: 0.5rem; border-radius: 4px; border: 1px solid #ddd;">
                            <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="preparing" <?php echo $order['status'] == 'preparing' ? 'selected' : ''; ?>>Preparing</option>
                            <option value="out_for_delivery" <?php echo $order['status'] == 'out_for_delivery' ? 'selected' : ''; ?>>Out for Delivery</option>
                            <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                        </select>
                        <input type="hidden" name="update_status" value="1">
                    </form>
                    
                    <div style="margin-top: 0.5rem; padding: 0.3rem 0.8rem; background: 
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
                        border-radius: 15px; font-size: 0.8rem; display: inline-block;">
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
                    <strong>Customer Notes:</strong> <?php echo htmlspecialchars($order['customer_notes']); ?>
                </div>
            <?php endif; ?>
            
            <!-- Order Items Details -->
            <div style="margin-top: 1rem;">
                <button type="button" onclick="toggleOrderItems(<?php echo $order['id']; ?>)" 
                        class="btn" style="padding: 0.5rem 1rem; font-size: 0.8rem;">
                    View Order Details
                </button>
                
                <div id="order-items-<?php echo $order['id']; ?>" style="display: none; margin-top: 1rem; padding: 1rem; background: #f8f9fa; border-radius: 4px;">
                    <?php
                    // Get order items
                    $items_query = "SELECT oi.*, m.name as meal_name 
                                   FROM order_items oi 
                                   JOIN meals m ON oi.meal_id = m.id 
                                   WHERE oi.order_id = :order_id";
                    $items_stmt = $db->prepare($items_query);
                    $items_stmt->bindParam(':order_id', $order['id']);
                    $items_stmt->execute();
                    $order_items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    
                    <h4>Order Items:</h4>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid #ddd;">
                                <th style="text-align: left; padding: 0.5rem;">Item</th>
                                <th style="text-align: right; padding: 0.5rem;">Price</th>
                                <th style="text-align: center; padding: 0.5rem;">Qty</th>
                                <th style="text-align: right; padding: 0.5rem;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order_items as $item): ?>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 0.5rem;"><?php echo htmlspecialchars($item['meal_name']); ?></td>
                                    <td style="text-align: right; padding: 0.5rem;">MK <?php echo number_format($item['unit_price'], 2); ?></td>
                                    <td style="text-align: center; padding: 0.5rem;"><?php echo $item['quantity']; ?></td>
                                    <td style="text-align: right; padding: 0.5rem;">MK <?php echo number_format($item['unit_price'] * $item['quantity'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
function toggleOrderItems(orderId) {
    var element = document.getElementById('order-items-' + orderId);
    if (element.style.display === 'none') {
        element.style.display = 'block';
    } else {
        element.style.display = 'none';
    }
}
</script>

<?php include '../includes/footer.php'; ?>