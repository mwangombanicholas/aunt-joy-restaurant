<?php
include '../includes/config.php';
include '../includes/auth.php';
checkAuth('customer');

$database = new Database();
$db = $database->getConnection();

$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    header('Location: cart.php');
    exit();
}

// Get user details
$query = "SELECT * FROM users WHERE id = :user_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Calculate total
$total = 0;
$placeholders = str_repeat('?,', count($cart) - 1) . '?';
$query = "SELECT * FROM meals WHERE id IN ($placeholders)";
$stmt = $db->prepare($query);
$stmt->execute(array_keys($cart));
$meals = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($meals as $meal) {
    $quantity = $cart[$meal['id']];
    $total += $meal['price'] * $quantity;
}

// Handle order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $delivery_address = $_POST['delivery_address'];
    $contact_number = $_POST['contact_number'];
    $customer_notes = $_POST['customer_notes'];
    
    try {
        $db->beginTransaction();
        
        // Create order
        $query = "INSERT INTO orders (user_id, total_amount, delivery_address, contact_number, customer_notes) 
                 VALUES (:user_id, :total_amount, :delivery_address, :contact_number, :customer_notes)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->bindParam(':total_amount', $total);
        $stmt->bindParam(':delivery_address', $delivery_address);
        $stmt->bindParam(':contact_number', $contact_number);
        $stmt->bindParam(':customer_notes', $customer_notes);
        $stmt->execute();
        
        $order_id = $db->lastInsertId();
        
        // Add order items
        foreach ($meals as $meal) {
            $quantity = $cart[$meal['id']];
            $query = "INSERT INTO order_items (order_id, meal_id, quantity, unit_price) 
                     VALUES (:order_id, :meal_id, :quantity, :unit_price)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':order_id', $order_id);
            $stmt->bindParam(':meal_id', $meal['id']);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':unit_price', $meal['price']);
            $stmt->execute();
        }
        
        $db->commit();
        
        // Clear cart
        $_SESSION['cart'] = [];
        
        header('Location: order_success.php?order_id=' . $order_id);
        exit();
        
    } catch (Exception $e) {
        $db->rollBack();
        $error = "Order failed: " . $e->getMessage();
    }
}
?>

<?php include '../includes/header.php'; ?>

<h1>Checkout</h1>

<?php if (isset($error)): ?>
    <div style="background: #ffebee; color: #c62828; padding: 1rem; margin-bottom: 1rem; border-radius: 4px;">
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
    <div>
        <h2>Order Summary</h2>
        <div style="background: white; padding: 1.5rem; border-radius: 8px;">
            <?php foreach ($meals as $meal): ?>
                <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #eee;">
                    <div>
                        <strong><?php echo htmlspecialchars($meal['name']); ?></strong>
                        <div style="color: #666;">Qty: <?php echo $cart[$meal['id']]; ?></div>
                    </div>
                    <div>MK <?php echo number_format($meal['price'] * $cart[$meal['id']], 2); ?></div>
                </div>
            <?php endforeach; ?>
            <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 1.2rem; margin-top: 1rem;">
                <div>Total:</div>
                <div>MK <?php echo number_format($total, 2); ?></div>
            </div>
        </div>
    </div>
    
    <div>
        <h2>Delivery Information</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="delivery_address">Delivery Address:</label>
                <textarea id="delivery_address" name="delivery_address" rows="3" required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="contact_number">Contact Number:</label>
                <input type="tel" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="customer_notes">Special Instructions (Optional):</label>
                <textarea id="customer_notes" name="customer_notes" rows="3" placeholder="Any special delivery instructions..."></textarea>
            </div>
            
            <button type="submit" class="btn btn-success" style="width: 100%; padding: 1rem; font-size: 1.2rem;">
                Place Order - MK <?php echo number_format($total, 2); ?>
            </button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>